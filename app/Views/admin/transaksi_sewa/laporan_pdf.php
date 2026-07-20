<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111827; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .muted { color: #6b7280; }
        .summary { width: 100%; margin: 14px 0; border-collapse: collapse; }
        .summary td { border: 1px solid #d1d5db; padding: 8px; }
        table.data { width: 100%; border-collapse: collapse; }
        table.data th, table.data td { border: 1px solid #d1d5db; padding: 5px; vertical-align: top; }
        table.data th { background: #f3f4f6; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h1>Laporan Transaksi Sewa</h1>
    <div class="muted">Dicetak: <?= esc(date('d/m/Y H:i')) ?></div>

    <table class="summary">
        <tr>
            <td><strong>Jumlah Transaksi</strong><br><?= number_format((int) $summary['jumlah_transaksi'], 0, ',', '.') ?></td>
            <td><strong>Total Tagihan</strong><br>Rp <?= number_format((int) $summary['total_tagihan'], 0, ',', '.') ?></td>
            <td><strong>Total Bayar</strong><br>Rp <?= number_format((int) $summary['total_bayar'], 0, ',', '.') ?></td>
            <td><strong>Sisa Tagihan</strong><br>Rp <?= number_format((int) $summary['sisa_tagihan'], 0, ',', '.') ?></td>
            <td><strong>Total Denda</strong><br>Rp <?= number_format((int) $summary['total_denda'], 0, ',', '.') ?></td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Kendaraan</th>
                <th>Periode</th>
                <th class="right">Total</th>
                <th class="right">Bayar</th>
                <th class="right">Sisa</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transaksi as $row) : ?>
                <tr>
                    <td><?= esc($row['kode_transaksi']) ?></td>
                    <td><?= esc(date('d/m/Y', strtotime($row['tanggal_transaksi']))) ?></td>
                    <td><?= esc($row['nama_lengkap']) ?></td>
                    <td><?= esc($row['nama_kendaraan'] . ' - ' . $row['plat_nomor']) ?></td>
                    <td><?= esc(date('d/m/Y', strtotime($row['tanggal_sewa']))) ?> - <?= esc(date('d/m/Y', strtotime($row['tanggal_kembali']))) ?></td>
                    <td class="right">Rp <?= number_format((int) $row['total_tagihan'], 0, ',', '.') ?></td>
                    <td class="right">Rp <?= number_format((int) $row['total_bayar'], 0, ',', '.') ?></td>
                    <td class="right">Rp <?= number_format((int) $row['sisa_tagihan'], 0, ',', '.') ?></td>
                    <td><?= esc(ucfirst(str_replace('_', ' ', $row['status_transaksi']))) ?> / <?= esc(ucfirst(str_replace('_', ' ', $row['status_pembayaran']))) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
