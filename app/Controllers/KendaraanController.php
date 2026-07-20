<?php

namespace App\Controllers;

use App\Models\KendaraanFotoModel;
use App\Models\KendaraanModel;
use App\Models\TransaksiSewaModel;
use CodeIgniter\HTTP\ResponseInterface;

class KendaraanController extends BaseController
{
    private KendaraanModel $kendaraanModel;
    private KendaraanFotoModel $kendaraanFotoModel;
    private TransaksiSewaModel $transaksiSewaModel;
    private string $uploadPath;
    private string $tempPath;

    private array $merkOptions = [
        'Toyota',
        'Honda',
        'Hyundai',
        'Daihatsu',
        'Suzuki',
        'Mitsubishi',
        'Nissan',
        'Mazda',
        'Wuling',
        'Kia',
        'Isuzu',
        'Mercedes-Benz',
        'BMW',
        'Yamaha',
        'Kawasaki',
        'Vespa',
        'Piaggio',
        'TVS',
        'Benelli',
        'Polytron',
        'United',
        'Lainnya',
    ];

    private array $warnaOptions = [
        'Putih',
        'Hitam',
        'Silver',
        'Abu-abu',
        'Merah',
        'Biru',
        'Cokelat',
        'Krem',
        'Hijau',
        'Kuning',
        'Oranye',
        'Emas',
        'Maroon',
        'Ungu',
        'Lainnya',
    ];

    private array $jenisKendaraanOptions = [
        'Mobil',
        'Sepeda Motor',
    ];

    private array $statusOptions = [
        'ready'       => 'Ready',
        'maintenance' => 'Maintenance',
        'nonaktif'    => 'Nonaktif',
    ];

    public function __construct()
    {
        $this->kendaraanModel     = new KendaraanModel();
        $this->kendaraanFotoModel = new KendaraanFotoModel();
        $this->transaksiSewaModel = new TransaksiSewaModel();
        $this->uploadPath     = FCPATH . 'uploads/kendaraan';
        $this->tempPath       = $this->uploadPath . DIRECTORY_SEPARATOR . 'temp';
    }

    public function index()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $filters = [
            'jenis_kendaraan' => trim((string) $this->request->getGet('jenis_kendaraan')),
            'merk'            => trim((string) $this->request->getGet('merk')),
            'tahun'           => trim((string) $this->request->getGet('tahun')),
            'warna'           => trim((string) $this->request->getGet('warna')),
            'status'          => trim((string) $this->request->getGet('status')),
        ];

        $builder = $this->kendaraanModel->orderBy('id', 'DESC');

        foreach ($filters as $field => $value) {
            if ($value !== '') {
                $builder->where($field, $value);
            }
        }

        $kendaraan = $builder->findAll();

        foreach ($kendaraan as &$row) {
            $row['foto_utama'] = $this->kendaraanFotoModel
                ->where('kendaraan_id', $row['id'])
                ->orderBy('id', 'ASC')
                ->first()['file_name'] ?? null;
            $row['has_transaksi'] = $this->hasTransaksi((int) $row['id']);
        }

        return view('admin/kendaraan/index', [
            'kendaraan'             => $kendaraan,
            'filters'               => $filters,
            'jenisKendaraanOptions' => $this->jenisKendaraanOptions,
            'merkOptions'           => $this->merkOptions,
            'warnaOptions'          => $this->warnaOptions,
            'statusOptions'         => $this->statusOptions,
            'userName'              => session()->get('userName'),
            'userEmail'             => session()->get('userEmail'),
            'userRole'              => session()->get('userRole'),
        ]);
    }

    public function detail(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $kendaraan = $this->kendaraanModel->find($id);

        if (! $kendaraan) {
            return redirect()->to('/admin/kendaraan')->with('error', 'Data kendaraan tidak ditemukan.');
        }

        $transaksiTerakhir = $this->transaksiSewaModel
            ->select('transaksi_sewa.*, pelanggan.nama_lengkap, pelanggan.kode_pelanggan')
            ->join('pelanggan', 'pelanggan.id = transaksi_sewa.pelanggan_id')
            ->where('transaksi_sewa.kendaraan_id', $id)
            ->orderBy('transaksi_sewa.id', 'DESC')
            ->findAll(10);

        return view('admin/kendaraan/detail', [
            'kendaraan'         => $kendaraan,
            'fotoKendaraan'     => $this->kendaraanFotoModel->where('kendaraan_id', $id)->orderBy('id', 'ASC')->findAll(),
            'transaksiTerakhir' => $transaksiTerakhir,
            'hasTransaksi'      => $this->hasTransaksi($id),
            'statusOptions'     => $this->statusOptions,
            'userName'          => session()->get('userName'),
            'userEmail'         => session()->get('userEmail'),
            'userRole'          => session()->get('userRole'),
        ]);
    }

    public function monitor()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $tanggal = (string) ($this->request->getGet('tanggal') ?: date('Y-m-d'));
        $kendaraan = $this->kendaraanModel->orderBy('jenis_kendaraan', 'ASC')->orderBy('nama_kendaraan', 'ASC')->findAll();

        foreach ($kendaraan as &$row) {
            $row['foto_utama'] = $this->kendaraanFotoModel
                ->where('kendaraan_id', $row['id'])
                ->orderBy('id', 'ASC')
                ->first()['file_name'] ?? null;
            $row['jadwal'] = $this->jadwalPadaTanggal((int) $row['id'], $tanggal);
            $row['status_monitor'] = $this->statusMonitor($row, $row['jadwal'], $tanggal);
        }

        return view('admin/kendaraan/monitor', [
            'kendaraan' => $kendaraan,
            'tanggal'   => $tanggal,
            'summary'   => $this->monitorSummary($kendaraan),
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

        return view('admin/kendaraan/create', [
            'kodeKendaraan'          => $this->generateKodeKendaraan(),
            'jenisKendaraanOptions'  => $this->jenisKendaraanOptions,
            'merkOptions'            => $this->merkOptions,
            'warnaOptions'           => $this->warnaOptions,
            'statusOptions'          => $this->statusOptions,
            'userName'               => session()->get('userName'),
            'userEmail'              => session()->get('userEmail'),
            'userRole'               => session()->get('userRole'),
        ]);
    }

    public function store()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $rules = $this->validationRules();
        $rules['plat_nomor'] = 'required|max_length[30]|is_unique[kendaraan.plat_nomor]';

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kendaraanId = $this->kendaraanModel->insert([
            'kode_kendaraan'           => $this->generateKodeKendaraan(),
            'jenis_kendaraan'      => $this->request->getPost('jenis_kendaraan'),
            'plat_nomor'           => strtoupper((string) $this->request->getPost('plat_nomor')),
            'merk'                 => $this->request->getPost('merk'),
            'nama_kendaraan'           => $this->request->getPost('nama_kendaraan'),
            'tahun'                => $this->request->getPost('tahun'),
            'warna'                => $this->request->getPost('warna'),
            'harga_sewa_per_hari'  => $this->request->getPost('harga_sewa_per_hari'),
            'status'               => $this->request->getPost('status'),
            'keterangan'           => $this->request->getPost('keterangan'),
        ]);

        $this->attachUploadedPhotos((int) $kendaraanId, $this->request->getPost('foto_kendaraan'));

        return redirect()->to('/admin/kendaraan')->with('success', 'Data kendaraan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $kendaraan = $this->kendaraanModel->find($id);

        if (! $kendaraan) {
            return redirect()->to('/admin/kendaraan')->with('error', 'Data kendaraan tidak ditemukan.');
        }

        return view('admin/kendaraan/edit', [
            'kendaraan'             => $kendaraan,
            'fotoKendaraan'         => $this->kendaraanFotoModel->where('kendaraan_id', $id)->orderBy('id', 'ASC')->findAll(),
            'jenisKendaraanOptions' => $this->jenisKendaraanOptions,
            'merkOptions'           => $this->merkOptions,
            'warnaOptions'          => $this->warnaOptions,
            'statusOptions'         => $this->statusOptions,
            'userName'              => session()->get('userName'),
            'userEmail'             => session()->get('userEmail'),
            'userRole'              => session()->get('userRole'),
        ]);
    }

    public function update(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $kendaraan = $this->kendaraanModel->find($id);

        if (! $kendaraan) {
            return redirect()->to('/admin/kendaraan')->with('error', 'Data kendaraan tidak ditemukan.');
        }

        $rules = $this->validationRules();
        $rules['plat_nomor'] = 'required|max_length[30]|is_unique[kendaraan.plat_nomor,id,' . $id . ']';

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kendaraanModel->update($id, [
            'jenis_kendaraan'     => $this->request->getPost('jenis_kendaraan'),
            'plat_nomor'          => strtoupper((string) $this->request->getPost('plat_nomor')),
            'merk'                => $this->request->getPost('merk'),
            'nama_kendaraan'          => $this->request->getPost('nama_kendaraan'),
            'tahun'               => $this->request->getPost('tahun'),
            'warna'               => $this->request->getPost('warna'),
            'harga_sewa_per_hari' => $this->request->getPost('harga_sewa_per_hari'),
            'status'              => $this->request->getPost('status'),
            'keterangan'          => $this->request->getPost('keterangan'),
        ]);

        $this->deleteSelectedPhotos($this->request->getPost('hapus_foto'));
        $this->attachUploadedPhotos($id, $this->request->getPost('foto_kendaraan'));

        return redirect()->to('/admin/kendaraan')->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $kendaraan = $this->kendaraanModel->find($id);

        if (! $kendaraan) {
            return redirect()->to('/admin/kendaraan')->with('error', 'Data kendaraan tidak ditemukan.');
        }

        if ($this->hasTransaksi($id)) {
            return redirect()
                ->to('/admin/kendaraan')
                ->with('error', 'Kendaraan yang sudah memiliki riwayat transaksi tidak bisa dihapus demi keamanan data. Ubah status kendaraan menjadi Nonaktif jika tidak dipakai lagi.');
        }

        $photos = $this->kendaraanFotoModel->where('kendaraan_id', $id)->findAll();

        foreach ($photos as $photo) {
            $this->deletePhotoFile($photo['file_name']);
        }

        $this->kendaraanModel->delete($id);

        return redirect()->to('/admin/kendaraan')->with('success', 'Data kendaraan berhasil dihapus.');
    }

    public function uploadPhoto(): ResponseInterface
    {
        if (! $this->isLoggedIn()) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Sesi login tidak valid.']);
        }

        $photo = $this->getUploadedPhoto();

        if (! $photo || ! $photo->isValid() || $photo->hasMoved()) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'File foto tidak valid.']);
        }

        if (! in_array($photo->getClientMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true) || $photo->getSizeByUnit('mb') > 2) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Foto harus jpg, png, atau webp maksimal 2 MB.']);
        }

        if (! is_dir($this->tempPath)) {
            mkdir($this->tempPath, 0775, true);
        }

        $fileName = $photo->getRandomName();
        $photo->move($this->tempPath, $fileName);

        return $this->response->setJSON([
            'fileName' => $fileName,
            'csrfHash' => csrf_hash(),
        ]);
    }

    public function revertPhoto(): ResponseInterface
    {
        if (! $this->isLoggedIn()) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Sesi login tidak valid.']);
        }

        $fileName = basename(trim((string) $this->request->getBody()));
        $filePath = $this->tempPath . DIRECTORY_SEPARATOR . $fileName;

        if ($fileName !== '' && is_file($filePath)) {
            unlink($filePath);
        }

        return $this->response->setJSON(['csrfHash' => csrf_hash()]);
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('isLoggedIn');
    }

    private function validationRules(): array
    {
        return [
            'jenis_kendaraan'     => 'required|in_list[' . implode(',', $this->jenisKendaraanOptions) . ']',
            'merk'                => 'required|in_list[' . implode(',', $this->merkOptions) . ']',
            'nama_kendaraan'          => 'required|min_length[2]|max_length[150]',
            'tahun'               => 'required|integer|greater_than_equal_to[1980]|less_than_equal_to[' . ((int) date('Y') + 1) . ']',
            'warna'               => 'required|in_list[' . implode(',', $this->warnaOptions) . ']',
            'harga_sewa_per_hari' => 'required|integer|greater_than_equal_to[0]',
            'status'              => 'required|in_list[ready,maintenance,nonaktif]',
            'keterangan'          => 'permit_empty',
        ];
    }

    private function hasTransaksi(int $kendaraanId): bool
    {
        return $this->transaksiSewaModel->where('kendaraan_id', $kendaraanId)->countAllResults() > 0;
    }

    private function jadwalPadaTanggal(int $kendaraanId, string $tanggal): ?array
    {
        return $this->transaksiSewaModel
            ->select('transaksi_sewa.*, pelanggan.nama_lengkap, pelanggan.kode_pelanggan, pelanggan.no_hp')
            ->join('pelanggan', 'pelanggan.id = transaksi_sewa.pelanggan_id')
            ->where('transaksi_sewa.kendaraan_id', $kendaraanId)
            ->groupStart()
                ->groupStart()
                    ->where('transaksi_sewa.status_transaksi', 'booking')
                    ->where('transaksi_sewa.tanggal_sewa <=', $tanggal)
                    ->where('transaksi_sewa.tanggal_kembali >=', $tanggal)
                ->groupEnd()
                ->orGroupStart()
                    ->where('transaksi_sewa.status_transaksi', 'berjalan')
                    ->where('transaksi_sewa.tanggal_sewa <=', $tanggal)
                ->groupEnd()
                ->orGroupStart()
                    ->where('transaksi_sewa.status_transaksi', 'selesai')
                    ->where('transaksi_sewa.tanggal_sewa <=', $tanggal)
                    ->where('COALESCE(transaksi_sewa.tanggal_dikembalikan, transaksi_sewa.tanggal_kembali) >=', $tanggal, false)
                ->groupEnd()
            ->groupEnd()
            ->orderBy("FIELD(transaksi_sewa.status_transaksi, 'berjalan', 'booking', 'selesai')", '', false)
            ->orderBy('transaksi_sewa.tanggal_sewa', 'ASC')
            ->first();
    }

    private function statusMonitor(array $kendaraan, ?array $jadwal, string $tanggal): array
    {
        if ($kendaraan['status'] === 'nonaktif') {
            if ($jadwal) {
                return $this->monitorStatusData('konflik', 'Konflik Jadwal', 'danger', 'Kendaraan nonaktif tetapi masih memiliki jadwal sewa.', 'bi-exclamation-triangle-fill', $jadwal, $tanggal);
            }

            return $this->monitorStatusData('nonaktif', 'Nonaktif', 'secondary', 'Kendaraan tidak digunakan untuk operasional.', 'bi-slash-circle');
        }

        if ($kendaraan['status'] === 'maintenance') {
            if ($jadwal) {
                return $this->monitorStatusData('konflik', 'Konflik Jadwal', 'danger', 'Kendaraan maintenance tetapi masih memiliki jadwal sewa.', 'bi-exclamation-triangle-fill', $jadwal, $tanggal);
            }

            return $this->monitorStatusData('maintenance', 'Maintenance', 'warning', 'Kendaraan sedang maintenance.', 'bi-tools');
        }

        if ($jadwal) {
            if ($jadwal['status_transaksi'] === 'berjalan' && $jadwal['tanggal_kembali'] < $tanggal) {
                return $this->monitorStatusData('telat', 'Telat Dikembalikan', 'danger', $jadwal['kode_transaksi'] . ' - ' . $jadwal['nama_lengkap'], 'bi-exclamation-octagon-fill', $jadwal, $tanggal);
            }

            if ($jadwal['status_transaksi'] === 'booking') {
                return $this->monitorStatusData('booking', 'Booking', 'warning', $jadwal['kode_transaksi'] . ' - ' . $jadwal['nama_lengkap'], 'bi-calendar-check-fill', $jadwal, $tanggal);
            }

            return $this->monitorStatusData('disewa', 'Disewa', 'primary', $jadwal['kode_transaksi'] . ' - ' . $jadwal['nama_lengkap'], 'bi-key-fill', $jadwal, $tanggal);
        }

        return $this->monitorStatusData('tersedia', 'Tersedia', 'success', 'Tidak ada jadwal sewa pada tanggal ini.', 'bi-check-circle-fill');
    }

    private function monitorStatusData(string $key, string $label, string $class, string $description, string $icon, ?array $jadwal = null, ?string $tanggal = null): array
    {
        $data = [
            'key'         => $key,
            'label'       => $label,
            'class'       => $class,
            'description' => $description,
            'icon'        => $icon,
            'meta'        => null,
        ];

        if ($jadwal && $tanggal) {
            $data['meta'] = $this->jadwalMeta($jadwal, $tanggal);
        }

        return $data;
    }

    private function jadwalMeta(array $jadwal, string $tanggal): array
    {
        $tanggalSewa = (string) $jadwal['tanggal_sewa'];
        $tanggalKembali = (string) $jadwal['tanggal_kembali'];
        $tanggalAkhir = (string) ($jadwal['tanggal_dikembalikan'] ?: $tanggalKembali);
        $hariTelat = 0;
        $hariMenujuKembali = null;

        if ($jadwal['status_transaksi'] === 'berjalan' && $tanggalKembali < $tanggal) {
            $hariTelat = (new \DateTime($tanggalKembali))->diff(new \DateTime($tanggal))->days;
        } elseif ($jadwal['status_transaksi'] !== 'selesai') {
            $hariMenujuKembali = (new \DateTime($tanggal))->diff(new \DateTime($tanggalKembali))->days;
        }

        return [
            'kode_transaksi'      => $jadwal['kode_transaksi'] ?? '-',
            'nama_lengkap'        => $jadwal['nama_lengkap'] ?? '-',
            'no_hp'               => $jadwal['no_hp'] ?? '-',
            'status_transaksi'    => $jadwal['status_transaksi'] ?? '-',
            'tanggal_sewa'        => $tanggalSewa,
            'tanggal_kembali'     => $tanggalKembali,
            'tanggal_akhir'       => $tanggalAkhir,
            'tanggal_dikembalikan' => $jadwal['tanggal_dikembalikan'] ?? null,
            'hari_telat'          => $hariTelat,
            'hari_menuju_kembali' => $hariMenujuKembali,
        ];
    }

    private function monitorSummary(array $kendaraan): array
    {
        $summary = [
            'total'       => count($kendaraan),
            'tersedia'    => 0,
            'disewa'      => 0,
            'booking'     => 0,
            'telat'       => 0,
            'maintenance' => 0,
            'nonaktif'    => 0,
            'konflik'     => 0,
        ];

        foreach ($kendaraan as $row) {
            $key = $row['status_monitor']['key'] ?? 'tersedia';

            if (array_key_exists($key, $summary)) {
                $summary[$key]++;
            }
        }

        return $summary;
    }

    private function getUploadedPhoto()
    {
        $photo = $this->request->getFile('foto_kendaraan');

        if ($photo) {
            return $photo;
        }

        $files = $this->request->getFiles();

        if (isset($files['foto_kendaraan']) && is_array($files['foto_kendaraan'])) {
            return reset($files['foto_kendaraan']);
        }

        return null;
    }

    private function generateKodeKendaraan(): string
    {
        $lastKendaraan = $this->kendaraanModel
            ->select('kode_kendaraan')
            ->orderBy('id', 'DESC')
            ->first();

        $lastNumber = 0;

        if ($lastKendaraan && preg_match('/^(\d+)$/', (string) $lastKendaraan['kode_kendaraan'], $matches)) {
            $lastNumber = (int) $matches[1];
        }

        return str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
    }

    private function attachUploadedPhotos(int $kendaraanId, $photoNames): void
    {
        $photoNames = is_array($photoNames) ? $photoNames : array_filter([(string) $photoNames]);

        if (! is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0775, true);
        }

        foreach ($photoNames as $photoName) {
            $safeName = basename((string) $photoName);
            $tempFile = $this->tempPath . DIRECTORY_SEPARATOR . $safeName;
            $finalFile = $this->uploadPath . DIRECTORY_SEPARATOR . $safeName;

            if ($safeName === '' || ! is_file($tempFile)) {
                continue;
            }

            rename($tempFile, $finalFile);
            $this->kendaraanFotoModel->insert([
                'kendaraan_id'   => $kendaraanId,
                'file_name'  => $safeName,
            ]);
        }
    }

    private function deleteSelectedPhotos($photoIds): void
    {
        $photoIds = is_array($photoIds) ? $photoIds : array_filter([(string) $photoIds]);

        foreach ($photoIds as $photoId) {
            $photo = $this->kendaraanFotoModel->find((int) $photoId);

            if (! $photo) {
                continue;
            }

            $this->deletePhotoFile($photo['file_name']);
            $this->kendaraanFotoModel->delete((int) $photoId);
        }
    }

    private function deletePhotoFile(?string $fileName): void
    {
        if (! $fileName) {
            return;
        }

        $filePath = $this->uploadPath . DIRECTORY_SEPARATOR . basename($fileName);

        if (is_file($filePath)) {
            unlink($filePath);
        }
    }
}
