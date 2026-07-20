<?php
$setting = function_exists('app_setting') ? app_setting() : [];
$appName = trim((string) ($setting['nama_aplikasi'] ?? 'Aplikasi')) ?: 'Aplikasi';
$company = trim((string) ($setting['nama_perusahaan'] ?? $appName)) ?: $appName;
$receiptNumber = $transaksi['kode_transaksi'] . '-' . str_pad((string) $pembayaran['id'], 4, '0', STR_PAD_LEFT);
$petugas = $petugas ?? '-';

if (! function_exists('kuitansi_terbilang')) {
    function kuitansi_terbilang(int $nilai): string
    {
        $nilai = abs($nilai);
        $huruf = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'];

        if ($nilai < 12) {
            return $huruf[$nilai];
        }

        if ($nilai < 20) {
            return kuitansi_terbilang($nilai - 10) . ' belas';
        }

        if ($nilai < 100) {
            return kuitansi_terbilang(intdiv($nilai, 10)) . ' puluh' . ($nilai % 10 > 0 ? ' ' . kuitansi_terbilang($nilai % 10) : '');
        }

        if ($nilai < 200) {
            return 'seratus' . ($nilai - 100 > 0 ? ' ' . kuitansi_terbilang($nilai - 100) : '');
        }

        if ($nilai < 1000) {
            return kuitansi_terbilang(intdiv($nilai, 100)) . ' ratus' . ($nilai % 100 > 0 ? ' ' . kuitansi_terbilang($nilai % 100) : '');
        }

        if ($nilai < 2000) {
            return 'seribu' . ($nilai - 1000 > 0 ? ' ' . kuitansi_terbilang($nilai - 1000) : '');
        }

        if ($nilai < 1000000) {
            return kuitansi_terbilang(intdiv($nilai, 1000)) . ' ribu' . ($nilai % 1000 > 0 ? ' ' . kuitansi_terbilang($nilai % 1000) : '');
        }

        if ($nilai < 1000000000) {
            return kuitansi_terbilang(intdiv($nilai, 1000000)) . ' juta' . ($nilai % 1000000 > 0 ? ' ' . kuitansi_terbilang($nilai % 1000000) : '');
        }

        if ($nilai < 1000000000000) {
            return kuitansi_terbilang(intdiv($nilai, 1000000000)) . ' miliar' . ($nilai % 1000000000 > 0 ? ' ' . kuitansi_terbilang($nilai % 1000000000) : '');
        }

        return kuitansi_terbilang(intdiv($nilai, 1000000000000)) . ' triliun' . ($nilai % 1000000000000 > 0 ? ' ' . kuitansi_terbilang($nilai % 1000000000000) : '');
    }
}

$terbilang = ucfirst(trim(kuitansi_terbilang((int) $pembayaran['jumlah_bayar']))) . ' rupiah';
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 28px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #111827;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .header>div {
            display: table-cell;
            vertical-align: top;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin: 0 0 4px;
            letter-spacing: .4px;
        }

        .muted {
            color: #6b7280;
        }

        .right {
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 6px 0;
            vertical-align: top;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            background: #ecfdf3;
            color: #166534;
            font-size: 11px;
            font-weight: bold;
        }

        .amount-box {
            margin: 18px 0;
            padding: 18px;
            border: 1px solid #d1d5db;
            background: #f8fafc;
            text-align: center;
        }

        .amount-label {
            color: #6b7280;
            margin-bottom: 6px;
        }

        .amount {
            font-size: 20px;
            font-weight: bold;
        }

        .terbilang {
            margin-top: 8px;
            color: #374151;
            font-style: italic;
        }

        .grid {
            display: table;
            width: 100%;
            margin-bottom: 16px;
        }

        .grid>div {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .card {
            border: 1px solid #d1d5db;
            padding: 12px;
        }

        .card-title {
            font-weight: bold;
            margin-bottom: 8px;
        }

        .info td {
            padding: 4px 0;
        }

        .label {
            width: 120px;
            color: #6b7280;
        }

        .items th,
        .items td {
            border: 1px solid #d1d5db;
            padding: 8px;
        }

        .items th {
            background: #f3f4f6;
            text-align: left;
        }

        .note {
            margin-top: 14px;
            padding: 10px 12px;
            border-left: 4px solid #2563eb;
            background: #eff6ff;
        }

        .footer {
            margin-top: 34px;
            display: table;
            width: 100%;
        }

        .footer>div {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .sign {
            width: 230px;
            margin-left: auto;
            text-align: center;
        }

        .line {
            border-top: 1px solid #111827;
            padding-top: 6px;
            margin-top: 58px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    
    <div class="header">
        <div>
            <div class="title">KUITANSI</div>
            <div><?= esc($company) ?></div>
            <div class="muted">Bukti pembayaran transaksi sewa kendaraan</div>
        </div>
        <div class="right">
            <div class="badge">Pembayaran Diterima</div>
            <div style="margin-top: 10px;"><strong>No. <?= esc($receiptNumber) ?></strong></div>
            <div class="muted">Invoice <?= esc($transaksi['kode_transaksi']) ?></div>
        </div>
    </div>

    <div class="grid">
        <div style="padding-right: 8px;">
            <div class="card">
                <div class="card-title">Diterima Dari</div>
                <table class="info">
                    <tr>
                        <td class="label">Pelanggan</td>
                        <td>: <?= esc($transaksi['nama_lengkap']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Kode</td>
                        <td>: <?= esc($transaksi['kode_pelanggan']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">No. HP</td>
                        <td>: <?= esc($transaksi['no_hp'] ?? '-') ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="padding-left: 8px;">
            <div class="card">
                <div class="card-title">Untuk Pembayaran</div>
                <table class="info">
                    <tr>
                        <td class="label">Kendaraan</td>
                        <td>: <?= esc(($transaksi['jenis_kendaraan'] ?? 'Mobil') . ' - ' . $transaksi['nama_kendaraan']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Plat Nomor</td>
                        <td>: <?= esc($transaksi['plat_nomor']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Periode</td>
                        <td>: <?= esc(date('d/m/Y', strtotime($transaksi['tanggal_sewa']))) ?> - <?= esc(date('d/m/Y', strtotime($transaksi['tanggal_kembali']))) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <table class="items">       
        <tbody>
            <tr>
                <td width="25%">Tanggal Pembayaran</td>
                <td><?= esc(date('d/m/Y', strtotime($pembayaran['tanggal_pembayaran']))) ?></td>
                <td>Metode Pembayaran</td>
                <td><?= esc(ucfirst($pembayaran['metode_pembayaran'])) ?></td>
            </tr>
            <tr>
                <td>Total Tagihan Invoice</td>
                <td>Rp <?= number_format((int) $transaksi['total_tagihan'], 0, ',', '.') ?></td>
                <td>Status Pembayaran Invoice</td>
                <td><?= esc(ucfirst(str_replace('_', ' ', $transaksi['status_pembayaran']))) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="amount-box">
        <div class="amount-label">Jumlah Pembayaran</div>
        <div class="amount">Rp <?= number_format((int) $pembayaran['jumlah_bayar'], 0, ',', '.') ?></div>
        <div class="terbilang">( <?= esc($terbilang) ?> ) </div>
    </div>

    

    

    <div class="note">
        <strong>Catatan:</strong> <?= esc($pembayaran['catatan'] ?: 'Pembayaran diterima untuk invoice ' . $transaksi['kode_transaksi']) ?>
    </div>

    <div class="footer">
        <div class="muted">
            Kuitansi ini dicetak otomatis oleh sistem pada <?= esc(date('d/m/Y H:i')) ?>.
        </div>
        <div>
            <div class="sign">
                <div><?= esc(date('d/m/Y')) ?></div>
                <div class="line"><?= esc($petugas) ?></div>
                <div class="muted">Petugas</div>
            </div>
        </div>
    </div>
</body>

</html>
