<?php

namespace App\Controllers;

class AdminController extends BaseController
{
    public function dashboard()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $canViewFinancials = $this->isAdministratorRole();

        return view('admin/dashboard', [
            'canViewFinancials' => $canViewFinancials,
            'summary'           => $this->dashboardSummary($canViewFinancials),
            'monthlyChart'      => $canViewFinancials ? $this->monthlyFinanceChart() : ['labels' => [], 'pemasukan' => [], 'pengeluaran' => []],
            'transactionChart'  => $this->transactionStatusChart(),
            'fleetChart'        => $this->fleetStatusChart(),
            'availabilityToday' => $this->vehicleAvailabilityToday(),
            'dueReturns'        => $this->dueReturns(),
            'recentTransaksi'   => $this->recentTransaksi(),
            'userName'          => session()->get('userName'),
            'userEmail'         => session()->get('userEmail'),
            'userRole'          => session()->get('userRole'),
        ]);
    }

    private function dashboardSummary(bool $includeFinancials = true): array
    {
        $db = db_connect();
        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        $pelangganAktif = (int) $db->table('pelanggan')->where('status', 'aktif')->countAllResults();
        $kendaraanTotal = (int) $db->table('kendaraan')->countAllResults();
        $kendaraanReady = (int) $db->table('kendaraan')->where('status', 'ready')->countAllResults();
        $kendaraanMaintenance = (int) $db->table('kendaraan')->where('status', 'maintenance')->countAllResults();
        $transaksiBerjalan = (int) $db->table('transaksi_sewa')->where('status_transaksi', 'berjalan')->countAllResults();
        $transaksiTelat = (int) $db->table('transaksi_sewa')
            ->where('status_transaksi', 'berjalan')
            ->where('tanggal_kembali <', $today)
            ->countAllResults();
        $bookingMendatang = (int) $db->table('transaksi_sewa')
            ->where('status_transaksi', 'booking')
            ->where('tanggal_sewa >=', $today)
            ->where('tanggal_sewa <=', date('Y-m-d', strtotime('+7 days')))
            ->countAllResults();

        $pemasukanBulanIni = 0;
        $pengeluaranBulanIni = 0;
        $piutang = 0;

        if ($includeFinancials) {
            $pemasukanBulanIni = (int) ($db->table('transaksi_keuangan')
                ->selectSum('nominal', 'total')
                ->where('jenis', 'Pemasukan')
                ->where('tanggal >=', $monthStart)
                ->where('tanggal <=', $monthEnd)
                ->get()
                ->getRowArray()['total'] ?? 0);

            $pengeluaranBulanIni = (int) ($db->table('transaksi_keuangan')
                ->selectSum('nominal', 'total')
                ->where('jenis', 'Pengeluaran')
                ->where('tanggal >=', $monthStart)
                ->where('tanggal <=', $monthEnd)
                ->get()
                ->getRowArray()['total'] ?? 0);

            $piutang = (int) ($db->query("
                SELECT SUM(GREATEST(transaksi_sewa.total_tagihan - COALESCE(pembayaran.total_bayar, 0), 0)) AS total
                FROM transaksi_sewa
                LEFT JOIN (
                    SELECT transaksi_sewa_id, SUM(jumlah_bayar) AS total_bayar
                    FROM pembayaran_sewa
                    GROUP BY transaksi_sewa_id
                ) pembayaran ON pembayaran.transaksi_sewa_id = transaksi_sewa.id
                WHERE transaksi_sewa.status_pembayaran <> 'lunas'
            ")->getRowArray()['total'] ?? 0);
        }

        return [
            'pelanggan_aktif'        => $pelangganAktif,
            'kendaraan_total'        => $kendaraanTotal,
            'kendaraan_ready'        => $kendaraanReady,
            'kendaraan_maintenance'  => $kendaraanMaintenance,
            'transaksi_berjalan'     => $transaksiBerjalan,
            'transaksi_telat'        => $transaksiTelat,
            'booking_mendatang'      => $bookingMendatang,
            'pemasukan_bulan_ini'    => $pemasukanBulanIni,
            'pengeluaran_bulan_ini'  => $pengeluaranBulanIni,
            'saldo_bulan_ini'        => $pemasukanBulanIni - $pengeluaranBulanIni,
            'piutang'                => $piutang,
        ];
    }

    private function monthlyFinanceChart(): array
    {
        $db = db_connect();
        $months = [];

        for ($i = 5; $i >= 0; $i--) {
            $key = date('Y-m', strtotime('-' . $i . ' months'));
            $months[$key] = [
                'label'       => date('M Y', strtotime($key . '-01')),
                'pemasukan'   => 0,
                'pengeluaran' => 0,
            ];
        }

        $rows = $db->query("
            SELECT DATE_FORMAT(tanggal, '%Y-%m') AS bulan, jenis, SUM(nominal) AS total
            FROM transaksi_keuangan
            WHERE tanggal >= ?
            GROUP BY DATE_FORMAT(tanggal, '%Y-%m'), jenis
        ", [array_key_first($months) . '-01'])->getResultArray();

        foreach ($rows as $row) {
            if (! isset($months[$row['bulan']])) {
                continue;
            }

            if ($row['jenis'] === 'Pemasukan') {
                $months[$row['bulan']]['pemasukan'] = (int) $row['total'];
            } elseif ($row['jenis'] === 'Pengeluaran') {
                $months[$row['bulan']]['pengeluaran'] = (int) $row['total'];
            }
        }

        return [
            'labels'      => array_column($months, 'label'),
            'pemasukan'   => array_column($months, 'pemasukan'),
            'pengeluaran' => array_column($months, 'pengeluaran'),
        ];
    }

    private function transactionStatusChart(): array
    {
        $db = db_connect();
        $labels = [
            'booking'  => 'Booking',
            'berjalan' => 'Berjalan',
            'selesai'  => 'Selesai',
            'batal'    => 'Batal',
        ];
        $values = array_fill_keys(array_keys($labels), 0);
        $rows = $db->table('transaksi_sewa')
            ->select('status_transaksi, COUNT(*) AS total')
            ->groupBy('status_transaksi')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            if (isset($values[$row['status_transaksi']])) {
                $values[$row['status_transaksi']] = (int) $row['total'];
            }
        }

        return [
            'labels' => array_values($labels),
            'values' => array_values($values),
        ];
    }

    private function fleetStatusChart(): array
    {
        $db = db_connect();
        $labels = [
            'ready'       => 'Ready',
            'maintenance' => 'Maintenance',
            'nonaktif'    => 'Nonaktif',
        ];
        $values = array_fill_keys(array_keys($labels), 0);
        $rows = $db->table('kendaraan')
            ->select('status, COUNT(*) AS total')
            ->groupBy('status')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            if (isset($values[$row['status']])) {
                $values[$row['status']] = (int) $row['total'];
            }
        }

        return [
            'labels' => array_values($labels),
            'values' => array_values($values),
        ];
    }

    private function vehicleAvailabilityToday(): array
    {
        $db = db_connect();
        $today = date('Y-m-d');
        $summary = [
            'tanggal'      => $today,
            'total'        => 0,
            'tersedia'     => 0,
            'booking'      => 0,
            'berjalan'     => 0,
            'jatuh_tempo'  => 0,
            'telat'        => 0,
            'maintenance'  => 0,
            'nonaktif'     => 0,
        ];

        $kendaraanRows = $db->table('kendaraan')
            ->select('id, status')
            ->get()
            ->getResultArray();

        $summary['total'] = count($kendaraanRows);

        foreach ($kendaraanRows as $kendaraan) {
            if ($kendaraan['status'] === 'maintenance') {
                $summary['maintenance']++;
                continue;
            }

            if ($kendaraan['status'] === 'nonaktif') {
                $summary['nonaktif']++;
                continue;
            }

            $jadwal = $db->table('transaksi_sewa')
                ->select('status_transaksi, tanggal_sewa, tanggal_kembali')
                ->where('kendaraan_id', $kendaraan['id'])
                ->groupStart()
                    ->groupStart()
                        ->where('status_transaksi', 'booking')
                        ->where('tanggal_sewa <=', $today)
                        ->where('tanggal_kembali >=', $today)
                    ->groupEnd()
                    ->orGroupStart()
                        ->where('status_transaksi', 'berjalan')
                        ->where('tanggal_sewa <=', $today)
                    ->groupEnd()
                ->groupEnd()
                ->orderBy("FIELD(status_transaksi, 'berjalan', 'booking')", '', false)
                ->orderBy('tanggal_sewa', 'ASC')
                ->get()
                ->getRowArray();

            if (! $jadwal) {
                $summary['tersedia']++;
                continue;
            }

            if ($jadwal['status_transaksi'] === 'booking') {
                $summary['booking']++;
                continue;
            }

            if ($jadwal['tanggal_kembali'] < $today) {
                $summary['telat']++;
            } elseif ($jadwal['tanggal_kembali'] === $today) {
                $summary['jatuh_tempo']++;
            } else {
                $summary['berjalan']++;
            }
        }

        return $summary;
    }

    private function dueReturns(): array
    {
        $db = db_connect();

        return $db->table('transaksi_sewa')
            ->select('transaksi_sewa.id, transaksi_sewa.kode_transaksi, transaksi_sewa.tanggal_kembali, pelanggan.nama_lengkap, kendaraan.nama_kendaraan, kendaraan.plat_nomor')
            ->join('pelanggan', 'pelanggan.id = transaksi_sewa.pelanggan_id')
            ->join('kendaraan', 'kendaraan.id = transaksi_sewa.kendaraan_id')
            ->where('transaksi_sewa.status_transaksi', 'berjalan')
            ->where('transaksi_sewa.tanggal_kembali <=', date('Y-m-d', strtotime('+3 days')))
            ->orderBy('transaksi_sewa.tanggal_kembali', 'ASC')
            ->limit(6)
            ->get()
            ->getResultArray();
    }

    private function recentTransaksi(): array
    {
        $db = db_connect();

        return $db->query("
            SELECT transaksi_sewa.id, transaksi_sewa.kode_transaksi, transaksi_sewa.tanggal_transaksi,
                   transaksi_sewa.status_transaksi, transaksi_sewa.status_pembayaran,
                   transaksi_sewa.total_tagihan, COALESCE(pembayaran.total_bayar, 0) AS total_bayar,
                   pelanggan.nama_lengkap, kendaraan.nama_kendaraan, kendaraan.plat_nomor
            FROM transaksi_sewa
            JOIN pelanggan ON pelanggan.id = transaksi_sewa.pelanggan_id
            JOIN kendaraan ON kendaraan.id = transaksi_sewa.kendaraan_id
            LEFT JOIN (
                SELECT transaksi_sewa_id, SUM(jumlah_bayar) AS total_bayar
                FROM pembayaran_sewa
                GROUP BY transaksi_sewa_id
            ) pembayaran ON pembayaran.transaksi_sewa_id = transaksi_sewa.id
            ORDER BY transaksi_sewa.id DESC
            LIMIT 6
        ")->getResultArray();
    }
}
