<?php
$setting = function_exists('app_setting') ? app_setting() : [];
$appName = trim((string) ($setting['nama_aplikasi'] ?? 'Aplikasi')) ?: 'Aplikasi';
$company = trim((string) ($setting['nama_perusahaan'] ?? $appName)) ?: $appName;
$statusPembayaranLabel = ucfirst(str_replace('_', ' ', $transaksi['status_pembayaran']));
$statusTransaksiLabel = ucfirst(str_replace('_', ' ', $transaksi['status_transaksi']));
$statusClass = $transaksi['status_pembayaran'] === 'lunas' ? 'success' : ($transaksi['status_pembayaran'] === 'belum_lunas' ? 'warning' : 'muted-badge');
$petugas = $petugas ?? '-';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .header { display: table; width: 100%; border-bottom: 2px solid #111827; padding-bottom: 14px; margin-bottom: 16px; }
        .header > div { display: table-cell; vertical-align: top; }
        .right { text-align: right; }
        .title { font-size: 25px; font-weight: bold; margin: 0 0 4px; letter-spacing: .4px; }
        .muted { color: #6b7280; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 999px; font-size: 10px; font-weight: bold; }
        .success { background: #ecfdf3; color: #166534; }
        .warning { background: #fffbeb; color: #92400e; }
        .muted-badge { background: #f3f4f6; color: #374151; }
        table { width: 100%; border-collapse: collapse; }
        .grid { display: table; width: 100%; margin: 14px 0; }
        .grid > div { display: table-cell; width: 50%; vertical-align: top; }
        .card { border: 1px solid #d1d5db; padding: 12px; min-height: 88px; }
        .card-title { font-weight: bold; margin-bottom: 8px; font-size: 12px; }
        .info td { padding: 4px 0; vertical-align: top; }
        .label { width: 125px; color: #6b7280; }
        .timeline { margin: 14px 0; border: 1px solid #d1d5db; background: #f8fafc; }
        .timeline td { padding: 10px 12px; border-right: 1px solid #d1d5db; vertical-align: top; }
        .timeline td:last-child { border-right: 0; }
        .timeline .value { font-size: 13px; font-weight: bold; margin-top: 3px; }
        .items th, .items td { border: 1px solid #d1d5db; padding: 8px; vertical-align: top; }
        .items th { background: #f3f4f6; text-align: left; font-size: 10px; text-transform: uppercase; color: #374151; }
        .items .money { text-align: right; white-space: nowrap; }
        .summary-wrap { display: table; width: 100%; margin-top: 14px; }
        .summary-note, .summary-box { display: table-cell; vertical-align: top; }
        .summary-note { width: 52%; padding-right: 18px; color: #4b5563; }
        .summary-box { width: 48%; }
        .summary td { padding: 6px 0; }
        .summary .total td { border-top: 1px solid #111827; padding-top: 8px; font-weight: bold; font-size: 13px; }
        .summary .remaining td { background: #f8fafc; border: 1px solid #d1d5db; padding: 9px; font-weight: bold; }
        .section-title { margin: 20px 0 8px; font-size: 13px; font-weight: bold; }
        .note-box { margin-top: 14px; padding: 10px 12px; border-left: 4px solid #2563eb; background: #eff6ff; }
        .footer { margin-top: 28px; display: table; width: 100%; }
        .footer > div { display: table-cell; width: 50%; vertical-align: top; }
        .sign { width: 230px; margin-left: auto; text-align: center; }
        .line { border-top: 1px solid #111827; padding-top: 6px; margin-top: 58px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="title">INVOICE SEWA</div>
            <div><?= esc($company) ?></div>
            <div class="muted">Dokumen tagihan transaksi sewa kendaraan</div>
        </div>
        <div class="right">
            <div><strong><?= esc($transaksi['kode_transaksi']) ?></strong></div>
            <div class="muted">Tanggal Invoice: <?= esc(date('d/m/Y', strtotime($transaksi['tanggal_transaksi']))) ?></div>
            <div style="margin-top: 8px;">
                <span class="badge <?= esc($statusClass) ?>"><?= esc($statusPembayaranLabel) ?></span>
            </div>
        </div>
    </div>

    <div class="grid">
        <div style="padding-right: 8px;">
            <div class="card">
                <div class="card-title">Pelanggan</div>
                <table class="info">
                    <tr>
                        <td class="label">Nama</td>
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
                <div class="card-title">Kendaraan</div>
                <table class="info">
                    <tr>
                        <td class="label">Jenis</td>
                        <td>: <?= esc($transaksi['jenis_kendaraan'] ?? 'Mobil') ?></td>
                    </tr>
                    <tr>
                        <td class="label">Nama</td>
                        <td>: <?= esc($transaksi['nama_kendaraan']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Plat Nomor</td>
                        <td>: <?= esc($transaksi['plat_nomor']) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <table class="timeline">
        <tr>
            <td>
                <div class="muted">Tanggal Sewa</div>
                <div class="value"><?= esc(date('d/m/Y', strtotime($transaksi['tanggal_sewa']))) ?></div>
            </td>
            <td>
                <div class="muted">Tanggal Kembali Rencana</div>
                <div class="value"><?= esc(date('d/m/Y', strtotime($transaksi['tanggal_kembali']))) ?></div>
            </td>
            <td>
                <div class="muted">Tanggal Kembali Aktual</div>
                <div class="value"><?= ! empty($transaksi['tanggal_dikembalikan']) ? esc(date('d/m/Y', strtotime($transaksi['tanggal_dikembalikan']))) : '-' ?></div>
            </td>
            <td>
                <div class="muted">Durasi Ditagihkan</div>
                <div class="value"><?= esc((int) $transaksi['lama_sewa']) ?> hari</div>
            </td>
            <td>
                <div class="muted">Status Transaksi</div>
                <div class="value"><?= esc($statusTransaksiLabel) ?></div>
            </td>
        </tr>
    </table>

    <div class="section-title">Rincian Tagihan</div>
    <table class="items">
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th class="money">Qty</th>
                <th class="money">Harga</th>
                <th class="money">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Sewa kendaraan <?= esc($transaksi['nama_kendaraan']) ?></strong>
                    <div class="muted">Harga sewa per hari x durasi ditagihkan</div>
                </td>
                <td class="money"><?= esc((int) $transaksi['lama_sewa']) ?> hari</td>
                <td class="money">Rp <?= number_format((int) $transaksi['harga_sewa_per_hari'], 0, ',', '.') ?></td>
                <td class="money">Rp <?= number_format((int) $transaksi['subtotal_sewa'], 0, ',', '.') ?></td>
            </tr>
            <?php foreach ($layanan as $row) : ?>
                <tr>
                    <td><?= esc($row['nama_layanan']) ?></td>
                    <td class="money"><?= esc((int) $row['qty']) ?></td>
                    <td class="money">Rp <?= number_format((int) $row['harga_layanan'], 0, ',', '.') ?></td>
                    <td class="money">Rp <?= number_format((int) $row['total_harga'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ((int) ($transaksi['denda'] ?? 0) > 0) : ?>
                <tr>
                    <td>
                        <strong>Denda keterlambatan</strong>
                        <div class="muted">Terlambat <?= esc((int) ($transaksi['hari_terlambat'] ?? 0)) ?> hari</div>
                    </td>
                    <td class="money"><?= esc((int) ($transaksi['hari_terlambat'] ?? 0)) ?> hari</td>
                    <td class="money">Rp <?= number_format((int) ($transaksi['harga_denda_per_hari'] ?? 0), 0, ',', '.') ?></td>
                    <td class="money">Rp <?= number_format((int) $transaksi['denda'], 0, ',', '.') ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="summary-wrap">
        <div class="summary-note">
            <div class="note-box">
                <strong>Catatan Transaksi</strong><br>
                <?= nl2br(esc($transaksi['catatan'] ?: 'Tidak ada catatan tambahan.')) ?>
            </div>
            <?php if ((int) ($transaksi['denda'] ?? 0) > 0) : ?>
                <div style="margin-top: 10px;" class="muted">
                    Denda memakai harga snapshot saat kendaraan dikembalikan, sehingga tidak berubah walaupun pengaturan denda berubah setelah transaksi selesai.
                </div>
            <?php endif; ?>
        </div>
        <div class="summary-box">
            <table class="summary">
                <tr>
                    <td>Subtotal Sewa</td>
                    <td class="right">Rp <?= number_format((int) $transaksi['subtotal_sewa'], 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>Total Layanan</td>
                    <td class="right">Rp <?= number_format((int) $transaksi['total_layanan'], 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>Denda</td>
                    <td class="right">Rp <?= number_format((int) ($transaksi['denda'] ?? 0), 0, ',', '.') ?></td>
                </tr>
                <tr class="total">
                    <td>Total Tagihan</td>
                    <td class="right">Rp <?= number_format((int) $transaksi['total_tagihan'], 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>Total Pembayaran</td>
                    <td class="right">Rp <?= number_format((int) $totalBayar, 0, ',', '.') ?></td>
                </tr>
                <tr class="remaining">
                    <td>Sisa Tagihan</td>
                    <td class="right">Rp <?= number_format((int) $sisaTagihan, 0, ',', '.') ?></td>
                </tr>
            </table>
        </div>
    </div>

    <?php if (! empty($pembayaran)) : ?>
        <div class="section-title">Riwayat Pembayaran</div>
        <table class="items">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Metode</th>
                    <th>Catatan</th>
                    <th class="money">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pembayaran as $row) : ?>
                    <tr>
                        <td><?= esc(date('d/m/Y', strtotime($row['tanggal_pembayaran']))) ?></td>
                        <td><?= esc(ucfirst($row['metode_pembayaran'])) ?></td>
                        <td><?= esc($row['catatan'] ?: '-') ?></td>
                        <td class="money">Rp <?= number_format((int) $row['jumlah_bayar'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="footer">
        <div class="muted">
            Invoice ini dicetak otomatis oleh sistem pada <?= esc(date('d/m/Y H:i')) ?>.
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
