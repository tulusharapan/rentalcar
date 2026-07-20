<?php

namespace App\Controllers;

use App\Models\KategoriKeuanganModel;
use App\Models\LayananTambahanModel;
use App\Models\KendaraanModel;
use App\Models\PelangganModel;
use App\Models\PembayaranSewaModel;
use App\Models\SettingModel;
use App\Models\TransaksiKeuanganModel;
use App\Models\TransaksiSewaLayananModel;
use App\Models\TransaksiSewaModel;
use CodeIgniter\HTTP\ResponseInterface;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;

class TransaksiSewaController extends BaseController
{
    private const PAYMENT_FINANCE_CATEGORY_ID = 2;

    private TransaksiSewaModel $transaksiModel;
    private TransaksiSewaLayananModel $transaksiLayananModel;
    private PembayaranSewaModel $pembayaranModel;
    private PelangganModel $pelangganModel;
    private KendaraanModel $kendaraanModel;
    private LayananTambahanModel $layananModel;
    private SettingModel $settingModel;
    private KategoriKeuanganModel $kategoriKeuanganModel;
    private TransaksiKeuanganModel $transaksiKeuanganModel;

    private array $statusTransaksiOptions = [
        'booking'  => 'Booking',
        'berjalan' => 'Berjalan',
        'selesai'  => 'Selesai/Dikembalikan',
        'batal'    => 'Batal',
    ];

    private array $metodePembayaranOptions = [
        'tunai'    => 'Tunai',
        'qris'     => 'QRIS',
        'transfer' => 'Transfer',
    ];

    public function __construct()
    {
        $this->transaksiModel        = new TransaksiSewaModel();
        $this->transaksiLayananModel = new TransaksiSewaLayananModel();
        $this->pembayaranModel       = new PembayaranSewaModel();
        $this->pelangganModel        = new PelangganModel();
        $this->kendaraanModel        = new KendaraanModel();
        $this->layananModel          = new LayananTambahanModel();
        $this->settingModel          = new SettingModel();
        $this->kategoriKeuanganModel = new KategoriKeuanganModel();
        $this->transaksiKeuanganModel = new TransaksiKeuanganModel();
    }

    public function index()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $filters = $this->indexFilters();
        $builder = $this->transaksiModel
            ->select('transaksi_sewa.*, pelanggan.nama_lengkap, pelanggan.kode_pelanggan, pelanggan.no_hp, kendaraan.jenis_kendaraan, kendaraan.nama_kendaraan, kendaraan.plat_nomor')
            ->join('pelanggan', 'pelanggan.id = transaksi_sewa.pelanggan_id')
            ->join('kendaraan', 'kendaraan.id = transaksi_sewa.kendaraan_id');

        if ($filters['q'] !== '') {
            $builder
                ->groupStart()
                    ->like('transaksi_sewa.kode_transaksi', $filters['q'])
                    ->orLike('pelanggan.nama_lengkap', $filters['q'])
                    ->orLike('pelanggan.kode_pelanggan', $filters['q'])
                    ->orLike('pelanggan.no_hp', $filters['q'])
                    ->orLike('kendaraan.nama_kendaraan', $filters['q'])
                    ->orLike('kendaraan.plat_nomor', $filters['q'])
                ->groupEnd();
        }

        if ($filters['tanggal_mulai'] !== '') {
            $builder->where('transaksi_sewa.tanggal_transaksi >=', $filters['tanggal_mulai']);
        }

        if ($filters['tanggal_selesai'] !== '') {
            $builder->where('transaksi_sewa.tanggal_transaksi <=', $filters['tanggal_selesai']);
        }

        if ($filters['periode_mulai'] !== '' && $filters['periode_selesai'] !== '') {
            $builder
                ->where('transaksi_sewa.tanggal_sewa <=', $filters['periode_selesai'])
                ->where('transaksi_sewa.tanggal_kembali >=', $filters['periode_mulai']);
        } elseif ($filters['periode_mulai'] !== '') {
            $builder->where('transaksi_sewa.tanggal_kembali >=', $filters['periode_mulai']);
        } elseif ($filters['periode_selesai'] !== '') {
            $builder->where('transaksi_sewa.tanggal_sewa <=', $filters['periode_selesai']);
        }

        foreach (['pelanggan_id', 'kendaraan_id', 'status_transaksi', 'status_pembayaran'] as $field) {
            if ($filters[$field] !== '') {
                $builder->where('transaksi_sewa.' . $field, $filters[$field]);
            }
        }

        $transaksi = $builder
            ->orderBy('transaksi_sewa.id', 'DESC')
            ->findAll();

        foreach ($transaksi as &$row) {
            $totalBayar = $this->totalBayar((int) $row['id']);
            $row['total_bayar'] = $totalBayar;
            $row['sisa_tagihan'] = max(0, (int) $row['total_tagihan'] - $totalBayar);
            $row['hari_telat_berjalan'] = 0;

            if ($row['status_transaksi'] === 'berjalan' && $row['tanggal_kembali'] < date('Y-m-d')) {
                $tanggalKembali = new DateTime($row['tanggal_kembali']);
                $hariIni = new DateTime(date('Y-m-d'));
                $row['hari_telat_berjalan'] = $tanggalKembali->diff($hariIni)->days;
            }
        }
        unset($row);

        $transaksi = $this->filterIndexKondisi($transaksi, $filters['kondisi']);

        return view('admin/transaksi_sewa/index', [
            'transaksi'               => $transaksi,
            'filters'                 => $filters,
            'summary'                 => $this->indexSummary($transaksi),
            'pelangganOptions'        => $this->pelangganModel->orderBy('nama_lengkap', 'ASC')->findAll(),
            'kendaraanOptions'        => $this->kendaraanModel->orderBy('nama_kendaraan', 'ASC')->findAll(),
            'statusTransaksiOptions'  => $this->statusTransaksiOptions,
            'statusPembayaranOptions' => $this->statusPembayaranOptions(),
            'kondisiOptions'          => [
                'telat'           => 'Telat Dikembalikan',
                'jatuh_tempo'     => 'Jatuh Tempo Hari Ini',
                'perlu_pembayaran' => 'Masih Ada Sisa Tagihan',
            ],
            'userName'                => session()->get('userName'),
            'userEmail'               => session()->get('userEmail'),
            'userRole'                => session()->get('userRole'),
        ]);
    }

    public function create()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        return view('admin/transaksi_sewa/create', array_merge($this->formData(), [
            'kodeTransaksi' => $this->generateKodeTransaksi(),
            'kendaraanOptions'  => $this->availableKendaraan(),
        ]));
    }

    public function laporan()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $filters = $this->laporanFilters();
        $transaksi = $this->laporanTransaksi($filters);

        return view('admin/transaksi_sewa/laporan', array_merge($this->laporanViewData(), [
            'filters'   => $filters,
            'transaksi' => $transaksi,
            'summary'   => $this->laporanSummary($transaksi),
        ]));
    }

    public function laporanPdf()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $filters = $this->laporanFilters();
        $transaksi = $this->laporanTransaksi($filters);
        $html = view('admin/transaksi_sewa/laporan_pdf', [
            'filters'   => $filters,
            'transaksi' => $transaksi,
            'summary'   => $this->laporanSummary($transaksi),
        ]);

        return $this->streamPdf($html, 'laporan-transaksi-sewa.pdf', 'A4', 'landscape');
    }

    public function invoicePdf(int $transaksiId)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $transaksi = $this->findTransaksiDetail($transaksiId);

        if (! $transaksi) {
            return redirect()->to('/admin/transaksi-sewa')->with('error', 'Data transaksi tidak ditemukan.');
        }

        $pembayaran = $this->pembayaranModel->where('transaksi_sewa_id', $transaksiId)->orderBy('id', 'ASC')->findAll();
        $totalBayar = $this->totalBayar($transaksiId);
        $html = view('admin/transaksi_sewa/invoice_pdf', [
            'transaksi'  => $transaksi,
            'layanan'    => $this->transaksiLayananModel->where('transaksi_sewa_id', $transaksiId)->orderBy('id', 'ASC')->findAll(),
            'pembayaran' => $pembayaran,
            'totalBayar' => $totalBayar,
            'sisaTagihan' => max(0, (int) $transaksi['total_tagihan'] - $totalBayar),
            'petugas'    => session()->get('userName') ?? '-',
        ]);

        return $this->streamPdf($html, 'invoice-' . $transaksi['kode_transaksi'] . '.pdf');
    }

    public function suratJalanPdf(int $transaksiId)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $transaksi = $this->findTransaksiDetail($transaksiId);

        if (! $transaksi) {
            return redirect()->to('/admin/transaksi-sewa')->with('error', 'Data transaksi tidak ditemukan.');
        }

        $html = view('admin/transaksi_sewa/surat_jalan_pdf', [
            'transaksi' => $transaksi,
            'layanan'   => $this->transaksiLayananModel->where('transaksi_sewa_id', $transaksiId)->orderBy('id', 'ASC')->findAll(),
            'petugas'   => session()->get('userName') ?? '-',
        ]);

        return $this->streamPdf($html, 'surat-jalan-' . $transaksi['kode_transaksi'] . '.pdf');
    }

    public function detail(int $transaksiId)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $transaksi = $this->findTransaksiDetail($transaksiId);

        if (! $transaksi) {
            return redirect()->to('/admin/transaksi-sewa')->with('error', 'Data transaksi tidak ditemukan.');
        }

        $pembayaran = $this->pembayaranModel->where('transaksi_sewa_id', $transaksiId)->orderBy('id', 'ASC')->findAll();
        $totalBayar = $this->totalBayar($transaksiId);

        return view('admin/transaksi_sewa/detail', [
            'transaksi'   => $transaksi,
            'layanan'     => $this->transaksiLayananModel->where('transaksi_sewa_id', $transaksiId)->orderBy('id', 'ASC')->findAll(),
            'pembayaran'  => $pembayaran,
            'totalBayar'  => $totalBayar,
            'sisaTagihan' => max(0, (int) $transaksi['total_tagihan'] - $totalBayar),
            'userName'    => session()->get('userName'),
            'userEmail'   => session()->get('userEmail'),
            'userRole'    => session()->get('userRole'),
        ]);
    }

    public function store()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (! $this->validate($this->transactionRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tanggalSewa = (string) $this->request->getPost('tanggal_sewa');
        $tanggalKembali = (string) $this->request->getPost('tanggal_kembali');
        $kendaraanId = (int) $this->request->getPost('kendaraan_id');
        $pelanggan = $this->pelangganModel->find((int) $this->request->getPost('pelanggan_id'));

        if (! $pelanggan || $pelanggan['status'] !== 'aktif') {
            return redirect()->back()->withInput()->with('error', 'Pelanggan harus berstatus aktif.');
        }

        if (! $this->isDateRangeValid($tanggalSewa, $tanggalKembali)) {
            return redirect()->back()->withInput()->with('error', 'Tanggal kembali tidak boleh lebih kecil dari tanggal sewa.');
        }

        if (! $this->isKendaraanAvailable($kendaraanId, $tanggalSewa, $tanggalKembali)) {
            return redirect()->back()->withInput()->with('error', 'Kendaraan tidak tersedia pada rentang tanggal yang dipilih.');
        }

        $kendaraan = $this->kendaraanModel->find($kendaraanId);
        $tanggalDikembalikan = $this->normalizedTanggalDikembalikan((string) $this->request->getPost('status_transaksi'), (string) $this->request->getPost('tanggal_dikembalikan'));

        if ($tanggalDikembalikan !== null && $tanggalDikembalikan < $tanggalSewa) {
            return redirect()->back()->withInput()->with('error', 'Tanggal kendaraan dikembalikan tidak boleh lebih kecil dari tanggal sewa.');
        }

        $dendaSnapshot = $this->calculateDendaSnapshot($tanggalKembali, $tanggalDikembalikan);
        $lamaSewa = $this->calculateLamaSewa($tanggalSewa, $this->tanggalAkhirTagihan($tanggalKembali, $tanggalDikembalikan));
        $layananRows = $this->buildLayananRows();
        $totalLayanan = array_sum(array_column($layananRows, 'total_harga'));
        $hargaSewa = (int) $kendaraan['harga_sewa_per_hari'];
        $subtotalSewa = $lamaSewa * $hargaSewa;
        $totalTagihan = $subtotalSewa + $totalLayanan + $dendaSnapshot['denda'];
        $jumlahBayar = (int) $this->request->getPost('jumlah_bayar');

        if ($jumlahBayar > $totalTagihan) {
            return redirect()->back()->withInput()->with('error', 'Pembayaran pertama tidak boleh melebihi total tagihan.');
        }

        if ($jumlahBayar > 0 && ! $this->paymentFinanceCategory()) {
            return redirect()->back()->withInput()->with('error', 'Kategori Penyewaan Kendaraan tidak ditemukan. Pembayaran belum bisa disimpan.');
        }

        $db = db_connect();
        $db->transStart();

        $transaksiId = (int) $this->transaksiModel->insert([
            'kode_transaksi'       => $this->generateKodeTransaksi(),
            'pelanggan_id'         => $this->request->getPost('pelanggan_id'),
            'kendaraan_id'             => $kendaraanId,
            'tanggal_transaksi'    => $this->request->getPost('tanggal_transaksi'),
            'tanggal_sewa'         => $tanggalSewa,
            'tanggal_kembali'      => $tanggalKembali,
            'tanggal_dikembalikan' => $tanggalDikembalikan,
            'hari_terlambat'       => $dendaSnapshot['hari_terlambat'],
            'harga_denda_per_hari' => $dendaSnapshot['harga_denda_per_hari'],
            'lama_sewa'            => $lamaSewa,
            'harga_sewa_per_hari'  => $hargaSewa,
            'subtotal_sewa'        => $subtotalSewa,
            'total_layanan'        => $totalLayanan,
            'denda'                => $dendaSnapshot['denda'],
            'total_tagihan'        => $totalTagihan,
            'catatan'              => $this->request->getPost('catatan'),
            'status_transaksi'     => $this->request->getPost('status_transaksi'),
            'status_pembayaran'    => $this->paymentStatus($totalTagihan, $jumlahBayar),
        ]);

        $this->saveLayananRows($transaksiId, $layananRows);

        if ($jumlahBayar > 0) {
            $paymentData = [
                'transaksi_sewa_id'  => $transaksiId,
                'tanggal_pembayaran' => $this->request->getPost('tanggal_transaksi'),
                'jumlah_bayar'       => $jumlahBayar,
                'metode_pembayaran'  => $this->request->getPost('metode_pembayaran'),
                'catatan'            => 'Pembayaran pertama',
            ];

            $pembayaranId = (int) $this->pembayaranModel->insert($paymentData);
            $transaksiKeuanganId = $this->createPaymentFinanceTransaction($transaksiId, $paymentData);

            if ($transaksiKeuanganId > 0) {
                $this->pembayaranModel->update($pembayaranId, [
                    'transaksi_keuangan_id' => $transaksiKeuanganId,
                ]);
            }
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Transaksi sewa gagal ditambahkan.');
        }

        return redirect()->to('/admin/transaksi-sewa')->with('success', 'Transaksi sewa berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $transaksi = $this->transaksiModel->find($id);

        if (! $transaksi) {
            return redirect()->to('/admin/transaksi-sewa')->with('error', 'Data transaksi tidak ditemukan.');
        }

        return view('admin/transaksi_sewa/edit', array_merge($this->formData(), [
            'transaksi'       => $transaksi,
            'layananTerpilih' => $this->transaksiLayananModel->where('transaksi_sewa_id', $id)->findAll(),
            'kendaraanOptions'    => $this->availableKendaraan($transaksi['tanggal_sewa'], $transaksi['tanggal_kembali'], $id, (int) $transaksi['kendaraan_id']),
            'hargaDendaPerHari' => $this->hargaDendaUntukForm($transaksi),
        ]));
    }

    public function update(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $transaksi = $this->transaksiModel->find($id);

        if (! $transaksi) {
            return redirect()->to('/admin/transaksi-sewa')->with('error', 'Data transaksi tidak ditemukan.');
        }

        if (! $this->validate($this->transactionRules(false))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tanggalSewa = (string) $this->request->getPost('tanggal_sewa');
        $tanggalKembali = (string) $this->request->getPost('tanggal_kembali');
        $kendaraanId = (int) $this->request->getPost('kendaraan_id');
        $pelanggan = $this->pelangganModel->find((int) $this->request->getPost('pelanggan_id'));

        if (! $pelanggan || $pelanggan['status'] !== 'aktif') {
            return redirect()->back()->withInput()->with('error', 'Pelanggan harus berstatus aktif.');
        }

        if (! $this->isDateRangeValid($tanggalSewa, $tanggalKembali)) {
            return redirect()->back()->withInput()->with('error', 'Tanggal kembali tidak boleh lebih kecil dari tanggal sewa.');
        }

        if (! $this->isKendaraanAvailable($kendaraanId, $tanggalSewa, $tanggalKembali, $id)) {
            return redirect()->back()->withInput()->with('error', 'Kendaraan tidak tersedia pada rentang tanggal yang dipilih.');
        }

        $kendaraan = $this->kendaraanModel->find($kendaraanId);
        $tanggalDikembalikan = $this->normalizedTanggalDikembalikan((string) $this->request->getPost('status_transaksi'), (string) $this->request->getPost('tanggal_dikembalikan'));

        if ($tanggalDikembalikan !== null && $tanggalDikembalikan < $tanggalSewa) {
            return redirect()->back()->withInput()->with('error', 'Tanggal kendaraan dikembalikan tidak boleh lebih kecil dari tanggal sewa.');
        }

        $dendaSnapshot = $this->calculateDendaSnapshot($tanggalKembali, $tanggalDikembalikan, $transaksi);
        $lamaSewa = $this->calculateLamaSewa($tanggalSewa, $this->tanggalAkhirTagihan($tanggalKembali, $tanggalDikembalikan));
        $layananRows = $this->buildLayananRows();
        $totalLayanan = array_sum(array_column($layananRows, 'total_harga'));
        $hargaSewa = (int) $kendaraan['harga_sewa_per_hari'];
        $subtotalSewa = $lamaSewa * $hargaSewa;
        $totalTagihan = $subtotalSewa + $totalLayanan + $dendaSnapshot['denda'];
        $totalBayar = $this->totalBayar($id);

        if ($totalBayar > $totalTagihan) {
            return redirect()->back()->withInput()->with('error', 'Total tagihan tidak boleh lebih kecil dari total pembayaran yang sudah tercatat.');
        }

        $db = db_connect();
        $db->transStart();

        $this->transaksiModel->update($id, [
            'pelanggan_id'        => $this->request->getPost('pelanggan_id'),
            'kendaraan_id'            => $kendaraanId,
            'tanggal_transaksi'   => $this->request->getPost('tanggal_transaksi'),
            'tanggal_sewa'        => $tanggalSewa,
            'tanggal_kembali'     => $tanggalKembali,
            'tanggal_dikembalikan' => $tanggalDikembalikan,
            'hari_terlambat'      => $dendaSnapshot['hari_terlambat'],
            'harga_denda_per_hari' => $dendaSnapshot['harga_denda_per_hari'],
            'lama_sewa'           => $lamaSewa,
            'harga_sewa_per_hari' => $hargaSewa,
            'subtotal_sewa'       => $subtotalSewa,
            'total_layanan'       => $totalLayanan,
            'denda'               => $dendaSnapshot['denda'],
            'total_tagihan'       => $totalTagihan,
            'catatan'             => $this->request->getPost('catatan'),
            'status_transaksi'    => $this->request->getPost('status_transaksi'),
        ]);

        $this->transaksiLayananModel->where('transaksi_sewa_id', $id)->delete();
        $this->saveLayananRows($id, $layananRows);
        $this->syncPaymentStatus($id);

        $db->transComplete();

        return redirect()->to('/admin/transaksi-sewa')->with('success', 'Transaksi sewa berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if (! $this->transaksiModel->find($id)) {
            return redirect()->to('/admin/transaksi-sewa')->with('error', 'Data transaksi tidak ditemukan.');
        }

        $pembayaranRows = $this->pembayaranModel->where('transaksi_sewa_id', $id)->findAll();

        $db = db_connect();
        $db->transStart();

        foreach ($pembayaranRows as $pembayaran) {
            if (! empty($pembayaran['transaksi_keuangan_id'])) {
                $this->transaksiKeuanganModel->delete((int) $pembayaran['transaksi_keuangan_id']);
            }
        }

        $this->transaksiModel->delete($id);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to('/admin/transaksi-sewa')->with('error', 'Transaksi sewa gagal dihapus.');
        }

        return redirect()->to('/admin/transaksi-sewa')->with('success', 'Transaksi sewa berhasil dihapus.');
    }

    public function availableKendaraanJson(): ResponseInterface
    {
        if (! $this->isLoggedIn()) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Sesi login tidak valid.']);
        }

        return $this->response->setJSON([
            'kendaraan' => $this->availableKendaraan(
                (string) $this->request->getGet('tanggal_sewa'),
                (string) $this->request->getGet('tanggal_kembali'),
                (int) $this->request->getGet('exclude_id'),
                (int) $this->request->getGet('selected_kendaraan_id')
            ),
        ]);
    }

    public function pembayaran(int $transaksiId)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $transaksi = $this->findTransaksiDetail($transaksiId);

        if (! $transaksi) {
            return redirect()->to('/admin/transaksi-sewa')->with('error', 'Data transaksi tidak ditemukan.');
        }

        return view('admin/transaksi_sewa/pembayaran', [
            'transaksi'               => $transaksi,
            'pembayaran'              => $this->pembayaranModel->where('transaksi_sewa_id', $transaksiId)->orderBy('id', 'asc')->findAll(),
            'totalBayar'              => $this->totalBayar($transaksiId),
            'metodePembayaranOptions' => $this->metodePembayaranOptions,
            'userName'                => session()->get('userName'),
            'userEmail'               => session()->get('userEmail'),
            'userRole'                => session()->get('userRole'),
        ]);
    }

    public function kuitansiPdf(int $transaksiId, int $pembayaranId)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $transaksi = $this->findTransaksiDetail($transaksiId);
        $pembayaran = $this->pembayaranModel->where('transaksi_sewa_id', $transaksiId)->find($pembayaranId);

        if (! $transaksi || ! $pembayaran) {
            return redirect()->to('/admin/transaksi-sewa/pembayaran/' . $transaksiId)->with('error', 'Data kuitansi tidak ditemukan.');
        }

        $html = view('admin/transaksi_sewa/kuitansi_pdf', [
            'transaksi'  => $transaksi,
            'pembayaran' => $pembayaran,
            'petugas'    => session()->get('userName') ?? '-',
        ]);

        return $this->streamPdf($html, 'kuitansi-' . $transaksi['kode_transaksi'] . '-' . $pembayaran['id'] . '.pdf');
    }

    public function storePembayaran(int $transaksiId)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $transaksi = $this->transaksiModel->find($transaksiId);

        if (! $transaksi) {
            return redirect()->to('/admin/transaksi-sewa')->with('error', 'Data transaksi tidak ditemukan.');
        }

        if (! $this->validate($this->paymentRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $jumlahBayar = (int) $this->request->getPost('jumlah_bayar');
        $sisaTagihan = max(0, (int) $transaksi['total_tagihan'] - $this->totalBayar($transaksiId));

        if ($jumlahBayar > $sisaTagihan) {
            return redirect()->back()->withInput()->with('error', 'Jumlah pembayaran tidak boleh melebihi sisa tagihan.');
        }

        if (! $this->paymentFinanceCategory()) {
            return redirect()->back()->withInput()->with('error', 'Kategori Penyewaan Kendaraan tidak ditemukan. Pembayaran belum bisa disimpan.');
        }

        $paymentData = [
            'transaksi_sewa_id'  => $transaksiId,
            'tanggal_pembayaran' => $this->request->getPost('tanggal_pembayaran'),
            'jumlah_bayar'       => $jumlahBayar,
            'metode_pembayaran'  => $this->request->getPost('metode_pembayaran'),
            'catatan'            => $this->request->getPost('catatan'),
        ];

        $db = db_connect();
        $db->transStart();

        $pembayaranId = (int) $this->pembayaranModel->insert($paymentData);
        $transaksiKeuanganId = $this->createPaymentFinanceTransaction($transaksiId, $paymentData);

        if ($transaksiKeuanganId > 0) {
            $this->pembayaranModel->update($pembayaranId, [
                'transaksi_keuangan_id' => $transaksiKeuanganId,
            ]);
        }

        $this->syncPaymentStatus($transaksiId);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('error', 'Pembayaran gagal ditambahkan.');
        }

        return redirect()->to('/admin/transaksi-sewa/pembayaran/' . $transaksiId)->with('success', 'Pembayaran berhasil ditambahkan.');
    }

    public function deletePembayaran(int $transaksiId, int $pembayaranId)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $pembayaran = $this->pembayaranModel->where('transaksi_sewa_id', $transaksiId)->find($pembayaranId);

        if (! $pembayaran) {
            return redirect()->to('/admin/transaksi-sewa/pembayaran/' . $transaksiId)->with('error', 'Data pembayaran tidak ditemukan.');
        }

        $lastPembayaran = $this->pembayaranModel
            ->where('transaksi_sewa_id', $transaksiId)
            ->orderBy('id', 'DESC')
            ->first();

        if (! $lastPembayaran || (int) $lastPembayaran['id'] !== $pembayaranId) {
            return redirect()->to('/admin/transaksi-sewa/pembayaran/' . $transaksiId)->with('error', 'Hanya pembayaran terakhir yang boleh dihapus.');
        }

        $db = db_connect();
        $db->transStart();

        if (! empty($pembayaran['transaksi_keuangan_id'])) {
            $this->transaksiKeuanganModel->delete((int) $pembayaran['transaksi_keuangan_id']);
        }

        $this->pembayaranModel->delete($pembayaranId);
        $this->syncPaymentStatus($transaksiId);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to('/admin/transaksi-sewa/pembayaran/' . $transaksiId)->with('error', 'Pembayaran gagal dihapus.');
        }

        return redirect()->to('/admin/transaksi-sewa/pembayaran/' . $transaksiId)->with('success', 'Pembayaran berhasil dihapus.');
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('isLoggedIn');
    }

    private function formData(): array
    {
        return [
            'pelangganOptions'        => $this->pelangganModel->where('status', 'aktif')->orderBy('nama_lengkap', 'ASC')->findAll(),
            'layananOptions'          => $this->layananModel->orderBy('nama_layanan', 'ASC')->findAll(),
            'statusTransaksiOptions'  => $this->statusTransaksiOptions,
            'metodePembayaranOptions' => $this->metodePembayaranOptions,
            'hargaDendaPerHari'       => $this->hargaDendaPerHari(),
            'userName'                => session()->get('userName'),
            'userEmail'               => session()->get('userEmail'),
            'userRole'                => session()->get('userRole'),
        ];
    }

    private function laporanViewData(): array
    {
        return [
            'pelangganOptions'        => $this->pelangganModel->orderBy('nama_lengkap', 'ASC')->findAll(),
            'kendaraanOptions'        => $this->kendaraanModel->orderBy('nama_kendaraan', 'ASC')->findAll(),
            'statusTransaksiOptions'  => $this->statusTransaksiOptions,
            'statusPembayaranOptions' => $this->statusPembayaranOptions(),
            'userName'                => session()->get('userName'),
            'userEmail'               => session()->get('userEmail'),
            'userRole'                => session()->get('userRole'),
        ];
    }

    private function indexFilters(): array
    {
        return [
            'q'                 => trim((string) $this->request->getGet('q')),
            'tanggal_mulai'     => trim((string) $this->request->getGet('tanggal_mulai')),
            'tanggal_selesai'   => trim((string) $this->request->getGet('tanggal_selesai')),
            'periode_mulai'     => trim((string) $this->request->getGet('periode_mulai')),
            'periode_selesai'   => trim((string) $this->request->getGet('periode_selesai')),
            'pelanggan_id'      => trim((string) $this->request->getGet('pelanggan_id')),
            'kendaraan_id'      => trim((string) $this->request->getGet('kendaraan_id')),
            'status_transaksi'  => trim((string) $this->request->getGet('status_transaksi')),
            'status_pembayaran' => trim((string) $this->request->getGet('status_pembayaran')),
            'kondisi'           => trim((string) $this->request->getGet('kondisi')),
        ];
    }

    private function filterIndexKondisi(array $transaksi, string $kondisi): array
    {
        if ($kondisi === '') {
            return $transaksi;
        }

        return array_values(array_filter($transaksi, static function (array $row) use ($kondisi): bool {
            if ($kondisi === 'telat') {
                return (int) ($row['hari_telat_berjalan'] ?? 0) > 0;
            }

            if ($kondisi === 'jatuh_tempo') {
                return $row['status_transaksi'] === 'berjalan' && $row['tanggal_kembali'] === date('Y-m-d');
            }

            if ($kondisi === 'perlu_pembayaran') {
                return (int) ($row['sisa_tagihan'] ?? 0) > 0;
            }

            return true;
        }));
    }

    private function indexSummary(array $transaksi): array
    {
        return [
            'jumlah'        => count($transaksi),
            'total_tagihan' => array_sum(array_map(static fn ($row) => (int) $row['total_tagihan'], $transaksi)),
            'total_bayar'   => array_sum(array_map(static fn ($row) => (int) ($row['total_bayar'] ?? 0), $transaksi)),
            'sisa_tagihan'  => array_sum(array_map(static fn ($row) => (int) ($row['sisa_tagihan'] ?? 0), $transaksi)),
            'telat'         => count(array_filter($transaksi, static fn ($row) => (int) ($row['hari_telat_berjalan'] ?? 0) > 0)),
        ];
    }

    private function statusPembayaranOptions(): array
    {
        return [
            'belum_bayar' => 'Belum Bayar',
            'belum_lunas' => 'Belum Lunas',
            'lunas'       => 'Lunas',
        ];
    }

    private function laporanFilters(): array
    {
        return [
            'tanggal_mulai'      => trim((string) $this->request->getGet('tanggal_mulai')),
            'tanggal_selesai'    => trim((string) $this->request->getGet('tanggal_selesai')),
            'pelanggan_id'       => trim((string) $this->request->getGet('pelanggan_id')),
            'kendaraan_id'       => trim((string) $this->request->getGet('kendaraan_id')),
            'status_transaksi'   => trim((string) $this->request->getGet('status_transaksi')),
            'status_pembayaran'  => trim((string) $this->request->getGet('status_pembayaran')),
        ];
    }

    private function laporanTransaksi(array $filters): array
    {
        $builder = $this->transaksiModel
            ->select('transaksi_sewa.*, pelanggan.nama_lengkap, pelanggan.kode_pelanggan, pelanggan.no_hp, kendaraan.jenis_kendaraan, kendaraan.nama_kendaraan, kendaraan.plat_nomor')
            ->join('pelanggan', 'pelanggan.id = transaksi_sewa.pelanggan_id')
            ->join('kendaraan', 'kendaraan.id = transaksi_sewa.kendaraan_id')
            ->orderBy('transaksi_sewa.tanggal_transaksi', 'DESC')
            ->orderBy('transaksi_sewa.id', 'DESC');

        if ($filters['tanggal_mulai'] !== '') {
            $builder->where('transaksi_sewa.tanggal_transaksi >=', $filters['tanggal_mulai']);
        }

        if ($filters['tanggal_selesai'] !== '') {
            $builder->where('transaksi_sewa.tanggal_transaksi <=', $filters['tanggal_selesai']);
        }

        foreach (['pelanggan_id', 'kendaraan_id', 'status_transaksi', 'status_pembayaran'] as $field) {
            if ($filters[$field] !== '') {
                $builder->where('transaksi_sewa.' . $field, $filters[$field]);
            }
        }

        $transaksi = $builder->findAll();

        foreach ($transaksi as &$row) {
            $row['total_bayar'] = $this->totalBayar((int) $row['id']);
            $row['sisa_tagihan'] = max(0, (int) $row['total_tagihan'] - (int) $row['total_bayar']);
        }

        return $transaksi;
    }

    private function laporanSummary(array $transaksi): array
    {
        return [
            'jumlah_transaksi' => count($transaksi),
            'total_tagihan'    => array_sum(array_map(static fn ($row) => (int) $row['total_tagihan'], $transaksi)),
            'total_bayar'      => array_sum(array_map(static fn ($row) => (int) $row['total_bayar'], $transaksi)),
            'sisa_tagihan'     => array_sum(array_map(static fn ($row) => (int) $row['sisa_tagihan'], $transaksi)),
            'total_denda'      => array_sum(array_map(static fn ($row) => (int) ($row['denda'] ?? 0), $transaksi)),
        ];
    }

    private function transactionRules(bool $withPayment = true): array
    {
        $rules = [
            'pelanggan_id'       => 'required|is_not_unique[pelanggan.id]',
            'kendaraan_id'           => 'required|is_not_unique[kendaraan.id]',
            'tanggal_transaksi'  => 'required|valid_date[Y-m-d]',
            'tanggal_sewa'       => 'required|valid_date[Y-m-d]',
            'tanggal_kembali'    => 'required|valid_date[Y-m-d]',
            'tanggal_dikembalikan' => 'permit_empty|valid_date[Y-m-d]',
            'status_transaksi'   => 'required|in_list[booking,berjalan,selesai,batal]',
            'catatan'            => 'permit_empty',
        ];

        if ($withPayment) {
            $rules['jumlah_bayar'] = 'required|integer|greater_than_equal_to[0]';
            $rules['metode_pembayaran'] = 'required|in_list[tunai,qris,transfer]';
        }

        return $rules;
    }

    private function paymentRules(): array
    {
        return [
            'tanggal_pembayaran' => 'required|valid_date[Y-m-d]',
            'jumlah_bayar'       => 'required|integer|greater_than[0]',
            'metode_pembayaran'  => 'required|in_list[tunai,qris,transfer]',
            'catatan'            => 'permit_empty',
        ];
    }

    private function generateKodeTransaksi(): string
    {
        $lastTransaksi = $this->transaksiModel
            ->select('kode_transaksi')
            ->like('kode_transaksi', 'INV', 'after')
            ->orderBy('id', 'DESC')
            ->first();

        $lastNumber = 0;

        if ($lastTransaksi && preg_match('/^INV(\d+)$/', (string) $lastTransaksi['kode_transaksi'], $matches)) {
            $lastNumber = (int) $matches[1];
        }

        return 'INV' . str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
    }

    private function availableKendaraan(?string $tanggalSewa = null, ?string $tanggalKembali = null, int $excludeTransaksiId = 0, int $selectedKendaraanId = 0): array
    {
        $kendaraanRows = $this->kendaraanModel->where('status', 'ready')->orderBy('nama_kendaraan', 'ASC')->findAll();

        if ($selectedKendaraanId > 0 && ! in_array($selectedKendaraanId, array_map(static fn ($row) => (int) $row['id'], $kendaraanRows), true)) {
            $selectedKendaraan = $this->kendaraanModel->find($selectedKendaraanId);

            if ($selectedKendaraan) {
                $kendaraanRows[] = $selectedKendaraan;
            }
        }

        if (! $tanggalSewa || ! $tanggalKembali || ! $this->isDateRangeValid($tanggalSewa, $tanggalKembali)) {
            return $kendaraanRows;
        }

        return array_values(array_filter($kendaraanRows, function (array $kendaraan) use ($tanggalSewa, $tanggalKembali, $excludeTransaksiId, $selectedKendaraanId): bool {
            return (int) $kendaraan['id'] === $selectedKendaraanId || $this->isKendaraanAvailable((int) $kendaraan['id'], $tanggalSewa, $tanggalKembali, $excludeTransaksiId);
        }));
    }

    private function isKendaraanAvailable(int $kendaraanId, string $tanggalSewa, string $tanggalKembali, int $excludeTransaksiId = 0): bool
    {
        $kendaraan = $this->kendaraanModel->find($kendaraanId);

        if (! $kendaraan || $kendaraan['status'] !== 'ready') {
            if ($excludeTransaksiId <= 0) {
                return false;
            }

            $currentTransaksi = $this->transaksiModel->find($excludeTransaksiId);

            if (! $currentTransaksi || (int) $currentTransaksi['kendaraan_id'] !== $kendaraanId) {
                return false;
            }
        }

        $builder = $this->transaksiModel
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
            ->groupEnd();

        if ($excludeTransaksiId > 0) {
            $builder->where('id !=', $excludeTransaksiId);
        }

        return $builder->countAllResults() === 0;
    }

    private function isDateRangeValid(string $tanggalSewa, string $tanggalKembali): bool
    {
        return $tanggalSewa !== '' && $tanggalKembali !== '' && $tanggalKembali >= $tanggalSewa;
    }

    private function calculateLamaSewa(string $tanggalSewa, string $tanggalKembali): int
    {
        $start = new DateTime($tanggalSewa);
        $end = new DateTime($tanggalKembali);

        return max(1, $start->diff($end)->days + 1);
    }

    private function tanggalAkhirTagihan(string $tanggalKembali, ?string $tanggalDikembalikan): string
    {
        if ($tanggalDikembalikan && $tanggalDikembalikan > $tanggalKembali) {
            return $tanggalDikembalikan;
        }

        return $tanggalKembali;
    }

    private function buildLayananRows(): array
    {
        $layananIds = $this->request->getPost('layanan_id') ?? [];
        $qtyRows = $this->request->getPost('layanan_qty') ?? [];
        $rows = [];

        foreach ($layananIds as $index => $layananId) {
            $layananId = (int) $layananId;
            $qty = max(0, (int) ($qtyRows[$index] ?? 0));

            if ($layananId <= 0 || $qty <= 0) {
                continue;
            }

            $layanan = $this->layananModel->find($layananId);

            if (! $layanan) {
                continue;
            }

            $harga = (int) $layanan['harga_layanan'];
            $rows[] = [
                'layanan_tambahan_id' => $layananId,
                'nama_layanan'        => $layanan['nama_layanan'],
                'harga_layanan'       => $harga,
                'qty'                 => $qty,
                'total_harga'         => $harga * $qty,
            ];
        }

        return $rows;
    }

    private function saveLayananRows(int $transaksiId, array $rows): void
    {
        foreach ($rows as $row) {
            $row['transaksi_sewa_id'] = $transaksiId;
            $this->transaksiLayananModel->insert($row);
        }
    }

    private function paymentStatus(int $totalTagihan, int $totalBayar): string
    {
        if ($totalBayar <= 0) {
            return 'belum_bayar';
        }

        if ($totalBayar >= $totalTagihan) {
            return 'lunas';
        }

        return 'belum_lunas';
    }

    private function normalizedTanggalDikembalikan(string $statusTransaksi, string $tanggalDikembalikan): ?string
    {
        if ($statusTransaksi !== 'selesai') {
            return null;
        }

        return $tanggalDikembalikan !== '' ? $tanggalDikembalikan : date('Y-m-d');
    }

    private function calculateDendaSnapshot(string $tanggalKembali, ?string $tanggalDikembalikan, ?array $existingTransaksi = null): array
    {
        $hargaDenda = $tanggalDikembalikan ? $this->hargaDendaSnapshot($existingTransaksi) : 0;
        $hariTerlambat = 0;

        if ($tanggalDikembalikan && $tanggalDikembalikan > $tanggalKembali) {
            $jatuhTempo = new DateTime($tanggalKembali);
            $dikembalikan = new DateTime($tanggalDikembalikan);
            $hariTerlambat = max(0, $jatuhTempo->diff($dikembalikan)->days);
        }

        return [
            'hari_terlambat'       => $hariTerlambat,
            'harga_denda_per_hari' => $hargaDenda,
            'denda'                => $hariTerlambat * $hargaDenda,
        ];
    }

    private function hargaDendaSnapshot(?array $existingTransaksi = null): int
    {
        if ($existingTransaksi && ! empty($existingTransaksi['tanggal_dikembalikan'])) {
            return (int) ($existingTransaksi['harga_denda_per_hari'] ?? 0);
        }

        return $this->hargaDendaPerHari();
    }

    private function hargaDendaUntukForm(array $transaksi): int
    {
        if (! empty($transaksi['tanggal_dikembalikan'])) {
            return (int) ($transaksi['harga_denda_per_hari'] ?? 0);
        }

        return $this->hargaDendaPerHari();
    }

    private function hargaDendaPerHari(): int
    {
        $setting = $this->settingModel->find(1);

        return (int) ($setting['harga_denda_per_hari'] ?? 0);
    }

    private function totalBayar(int $transaksiId): int
    {
        return (int) ($this->pembayaranModel
            ->selectSum('jumlah_bayar', 'total_bayar')
            ->where('transaksi_sewa_id', $transaksiId)
            ->first()['total_bayar'] ?? 0);
    }

    private function syncPaymentStatus(int $transaksiId): void
    {
        $transaksi = $this->transaksiModel->find($transaksiId);

        if (! $transaksi) {
            return;
        }

        $this->transaksiModel->update($transaksiId, [
            'status_pembayaran' => $this->paymentStatus((int) $transaksi['total_tagihan'], $this->totalBayar($transaksiId)),
        ]);
    }

    private function paymentFinanceCategory(): ?array
    {
        return $this->kategoriKeuanganModel->find(self::PAYMENT_FINANCE_CATEGORY_ID);
    }

    private function createPaymentFinanceTransaction(int $transaksiId, array $paymentData): int
    {
        $kategori = $this->paymentFinanceCategory();

        if (! $kategori) {
            return 0;
        }

        $transaksi = $this->findTransaksiDetail($transaksiId) ?? $this->transaksiModel->find($transaksiId);
        $kodeTransaksi = (string) ($transaksi['kode_transaksi'] ?? ('#' . $transaksiId));
        $namaPelanggan = trim((string) ($transaksi['nama_lengkap'] ?? ''));
        $catatan = 'Pembayaran sewa ' . $kodeTransaksi;

        if ($namaPelanggan !== '') {
            $catatan .= ' - ' . $namaPelanggan;
        }

        if (! empty($paymentData['catatan'])) {
            $catatan .= '. ' . trim((string) $paymentData['catatan']);
        }

        return (int) $this->transaksiKeuanganModel->insert([
            'tanggal'              => $paymentData['tanggal_pembayaran'],
            'jenis'                => 'Pemasukan',
            'kategori_keuangan_id' => self::PAYMENT_FINANCE_CATEGORY_ID,
            'kategori'             => $kategori['kategori'],
            'nominal'              => (int) $paymentData['jumlah_bayar'],
            'catatan'              => $catatan,
        ]);
    }

    private function findTransaksiDetail(int $transaksiId): ?array
    {
        return $this->transaksiModel
            ->select('transaksi_sewa.*, pelanggan.nama_lengkap, pelanggan.kode_pelanggan, pelanggan.no_hp, pelanggan.alamat, kendaraan.jenis_kendaraan, kendaraan.nama_kendaraan, kendaraan.plat_nomor, kendaraan.merk, kendaraan.tahun, kendaraan.warna')
            ->join('pelanggan', 'pelanggan.id = transaksi_sewa.pelanggan_id')
            ->join('kendaraan', 'kendaraan.id = transaksi_sewa.kendaraan_id')
            ->where('transaksi_sewa.id', $transaksiId)
            ->first();
    }

    private function streamPdf(string $html, string $fileName, string $paper = 'A4', string $orientation = 'portrait'): ResponseInterface
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper($paper, $orientation);
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="' . $fileName . '"')
            ->setBody($dompdf->output());
    }
}
