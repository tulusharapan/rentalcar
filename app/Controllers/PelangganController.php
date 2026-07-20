<?php

namespace App\Controllers;

use App\Models\PelangganModel;

class PelangganController extends BaseController
{
    private PelangganModel $pelangganModel;
    private string $uploadPath;

    public function __construct()
    {
        $this->pelangganModel = new PelangganModel();
        $this->uploadPath     = FCPATH . 'uploads/pelanggan';
    }

    public function index()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('admin/pelanggan/index', [
            'pelanggan' => $this->pelangganModel->orderBy('id', 'DESC')->findAll(),
            'userName'  => session()->get('userName'),
            'userEmail' => session()->get('userEmail'),
            'userRole'  => session()->get('userRole'),
        ]);
    }

    public function create()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('admin/pelanggan/create', [
            'kodePelanggan' => $this->generateKodePelanggan(),
            'userName'      => session()->get('userName'),
            'userEmail'     => session()->get('userEmail'),
            'userRole'      => session()->get('userRole'),
        ]);
    }

    public function detail(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $pelanggan = $this->pelangganModel->find($id);

        if (! $pelanggan) {
            return redirect()->to('/admin/pelanggan')->with('error', 'Data pelanggan tidak ditemukan.');
        }

        return view('admin/pelanggan/detail', [
            'pelanggan' => $pelanggan,
            'userName'  => session()->get('userName'),
            'userEmail' => session()->get('userEmail'),
            'userRole'  => session()->get('userRole'),
        ]);
    }

    public function store()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $rules = $this->validationRules();
        $rules['nik'] = 'required|max_length[30]|is_unique[pelanggan.nik]';

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->pelangganModel->insert([
            'kode_pelanggan'     => $this->generateKodePelanggan(),
            'nama_lengkap'       => $this->request->getPost('nama_lengkap'),
            'nik'                => $this->request->getPost('nik'),
            'alamat'             => $this->request->getPost('alamat'),
            'no_hp'              => $this->request->getPost('no_hp'),
            'tanggal_terdaftar'  => $this->request->getPost('tanggal_terdaftar'),
            'foto_ktp'           => $this->uploadImage('foto_ktp'),
            'foto_sim'           => $this->uploadImage('foto_sim'),
            'foto'               => $this->uploadImage('foto'),
            'status'             => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/pelanggan')->with('success', 'Data pelanggan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $pelanggan = $this->pelangganModel->find($id);

        if (! $pelanggan) {
            return redirect()->to('/admin/pelanggan')->with('error', 'Data pelanggan tidak ditemukan.');
        }

        return view('admin/pelanggan/edit', [
            'pelanggan' => $pelanggan,
            'userName'  => session()->get('userName'),
            'userEmail' => session()->get('userEmail'),
            'userRole'  => session()->get('userRole'),
        ]);
    }

    public function update(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $pelanggan = $this->pelangganModel->find($id);

        if (! $pelanggan) {
            return redirect()->to('/admin/pelanggan')->with('error', 'Data pelanggan tidak ditemukan.');
        }

        $rules = $this->validationRules();
        $rules['nik'] = 'required|max_length[30]|is_unique[pelanggan.nik,id,' . $id . ']';

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nama_lengkap'      => $this->request->getPost('nama_lengkap'),
            'nik'               => $this->request->getPost('nik'),
            'alamat'            => $this->request->getPost('alamat'),
            'no_hp'             => $this->request->getPost('no_hp'),
            'tanggal_terdaftar' => $this->request->getPost('tanggal_terdaftar'),
            'status'            => $this->request->getPost('status'),
        ];

        foreach (['foto_ktp', 'foto_sim', 'foto'] as $field) {
            $fileName = $this->uploadImage($field);

            if ($fileName !== null) {
                $data[$field] = $fileName;
                $this->deleteImage($pelanggan[$field] ?? null);
            }
        }

        $this->pelangganModel->update($id, $data);

        return redirect()->to('/admin/pelanggan')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $pelanggan = $this->pelangganModel->find($id);

        if (! $pelanggan) {
            return redirect()->to('/admin/pelanggan')->with('error', 'Data pelanggan tidak ditemukan.');
        }

        foreach (['foto_ktp', 'foto_sim', 'foto'] as $field) {
            $this->deleteImage($pelanggan[$field] ?? null);
        }

        $this->pelangganModel->delete($id);

        return redirect()->to('/admin/pelanggan')->with('success', 'Data pelanggan berhasil dihapus.');
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('isLoggedIn');
    }

    private function validationRules(): array
    {
        $imageRule = 'permit_empty|is_image[{field}]|max_size[{field},2048]|ext_in[{field},jpg,jpeg,png,webp]';

        return [
            'nama_lengkap'      => 'required|min_length[3]|max_length[150]',
            'alamat'            => 'required',
            'no_hp'             => 'required|max_length[30]',
            'tanggal_terdaftar' => 'required|valid_date[Y-m-d]',
            'status'            => 'required|in_list[aktif,non-aktif]',
            'foto_ktp'          => str_replace('{field}', 'foto_ktp', $imageRule),
            'foto_sim'          => str_replace('{field}', 'foto_sim', $imageRule),
            'foto'              => str_replace('{field}', 'foto', $imageRule),
        ];
    }

    private function generateKodePelanggan(): string
    {
        $lastPelanggan = $this->pelangganModel
            ->select('kode_pelanggan')
            ->like('kode_pelanggan', 'PLG', 'after')
            ->orderBy('id', 'DESC')
            ->first();

        $lastNumber = 0;

        if ($lastPelanggan && preg_match('/^PLG(\d+)$/', (string) $lastPelanggan['kode_pelanggan'], $matches)) {
            $lastNumber = (int) $matches[1];
        }

        return 'PLG' . str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
    }

    private function uploadImage(string $field): ?string
    {
        $image = $this->request->getFile($field);

        if (! $image || ! $image->isValid() || $image->hasMoved()) {
            return null;
        }

        if (! is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0775, true);
        }

        $imageName = $image->getRandomName();
        $image->move($this->uploadPath, $imageName);

        return $imageName;
    }

    private function deleteImage(?string $imageName): void
    {
        if (! $imageName) {
            return;
        }

        $imagePath = $this->uploadPath . DIRECTORY_SEPARATOR . $imageName;

        if (is_file($imagePath)) {
            unlink($imagePath);
        }
    }
}
