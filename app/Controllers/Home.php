<?php

namespace App\Controllers;

use App\Models\KendaraanFotoModel;
use App\Models\KendaraanModel;
use App\Models\LayananTambahanModel;
use App\Models\TransaksiSewaModel;

class Home extends BaseController
{
    public function index(): string
    {
        $kendaraanModel = new KendaraanModel();
        $fotoModel      = new KendaraanFotoModel();
        $layananModel   = new LayananTambahanModel();
        $perPage        = 8;
        $setting        = app_setting();
        $appName        = trim((string) ($setting['nama_aplikasi'] ?? 'Rental Kendaraan')) ?: 'Rental Kendaraan';

        $kendaraan = $kendaraanModel
            ->whereIn('status', ['ready', 'maintenance'])
            ->orderBy('jenis_kendaraan', 'ASC')
            ->orderBy('harga_sewa_per_hari', 'ASC')
            ->paginate($perPage, 'kendaraan');

        $kendaraan = $this->attachMainPhotos($kendaraan, $fotoModel);

        $summary = [
            'total_kendaraan' => $kendaraanModel->whereIn('status', ['ready', 'maintenance'])->countAllResults(),
            'ready'           => $kendaraanModel->where('status', 'ready')->countAllResults(),
            'mobil'           => $kendaraanModel->where('status', 'ready')->where('jenis_kendaraan', 'Mobil')->countAllResults(),
            'motor'           => $kendaraanModel->where('status', 'ready')->where('jenis_kendaraan', 'Sepeda Motor')->countAllResults(),
        ];

        return view('public/home', [
            'setting'   => $setting,
            'title'     => $appName . ' - Rental Mobil dan Motor',
            'active'    => 'home',
            'kendaraan' => $kendaraan,
            'layanan'   => $layananModel->orderBy('nama_layanan', 'ASC')->findAll(),
            'summary'   => $summary,
            'pager'     => $kendaraanModel->pager,
        ]);
    }

    public function detail(int $id): string
    {
        $kendaraanModel = new KendaraanModel();
        $fotoModel      = new KendaraanFotoModel();
        $layananModel   = new LayananTambahanModel();
        $setting        = app_setting();
        $appName        = trim((string) ($setting['nama_aplikasi'] ?? 'Rental Kendaraan')) ?: 'Rental Kendaraan';

        $kendaraan = $kendaraanModel
            ->whereIn('status', ['ready', 'maintenance'])
            ->find($id);

        if (! $kendaraan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Kendaraan tidak ditemukan.');
        }

        $fotoKendaraan = $fotoModel
            ->where('kendaraan_id', $id)
            ->orderBy('id', 'ASC')
            ->findAll();

        $rekomendasi = $kendaraanModel
            ->where('id !=', $id)
            ->whereIn('status', ['ready', 'maintenance'])
            ->orderBy('status', 'DESC')
            ->orderBy('harga_sewa_per_hari', 'ASC')
            ->findAll(4);

        return view('public/kendaraan_detail', [
            'setting'       => $setting,
            'title'         => ($kendaraan['nama_kendaraan'] ?? 'Detail Kendaraan') . ' - ' . $appName,
            'active'        => 'armada',
            'kendaraan'     => $kendaraan,
            'fotoKendaraan' => $fotoKendaraan,
            'rekomendasi'   => $this->attachMainPhotos($rekomendasi, $fotoModel),
            'layanan'       => $layananModel->orderBy('nama_layanan', 'ASC')->findAll(),
        ]);
    }

    public function cekKetersediaan(): string
    {
        $tanggalSewa    = trim((string) $this->request->getGet('tanggal_sewa'));
        $tanggalKembali = trim((string) $this->request->getGet('tanggal_kembali'));
        $jenisKendaraan = trim((string) $this->request->getGet('jenis_kendaraan'));
        $fotoModel      = new KendaraanFotoModel();
        $setting        = app_setting();
        $appName        = trim((string) ($setting['nama_aplikasi'] ?? 'Rental Kendaraan')) ?: 'Rental Kendaraan';

        $filters = [
            'tanggal_sewa'     => $tanggalSewa,
            'tanggal_kembali'  => $tanggalKembali,
            'jenis_kendaraan'  => $jenisKendaraan,
        ];

        $isValidRange = $this->isDateRangeValid($tanggalSewa, $tanggalKembali);
        $kendaraan    = $isValidRange ? $this->kendaraanTersedia($tanggalSewa, $tanggalKembali, $jenisKendaraan) : [];

        return view('public/cek_ketersediaan', [
            'setting'      => $setting,
            'title'        => 'Cek Ketersediaan - ' . $appName,
            'active'       => 'armada',
            'filters'      => $filters,
            'kendaraan'    => $this->attachMainPhotos($kendaraan, $fotoModel),
            'isValidRange' => $isValidRange,
            'lamaSewa'     => $isValidRange ? $this->calculateLamaSewa($tanggalSewa, $tanggalKembali) : 0,
        ]);
    }

    private function attachMainPhotos(array $kendaraan, KendaraanFotoModel $fotoModel): array
    {
        foreach ($kendaraan as &$row) {
            $foto = $fotoModel
                ->where('kendaraan_id', $row['id'])
                ->orderBy('id', 'ASC')
                ->first();

            $row['foto_utama'] = $foto['file_name'] ?? null;
        }
        unset($row);

        return $kendaraan;
    }

    private function kendaraanTersedia(string $tanggalSewa, string $tanggalKembali, string $jenisKendaraan = ''): array
    {
        $kendaraanModel = new KendaraanModel();
        $builder = $kendaraanModel
            ->where('status', 'ready')
            ->orderBy('harga_sewa_per_hari', 'ASC')
            ->orderBy('nama_kendaraan', 'ASC');

        if ($jenisKendaraan !== '' && in_array($jenisKendaraan, ['Mobil', 'Sepeda Motor'], true)) {
            $builder->where('jenis_kendaraan', $jenisKendaraan);
        }

        $kendaraanRows = $builder->findAll();

        return array_values(array_filter($kendaraanRows, function (array $kendaraan) use ($tanggalSewa, $tanggalKembali): bool {
            return $this->isKendaraanAvailable((int) $kendaraan['id'], $tanggalSewa, $tanggalKembali);
        }));
    }

    private function isKendaraanAvailable(int $kendaraanId, string $tanggalSewa, string $tanggalKembali): bool
    {
        $transaksiModel = new TransaksiSewaModel();

        return $transaksiModel
            ->where('kendaraan_id', $kendaraanId)
            ->groupStart()
                ->groupStart()
                    ->where('status_transaksi', 'booking')
                    ->where('tanggal_sewa <=', $tanggalKembali)
                    ->where('tanggal_kembali >=', $tanggalSewa)
                ->groupEnd()
                ->orGroupStart()
                    ->where('status_transaksi', 'berjalan')
                    ->where('tanggal_sewa <=', $tanggalKembali)
                ->groupEnd()
            ->groupEnd()
            ->countAllResults() === 0;
    }

    private function isDateRangeValid(string $tanggalSewa, string $tanggalKembali): bool
    {
        return $tanggalSewa !== '' && $tanggalKembali !== '' && $tanggalKembali >= $tanggalSewa;
    }

    private function calculateLamaSewa(string $tanggalSewa, string $tanggalKembali): int
    {
        $start = new \DateTime($tanggalSewa);
        $end   = new \DateTime($tanggalKembali);

        return max(1, $start->diff($end)->days + 1);
    }
}
