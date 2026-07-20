<?php

namespace App\Controllers;

use App\Models\KategoriKeuanganModel;
use App\Models\TransaksiKeuanganModel;

class KategoriKeuanganController extends BaseController
{
    private KategoriKeuanganModel $kategoriKeuanganModel;
    private TransaksiKeuanganModel $transaksiKeuanganModel;
    private array $lockedCategoryIds = [1, 2];

    public function __construct()
    {
        $this->kategoriKeuanganModel = new KategoriKeuanganModel();
        $this->transaksiKeuanganModel = new TransaksiKeuanganModel();
    }

    public function index()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard', 'Akses ditolak. Kategori keuangan hanya dapat diakses oleh administrator.')) {
            return $redirect;
        }

        return view('admin/kategori_keuangan/index', [
            'kategoriKeuangan' => $this->kategoriKeuanganModel->orderBy('id', 'DESC')->findAll(),
            'userName'         => session()->get('userName'),
            'userEmail'        => session()->get('userEmail'),
            'userRole'         => session()->get('userRole'),
        ]);
    }

    public function create()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard', 'Akses ditolak. Kategori keuangan hanya dapat diakses oleh administrator.')) {
            return $redirect;
        }

        return view('admin/kategori_keuangan/create', $this->viewData());
    }

    public function store()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard', 'Akses ditolak. Kategori keuangan hanya dapat diakses oleh administrator.')) {
            return $redirect;
        }

        if (! $this->validate([
            'kategori' => 'required|min_length[2]|max_length[150]|is_unique[kategori_keuangan.kategori]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kategoriKeuanganModel->insert([
            'kategori' => trim((string) $this->request->getPost('kategori')),
        ]);

        return redirect()->to('/admin/kategori-keuangan')->with('success', 'Kategori keuangan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard', 'Akses ditolak. Kategori keuangan hanya dapat diakses oleh administrator.')) {
            return $redirect;
        }

        $kategori = $this->kategoriKeuanganModel->find($id);

        if (! $kategori) {
            return redirect()->to('/admin/kategori-keuangan')->with('error', 'Kategori keuangan tidak ditemukan.');
        }

        if ($this->isLockedCategory($id)) {
            return redirect()->to('/admin/kategori-keuangan')->with('error', 'Kategori default sistem tidak bisa diedit.');
        }

        return view('admin/kategori_keuangan/edit', array_merge($this->viewData(), [
            'kategori' => $kategori,
        ]));
    }

    public function update(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard', 'Akses ditolak. Kategori keuangan hanya dapat diakses oleh administrator.')) {
            return $redirect;
        }

        $kategori = $this->kategoriKeuanganModel->find($id);

        if (! $kategori) {
            return redirect()->to('/admin/kategori-keuangan')->with('error', 'Kategori keuangan tidak ditemukan.');
        }

        if ($this->isLockedCategory($id)) {
            return redirect()->to('/admin/kategori-keuangan')->with('error', 'Kategori default sistem tidak bisa diedit.');
        }

        if (! $this->validate([
            'kategori' => 'required|min_length[2]|max_length[150]|is_unique[kategori_keuangan.kategori,id,' . $id . ']',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kategoriKeuanganModel->update($id, [
            'kategori' => trim((string) $this->request->getPost('kategori')),
        ]);

        return redirect()->to('/admin/kategori-keuangan')->with('success', 'Kategori keuangan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard', 'Akses ditolak. Kategori keuangan hanya dapat diakses oleh administrator.')) {
            return $redirect;
        }

        if (! $this->kategoriKeuanganModel->find($id)) {
            return redirect()->to('/admin/kategori-keuangan')->with('error', 'Kategori keuangan tidak ditemukan.');
        }

        if ($this->isLockedCategory($id)) {
            return redirect()->to('/admin/kategori-keuangan')->with('error', 'Kategori default sistem tidak bisa dihapus.');
        }

        $fallbackCategory = $this->kategoriKeuanganModel->find(1);

        if (! $fallbackCategory) {
            return redirect()->to('/admin/kategori-keuangan')->with('error', 'Kategori Lainnya tidak ditemukan. Kategori tidak bisa dihapus.');
        }

        $movedTransactions = $this->transaksiKeuanganModel
            ->where('kategori_keuangan_id', $id)
            ->countAllResults();

        $db = db_connect();
        $db->transStart();

        $this->transaksiKeuanganModel
            ->where('kategori_keuangan_id', $id)
            ->set([
                'kategori_keuangan_id' => 1,
                'kategori'             => $fallbackCategory['kategori'],
            ])
            ->update();

        $this->kategoriKeuanganModel->delete($id);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to('/admin/kategori-keuangan')->with('error', 'Kategori gagal dihapus.');
        }

        $message = 'Kategori keuangan berhasil dihapus.';

        if ($movedTransactions > 0) {
            $message .= ' ' . $movedTransactions . ' transaksi dipindahkan ke kategori "' . $fallbackCategory['kategori'] . '".';
        }

        return redirect()->to('/admin/kategori-keuangan')->with('success', $message);
    }

    private function viewData(): array
    {
        return [
            'userName'  => session()->get('userName'),
            'userEmail' => session()->get('userEmail'),
            'userRole'  => session()->get('userRole'),
        ];
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('isLoggedIn');
    }

    private function isLockedCategory(int $id): bool
    {
        return in_array($id, $this->lockedCategoryIds, true);
    }

}
