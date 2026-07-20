<?php

namespace App\Controllers;

use App\Models\KategoriKeuanganModel;
use App\Models\PembayaranSewaModel;
use App\Models\TransaksiKeuanganModel;
use CodeIgniter\HTTP\ResponseInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

class TransaksiKeuanganController extends BaseController
{
    private TransaksiKeuanganModel $transaksiKeuanganModel;
    private KategoriKeuanganModel $kategoriKeuanganModel;
    private PembayaranSewaModel $pembayaranSewaModel;

    private array $jenisOptions = [
        'Pemasukan',
        'Pengeluaran',
    ];

    public function __construct()
    {
        $this->transaksiKeuanganModel = new TransaksiKeuanganModel();
        $this->kategoriKeuanganModel = new KategoriKeuanganModel();
        $this->pembayaranSewaModel = new PembayaranSewaModel();
    }

    public function index()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard', 'Akses ditolak. Modul keuangan hanya dapat diakses oleh administrator.')) {
            return $redirect;
        }

        $transaksiKeuangan = $this->transaksiKeuanganModel
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('admin/transaksi_keuangan/index', [
            'transaksiKeuangan' => $transaksiKeuangan,
            'summary'           => $this->summary($transaksiKeuangan),
            'userName'          => session()->get('userName'),
            'userEmail'         => session()->get('userEmail'),
            'userRole'          => session()->get('userRole'),
        ]);
    }

    public function create()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard', 'Akses ditolak. Modul keuangan hanya dapat diakses oleh administrator.')) {
            return $redirect;
        }

        return view('admin/transaksi_keuangan/create', $this->formData());
    }

    public function laporan()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard', 'Akses ditolak. Laporan keuangan hanya dapat diakses oleh administrator.')) {
            return $redirect;
        }

        $filters = $this->laporanFilters();
        $transaksi = $this->laporanRows($filters);

        return view('admin/transaksi_keuangan/laporan', [
            'filters'         => $filters,
            'transaksi'       => $transaksi,
            'summary'         => $this->summary($transaksi),
            'kategoriSummary' => $this->kategoriSummary($transaksi),
            'jenisOptions'    => $this->jenisOptions,
            'kategoriOptions' => $this->kategoriKeuanganModel->orderBy('kategori', 'ASC')->findAll(),
            'userName'        => session()->get('userName'),
            'userEmail'       => session()->get('userEmail'),
            'userRole'        => session()->get('userRole'),
        ]);
    }

    public function laporanPdf(): ResponseInterface
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard', 'Akses ditolak. Laporan keuangan hanya dapat diakses oleh administrator.')) {
            return $redirect;
        }

        $filters = $this->laporanFilters();
        $transaksi = $this->laporanRows($filters);
        $html = view('admin/transaksi_keuangan/laporan_pdf', [
            'filters'         => $filters,
            'transaksi'       => $transaksi,
            'summary'         => $this->summary($transaksi),
            'kategoriSummary' => $this->kategoriSummary($transaksi),
            'petugas'         => session()->get('userName') ?? '-',
        ]);

        return $this->streamPdf($html, 'laporan-keuangan.pdf', 'A4', 'portrait');
    }

    public function store()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard', 'Akses ditolak. Modul keuangan hanya dapat diakses oleh administrator.')) {
            return $redirect;
        }

        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kategori = $this->kategoriKeuanganModel->find((int) $this->request->getPost('kategori_keuangan_id'));

        if (! $kategori) {
            return redirect()->back()->withInput()->with('error', 'Kategori keuangan tidak ditemukan.');
        }

        $this->transaksiKeuanganModel->insert([
            'tanggal'               => $this->request->getPost('tanggal'),
            'jenis'                 => $this->request->getPost('jenis'),
            'kategori_keuangan_id'  => $kategori['id'],
            'kategori'              => $kategori['kategori'],
            'nominal'               => $this->request->getPost('nominal'),
            'catatan'               => $this->request->getPost('catatan'),
        ]);

        return redirect()->to('/admin/transaksi-keuangan')->with('success', 'Transaksi keuangan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard', 'Akses ditolak. Modul keuangan hanya dapat diakses oleh administrator.')) {
            return $redirect;
        }

        $transaksi = $this->transaksiKeuanganModel->find($id);

        if (! $transaksi) {
            return redirect()->to('/admin/transaksi-keuangan')->with('error', 'Transaksi keuangan tidak ditemukan.');
        }

        if ($this->isFromPembayaranSewa($id)) {
            return redirect()->to('/admin/transaksi-keuangan')->with('error', 'Transaksi keuangan dari pembayaran sewa tidak bisa diedit langsung.');
        }

        return view('admin/transaksi_keuangan/edit', array_merge($this->formData(), [
            'transaksi' => $transaksi,
        ]));
    }

    public function update(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard', 'Akses ditolak. Modul keuangan hanya dapat diakses oleh administrator.')) {
            return $redirect;
        }

        $transaksi = $this->transaksiKeuanganModel->find($id);

        if (! $transaksi) {
            return redirect()->to('/admin/transaksi-keuangan')->with('error', 'Transaksi keuangan tidak ditemukan.');
        }

        if ($this->isFromPembayaranSewa($id)) {
            return redirect()->to('/admin/transaksi-keuangan')->with('error', 'Transaksi keuangan dari pembayaran sewa tidak bisa diedit langsung.');
        }

        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $kategori = $this->kategoriKeuanganModel->find((int) $this->request->getPost('kategori_keuangan_id'));

        if (! $kategori) {
            return redirect()->back()->withInput()->with('error', 'Kategori keuangan tidak ditemukan.');
        }

        $this->transaksiKeuanganModel->update($id, [
            'tanggal'               => $this->request->getPost('tanggal'),
            'jenis'                 => $this->request->getPost('jenis'),
            'kategori_keuangan_id'  => $kategori['id'],
            'kategori'              => $kategori['kategori'],
            'nominal'               => $this->request->getPost('nominal'),
            'catatan'               => $this->request->getPost('catatan'),
        ]);

        return redirect()->to('/admin/transaksi-keuangan')->with('success', 'Transaksi keuangan berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard', 'Akses ditolak. Modul keuangan hanya dapat diakses oleh administrator.')) {
            return $redirect;
        }

        if (! $this->transaksiKeuanganModel->find($id)) {
            return redirect()->to('/admin/transaksi-keuangan')->with('error', 'Transaksi keuangan tidak ditemukan.');
        }

        if ($this->isFromPembayaranSewa($id)) {
            return redirect()->to('/admin/transaksi-keuangan')->with('error', 'Transaksi keuangan dari pembayaran sewa tidak bisa dihapus langsung. Hapus pembayaran terakhir dari menu pembayaran sewa.');
        }

        $this->transaksiKeuanganModel->delete($id);

        return redirect()->to('/admin/transaksi-keuangan')->with('success', 'Transaksi keuangan berhasil dihapus.');
    }

    private function formData(): array
    {
        return [
            'jenisOptions'     => $this->jenisOptions,
            'kategoriOptions'  => $this->kategoriKeuanganModel->orderBy('kategori', 'ASC')->findAll(),
            'userName'         => session()->get('userName'),
            'userEmail'        => session()->get('userEmail'),
            'userRole'         => session()->get('userRole'),
        ];
    }

    private function validationRules(): array
    {
        return [
            'tanggal'              => 'required|valid_date[Y-m-d]',
            'jenis'                => 'required|in_list[Pemasukan,Pengeluaran]',
            'kategori_keuangan_id' => 'required|is_not_unique[kategori_keuangan.id]',
            'nominal'              => 'required|integer|greater_than[0]',
            'catatan'              => 'permit_empty',
        ];
    }

    private function laporanFilters(): array
    {
        return [
            'tanggal_mulai'      => trim((string) ($this->request->getGet('tanggal_mulai') ?: date('Y-m-01'))),
            'tanggal_selesai'    => trim((string) ($this->request->getGet('tanggal_selesai') ?: date('Y-m-d'))),
            'jenis'              => trim((string) $this->request->getGet('jenis')),
            'kategori_keuangan_id' => trim((string) $this->request->getGet('kategori_keuangan_id')),
            'q'                  => trim((string) $this->request->getGet('q')),
        ];
    }

    private function laporanRows(array $filters): array
    {
        $builder = $this->transaksiKeuanganModel
            ->orderBy('tanggal', 'ASC')
            ->orderBy('id', 'ASC');

        if ($filters['tanggal_mulai'] !== '') {
            $builder->where('tanggal >=', $filters['tanggal_mulai']);
        }

        if ($filters['tanggal_selesai'] !== '') {
            $builder->where('tanggal <=', $filters['tanggal_selesai']);
        }

        if ($filters['jenis'] !== '') {
            $builder->where('jenis', $filters['jenis']);
        }

        if ($filters['kategori_keuangan_id'] !== '') {
            $builder->where('kategori_keuangan_id', $filters['kategori_keuangan_id']);
        }

        if ($filters['q'] !== '') {
            $builder
                ->groupStart()
                    ->like('kategori', $filters['q'])
                    ->orLike('catatan', $filters['q'])
                ->groupEnd();
        }

        return $builder->findAll();
    }

    private function summary(array $rows): array
    {
        $pemasukan = 0;
        $pengeluaran = 0;

        foreach ($rows as $row) {
            if ($row['jenis'] === 'Pemasukan') {
                $pemasukan += (int) $row['nominal'];
            } else {
                $pengeluaran += (int) $row['nominal'];
            }
        }

        return [
            'jumlah_transaksi' => count($rows),
            'pemasukan'   => $pemasukan,
            'pengeluaran' => $pengeluaran,
            'saldo'       => $pemasukan - $pengeluaran,
        ];
    }

    private function kategoriSummary(array $rows): array
    {
        $summary = [];

        foreach ($rows as $row) {
            $key = $row['jenis'] . '|' . $row['kategori'];

            if (! isset($summary[$key])) {
                $summary[$key] = [
                    'jenis'    => $row['jenis'],
                    'kategori' => $row['kategori'],
                    'total'    => 0,
                    'jumlah'   => 0,
                ];
            }

            $summary[$key]['total'] += (int) $row['nominal'];
            $summary[$key]['jumlah']++;
        }

        usort($summary, static function (array $a, array $b): int {
            if ($a['jenis'] === $b['jenis']) {
                return $b['total'] <=> $a['total'];
            }

            return $a['jenis'] <=> $b['jenis'];
        });

        return $summary;
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('isLoggedIn');
    }

    private function isFromPembayaranSewa(int $transaksiKeuanganId): bool
    {
        return $this->pembayaranSewaModel
            ->where('transaksi_keuangan_id', $transaksiKeuanganId)
            ->countAllResults() > 0;
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
