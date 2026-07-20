<?php

namespace App\Controllers;

use App\Models\LayananTambahanModel;
use App\Models\TransaksiSewaLayananModel;

class LayananTambahanController extends BaseController
{
    private LayananTambahanModel $layananTambahanModel;
    private TransaksiSewaLayananModel $transaksiSewaLayananModel;

    public function __construct()
    {
        $this->layananTambahanModel = new LayananTambahanModel();
        $this->transaksiSewaLayananModel = new TransaksiSewaLayananModel();
    }

    public function index()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $layananTambahan = $this->layananTambahanModel->orderBy('id', 'DESC')->findAll();

        foreach ($layananTambahan as &$layanan) {
            $layanan['is_locked'] = $this->isLocked((int) $layanan['id']);
        }

        return view('admin/layanan_tambahan/index', [
            'layananTambahan' => $layananTambahan,
            'userName'        => session()->get('userName'),
            'userEmail'       => session()->get('userEmail'),
            'userRole'        => session()->get('userRole'),
        ]);
    }

    public function create()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('admin/layanan_tambahan/create', $this->viewData());
    }

    public function store()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $rules = $this->validationRules();
        $rules['nama_layanan'] = 'required|min_length[2]|max_length[150]|is_unique[layanan_tambahan.nama_layanan]';

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->layananTambahanModel->insert([
            'nama_layanan'  => $this->request->getPost('nama_layanan'),
            'harga_layanan' => $this->request->getPost('harga_layanan'),
            'icon'          => $this->normalizeIcon((string) $this->request->getPost('icon')),
        ]);

        return redirect()->to('/admin/layanan-tambahan')->with('success', 'Data layanan tambahan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $layanan = $this->layananTambahanModel->find($id);

        if (! $layanan) {
            return redirect()->to('/admin/layanan-tambahan')->with('error', 'Data layanan tambahan tidak ditemukan.');
        }

        if ($this->isLocked($id)) {
            return redirect()->to('/admin/layanan-tambahan')->with('error', 'Layanan tambahan yang sudah dipakai transaksi tidak bisa diedit demi menjaga riwayat invoice.');
        }

        return view('admin/layanan_tambahan/edit', array_merge($this->viewData(), [
            'layanan' => $layanan,
        ]));
    }

    public function update(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $layanan = $this->layananTambahanModel->find($id);

        if (! $layanan) {
            return redirect()->to('/admin/layanan-tambahan')->with('error', 'Data layanan tambahan tidak ditemukan.');
        }

        if ($this->isLocked($id)) {
            return redirect()->to('/admin/layanan-tambahan')->with('error', 'Layanan tambahan yang sudah dipakai transaksi tidak bisa diedit demi menjaga riwayat invoice.');
        }

        $rules = $this->validationRules();
        $rules['nama_layanan'] = 'required|min_length[2]|max_length[150]|is_unique[layanan_tambahan.nama_layanan,id,' . $id . ']';

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->layananTambahanModel->update($id, [
            'nama_layanan'  => $this->request->getPost('nama_layanan'),
            'harga_layanan' => $this->request->getPost('harga_layanan'),
            'icon'          => $this->normalizeIcon((string) $this->request->getPost('icon')),
        ]);

        return redirect()->to('/admin/layanan-tambahan')->with('success', 'Data layanan tambahan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $layanan = $this->layananTambahanModel->find($id);

        if (! $layanan) {
            return redirect()->to('/admin/layanan-tambahan')->with('error', 'Data layanan tambahan tidak ditemukan.');
        }

        if ($this->isLocked($id)) {
            return redirect()->to('/admin/layanan-tambahan')->with('error', 'Layanan tambahan yang sudah dipakai transaksi tidak bisa dihapus demi menjaga riwayat invoice.');
        }

        $this->layananTambahanModel->delete($id);

        return redirect()->to('/admin/layanan-tambahan')->with('success', 'Data layanan tambahan berhasil dihapus.');
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('isLoggedIn');
    }

    private function viewData(): array
    {
        return [
            'iconOptions' => $this->iconOptions(),
            'userName'    => session()->get('userName'),
            'userEmail'   => session()->get('userEmail'),
            'userRole'    => session()->get('userRole'),
        ];
    }

    private function validationRules(): array
    {
        return [
            'harga_layanan' => 'required|integer|greater_than_equal_to[0]',
            'icon'          => 'required|max_length[80]|regex_match[/^bi-[a-z0-9-]+$/]',
        ];
    }

    private function isLocked(int $layananId): bool
    {
        return $this->transaksiSewaLayananModel->where('layanan_tambahan_id', $layananId)->countAllResults() > 0;
    }

    private function normalizeIcon(string $icon): string
    {
        $icon = trim($icon);

        return $icon !== '' ? $icon : 'bi-stars';
    }

    private function iconOptions(): array
    {
        return [
            'bi-car-front-fill' => 'Kendaraan',
            'bi-geo-alt-fill' => 'Lokasi',
            'bi-pin-map-fill' => 'Antar Jemput',
            'bi-person-badge-fill' => 'Driver',
            'bi-person-check-fill' => 'Pendamping',
            'bi-clock-fill' => 'Jam',
            'bi-calendar-check-fill' => 'Jadwal',
            'bi-shield-check' => 'Asuransi',
            'bi-fuel-pump-fill' => 'BBM',
            'bi-tools' => 'Peralatan',
            'bi-wrench-adjustable' => 'Maintenance',
            'bi-stars' => 'Layanan',
            'bi-gift-fill' => 'Bonus',
            'bi-cash-coin' => 'Biaya',
            'bi-credit-card-fill' => 'Pembayaran',
            'bi-headset' => 'Customer Service',
            'bi-whatsapp' => 'WhatsApp',
            'bi-telephone-fill' => 'Telepon',
            'bi-bag-check-fill' => 'Paket',
            'bi-box-seam-fill' => 'Barang',
            'bi-suitcase-lg-fill' => 'Bagasi',
            'bi-airplane-fill' => 'Bandara',
            'bi-building-fill' => 'Hotel',
            'bi-house-check-fill' => 'Rumah',
            'bi-signpost-split-fill' => 'Rute',
            'bi-map-fill' => 'Peta',
            'bi-ticket-perforated-fill' => 'Tiket',
            'bi-cup-hot-fill' => 'Minuman',
            'bi-lightning-charge-fill' => 'Cepat',
            'bi-patch-check-fill' => 'Terjamin',
        ];
    }
}
