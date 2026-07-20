<?php
$setting = function_exists('app_setting') ? app_setting() : [];
$appName = trim((string) ($setting['nama_aplikasi'] ?? 'Aplikasi')) ?: 'Aplikasi';
$company = trim((string) ($setting['nama_perusahaan'] ?? $appName)) ?: $appName;
$tanggalMulai = $filters['tanggal_mulai'] !== '' ? date('d/m/Y', strtotime($filters['tanggal_mulai'])) : '-';
$tanggalSelesai = $filters['tanggal_selesai'] !== '' ? date('d/m/Y', strtotime($filters['tanggal_selesai'])) : '-';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        .header { display: table; width: 100%; border-bottom: 2px solid #111827; padding-bottom: 12px; margin-bottom: 14px; }
        .header > div { display: table-cell; vertical-align: top; }
        .right { text-align: right; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        .muted { color: #6b7280; }
        .summary { width: 100%; margin: 14px 0; border-collapse: collapse; }
        .summary td { border: 1px solid #d1d5db; padding: 8px; }
        .summary .value { font-size: 13px; font-weight: bold; margin-top: 3px; }
        .green { color: #166534; }
        .red { color: #b91c1c; }
        .section-title { font-size: 12px; font-weight: bold; margin: 16px 0 7px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #d1d5db; padding: 6px; vertical-align: top; }
        table.data th { background: #f3f4f6; text-align: left; }
        .footer { margin-top: 26px; display: table; width: 100%; }
        .footer > div { display: table-cell; width: 50%; vertical-align: top; }
        .sign { width: 220px; margin-left: auto; text-align: center; }
        .line { border-top: 1px solid #111827; padding-top: 6px; margin-top: 54px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>Laporan Keuangan</h1>
            <div><?= esc($company) ?></div>
            <div class="muted">Periode <?= esc($tanggalMulai) ?> - <?= esc($tanggalSelesai) ?></div>
        </div>
        <div class="right">
            <div><strong><?= esc($appName) ?></strong></div>
            <div class="muted">Dicetak: <?= esc(date('d/m/Y H:i')) ?></div>
        </div>
    </div>

    <table class="summary">
        <tr>
            <td>
                <strong>Jumlah Transaksi</strong>
                <div class="value"><?= number_format((int) $summary['jumlah_transaksi'], 0, ',', '.') ?></div>
            </td>
            <td>
                <strong>Total Pemasukan</strong>
                <div class="value green">Rp <?= number_format((int) $summary['pemasukan'], 0, ',', '.') ?></div>
            </td>
            <td>
                <strong>Total Pengeluaran</strong>
                <div class="value red">Rp <?= number_format((int) $summary['pengeluaran'], 0, ',', '.') ?></div>
            </td>
            <td>
                <strong>Saldo Bersih</strong>
                <div class="value <?= (int) $summary['saldo'] >= 0 ? 'green' : 'red' ?>">Rp <?= number_format((int) $summary['saldo'], 0, ',', '.') ?></div>
            </td>
        </tr>
    </table>

    <div class="section-title">Ringkasan Kategori</div>
    <table class="data">
        <thead>
            <tr>
                <th>Jenis</th>
                <th>Kategori</th>
                <th class="right">Jumlah Transaksi</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kategoriSummary as $row) : ?>
                <tr>
                    <td><?= esc($row['jenis']) ?></td>
                    <td><?= esc($row['kategori']) ?></td>
                    <td class="right"><?= esc((int) $row['jumlah']) ?></td>
                    <td class="right">Rp <?= number_format((int) $row['total'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($kategoriSummary)) : ?>
                <tr>
                    <td colspan="4" class="right">Tidak ada data.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="section-title">Detail Transaksi</div>
    <table class="data">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Kategori</th>
                <th>Catatan</th>
                <th class="right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transaksi as $row) : ?>
                <tr>
                    <td><?= esc(date('d/m/Y', strtotime($row['tanggal']))) ?></td>
                    <td><?= esc($row['jenis']) ?></td>
                    <td><?= esc($row['kategori']) ?></td>
                    <td><?= esc($row['catatan'] ?: '-') ?></td>
                    <td class="right">Rp <?= number_format((int) $row['nominal'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($transaksi)) : ?>
                <tr>
                    <td colspan="5" class="right">Tidak ada data.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="muted">
            Laporan ini disusun dari transaksi keuangan yang tercatat pada sistem.
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
