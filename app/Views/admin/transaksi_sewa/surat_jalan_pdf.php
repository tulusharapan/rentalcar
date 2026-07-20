<?php
$setting = function_exists('app_setting') ? app_setting() : [];
$appName = trim((string) ($setting['nama_aplikasi'] ?? 'Aplikasi')) ?: 'Aplikasi';
$company = trim((string) ($setting['nama_perusahaan'] ?? $appName)) ?: $appName;
$petugas = $petugas ?? '-';
$statusTransaksiLabel = ucfirst(str_replace('_', ' ', (string) $transaksi['status_transaksi']));
$tanggalCetak = date('d/m/Y H:i');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 30px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        .header { display: table; width: 100%; border-bottom: 2px solid #111827; padding-bottom: 14px; margin-bottom: 16px; }
        .header > div { display: table-cell; vertical-align: top; }
        .right { text-align: right; }
        .title { font-size: 24px; font-weight: bold; margin: 0 0 4px; letter-spacing: .4px; }
        .muted { color: #6b7280; }
        .doc-number { font-size: 14px; font-weight: bold; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 999px; background: #f3f4f6; color: #374151; font-size: 10px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        .grid { display: table; width: 100%; margin: 14px 0; }
        .grid > div { display: table-cell; width: 50%; vertical-align: top; }
        .card { border: 1px solid #d1d5db; padding: 12px; min-height: 116px; }
        .card-title { font-weight: bold; margin-bottom: 8px; font-size: 12px; }
        .info td { padding: 4px 0; vertical-align: top; }
        .label { width: 118px; color: #6b7280; }
        .timeline { margin: 14px 0; border: 1px solid #d1d5db; background: #f8fafc; }
        .timeline td { padding: 10px 12px; border-right: 1px solid #d1d5db; vertical-align: top; }
        .timeline td:last-child { border-right: 0; }
        .timeline .value { font-size: 13px; font-weight: bold; margin-top: 3px; }
        .section-title { margin: 18px 0 8px; font-size: 13px; font-weight: bold; }
        .items th, .items td { border: 1px solid #d1d5db; padding: 8px; vertical-align: top; }
        .items th { background: #f3f4f6; text-align: left; font-size: 10px; text-transform: uppercase; color: #374151; }
        .items .center { text-align: center; }
        .checklist td { border: 1px solid #d1d5db; padding: 9px 10px; }
        .checkbox { display: inline-block; width: 12px; height: 12px; border: 1px solid #111827; margin-right: 6px; vertical-align: -2px; }
        .note-box { margin-top: 14px; padding: 10px 12px; border-left: 4px solid #2563eb; background: #eff6ff; line-height: 1.55; }
        .terms { margin-top: 12px; color: #374151; line-height: 1.6; }
        .signatures { display: table; width: 100%; margin-top: 28px; }
        .signatures > div { display: table-cell; width: 33.333%; vertical-align: top; text-align: center; }
        .line { border-top: 1px solid #111827; padding-top: 6px; margin: 62px 16px 0; }
        .footer-note { margin-top: 18px; font-size: 10px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="title">SURAT JALAN</div>
            <div><?= esc($company) ?></div>
            <div class="muted">Dokumen serah-terima kendaraan rental</div>
        </div>
        <div class="right">
            <div class="doc-number"><?= esc($transaksi['kode_transaksi']) ?></div>
            <div class="muted">Tanggal Cetak: <?= esc($tanggalCetak) ?></div>
            <div style="margin-top: 8px;"><span class="badge"><?= esc($statusTransaksiLabel) ?></span></div>
        </div>
    </div>

    <div class="grid">
        <div style="padding-right: 8px;">
            <div class="card">
                <div class="card-title">Data Pelanggan</div>
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
                    <tr>
                        <td class="label">Alamat</td>
                        <td>: <?= esc($transaksi['alamat'] ?? '-') ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="padding-left: 8px;">
            <div class="card">
                <div class="card-title">Data Kendaraan</div>
                <table class="info">
                    <tr>
                        <td class="label">Jenis</td>
                        <td>: <?= esc($transaksi['jenis_kendaraan'] ?? 'Kendaraan') ?></td>
                    </tr>
                    <tr>
                        <td class="label">Kendaraan</td>
                        <td>: <?= esc($transaksi['nama_kendaraan']) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Merk/Tahun</td>
                        <td>: <?= esc(($transaksi['merk'] ?? '-') . ' / ' . ($transaksi['tahun'] ?? '-')) ?></td>
                    </tr>
                    <tr>
                        <td class="label">Plat/Warna</td>
                        <td>: <?= esc($transaksi['plat_nomor'] . ' / ' . ($transaksi['warna'] ?? '-')) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <table class="timeline">
        <tr>
            <td>
                <div class="muted">Tanggal Transaksi</div>
                <div class="value"><?= esc(date('d/m/Y', strtotime($transaksi['tanggal_transaksi']))) ?></div>
            </td>
            <td>
                <div class="muted">Tanggal Sewa</div>
                <div class="value"><?= esc(date('d/m/Y', strtotime($transaksi['tanggal_sewa']))) ?></div>
            </td>
            <td>
                <div class="muted">Rencana Kembali</div>
                <div class="value"><?= esc(date('d/m/Y', strtotime($transaksi['tanggal_kembali']))) ?></div>
            </td>
            <td>
                <div class="muted">Lama Sewa</div>
                <div class="value"><?= esc((int) $transaksi['lama_sewa']) ?> hari</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Layanan Tambahan Dibawa/Digunakan</div>
    <table class="items">
        <thead>
            <tr>
                <th>Layanan</th>
                <th class="center">Qty</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (! empty($layanan)) : ?>
                <?php foreach ($layanan as $row) : ?>
                    <tr>
                        <td><?= esc($row['nama_layanan']) ?></td>
                        <td class="center"><?= esc((int) $row['qty']) ?></td>
                        <td>Disertakan pada transaksi sewa.</td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="3" class="center muted">Tidak ada layanan tambahan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="section-title">Checklist Serah Terima</div>
    <table class="checklist">
        <tr>
            <td><span class="checkbox"></span>Kondisi kendaraan sudah diperiksa bersama pelanggan.</td>
            <td><span class="checkbox"></span>STNK/dokumen kendaraan diterima pelanggan.</td>
        </tr>
        <tr>
            <td><span class="checkbox"></span>Perlengkapan kendaraan diserahkan lengkap.</td>
            <td><span class="checkbox"></span>Pelanggan memahami tanggal pengembalian.</td>
        </tr>
        <tr>
            <td><span class="checkbox"></span>Foto/kondisi awal kendaraan sudah didokumentasikan.</td>
            <td><span class="checkbox"></span>Ketentuan denda keterlambatan sudah dijelaskan.</td>
        </tr>
    </table>

    <div class="note-box">
        <strong>Catatan Transaksi</strong><br>
        <?= nl2br(esc($transaksi['catatan'] ?: 'Tidak ada catatan tambahan.')) ?>
    </div>

    <div class="terms">
        Dengan ditandatanganinya surat jalan ini, kendaraan dinyatakan telah diserahkan kepada pelanggan sesuai data transaksi di atas.
        Pelanggan wajib mengembalikan kendaraan sesuai jadwal, menjaga kondisi kendaraan, dan bertanggung jawab atas penggunaan kendaraan selama masa sewa.
    </div>

    <div class="signatures">
        <div>
            <div>Diserahkan oleh,</div>
            <div class="line"><?= esc($petugas) ?></div>
            <div class="muted">Petugas</div>
        </div>
        <div>
            <div>Diterima oleh,</div>
            <div class="line"><?= esc($transaksi['nama_lengkap']) ?></div>
            <div class="muted">Pelanggan</div>
        </div>
        <div>
            <div>Diperiksa oleh,</div>
            <div class="line">&nbsp;</div>
            <div class="muted">Pemeriksa Kendaraan</div>
        </div>
    </div>

    <div class="footer-note">
        Surat jalan ini dicetak otomatis oleh sistem <?= esc($appName) ?>. Simpan dokumen ini sebagai bukti serah-terima kendaraan.
    </div>
</body>
</html>
