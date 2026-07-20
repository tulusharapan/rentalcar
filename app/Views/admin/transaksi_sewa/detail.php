<?= view('admin/layouts/header', ['title' => 'Detail Transaksi Sewa - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'transaksi-sewa',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Detail Transaksi Sewa',
    'pageSubtitle' => $transaksi['kode_transaksi'] . ' - ' . $transaksi['nama_lengkap'],
    'topbarAction' => '<div class="d-flex flex-wrap gap-2">'
        . '<a href="' . site_url('admin/transaksi-sewa/invoice/' . $transaksi['id']) . '" class="btn btn-dark" target="_blank"><i class="bi bi-file-earmark-pdf me-1"></i>Invoice</a>'
        . '<a href="' . site_url('admin/transaksi-sewa/surat-jalan/' . $transaksi['id']) . '" class="btn btn-secondary" target="_blank"><i class="bi bi-truck me-1"></i>Surat Jalan</a>'
        . '<a href="' . site_url('admin/transaksi-sewa') . '" class="btn btn-outline-dark"><i class="bi bi-arrow-left me-1"></i>Kembali</a>'
        . '</div>',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<section class="content-area">
    <div class="row g-4">
        <div class="col-12 col-xl-4">
            <div class="panel p-4 h-100">
                <div class="section-heading mb-0">Ringkasan Invoice</div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-secondary">Kode Invoice</span>
                    <span class="fw-semibold"><?= esc($transaksi['kode_transaksi']) ?></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-secondary">Tanggal Transaksi</span>
                    <span><?= esc(date('d/m/Y', strtotime($transaksi['tanggal_transaksi']))) ?></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-secondary">Lama Sewa Ditagihkan</span>
                    <span><?= esc((int) $transaksi['lama_sewa']) ?> hari</span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-secondary">Subtotal Sewa</span>
                    <span>Rp <?= number_format((int) $transaksi['subtotal_sewa'], 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-secondary">Total Layanan</span>
                    <span>Rp <?= number_format((int) $transaksi['total_layanan'], 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-secondary">Denda</span>
                    <span>Rp <?= number_format((int) ($transaksi['denda'] ?? 0), 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between py-3 border-bottom fs-5 fw-bold">
                    <span>Total Tagihan</span>
                    <span>Rp <?= number_format((int) $transaksi['total_tagihan'], 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-secondary">Total Bayar</span>
                    <span>Rp <?= number_format((int) $totalBayar, 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-secondary">Sisa Tagihan</span>
                    <span>Rp <?= number_format((int) $sisaTagihan, 0, ',', '.') ?></span>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="panel p-4 mb-4">
                <div class="row g-4">
                    <div class="col-12 col-lg-6">
                        <div class="section-heading mb-0">Data Pelanggan</div>
                        <div class="small text-secondary">Nama</div>
                        <div class="fw-semibold mb-2"><?= esc($transaksi['nama_lengkap']) ?></div>
                        <div class="small text-secondary">Kode Pelanggan</div>
                        <div class="mb-2"><?= esc($transaksi['kode_pelanggan']) ?></div>
                        <div class="small text-secondary">No. HP</div>
                        <div><?= esc($transaksi['no_hp'] ?? '-') ?></div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="section-heading mb-0">Data Kendaraan</div>
                        <div class="small text-secondary">Kendaraan</div>
                        <div class="fw-semibold mb-2"><?= esc(($transaksi['jenis_kendaraan'] ?? 'Mobil') . ' - ' . $transaksi['nama_kendaraan']) ?></div>
                        <div class="small text-secondary">Plat Nomor</div>
                        <div class="mb-2"><?= esc($transaksi['plat_nomor']) ?></div>
                        <div class="small text-secondary">Harga Sewa / Hari</div>
                        <div>Rp <?= number_format((int) $transaksi['harga_sewa_per_hari'], 0, ',', '.') ?></div>
                    </div>
                    <div class="col-12">
                        <div class="fw-semibold mb-2">Periode</div>
                        <div>
                            <?= esc(date('d/m/Y', strtotime($transaksi['tanggal_sewa']))) ?> -
                            <?= esc(date('d/m/Y', strtotime($transaksi['tanggal_kembali']))) ?>
                        </div>
                        <?php if (! empty($transaksi['tanggal_dikembalikan'])) : ?>
                            <div class="text-success small mt-1">
                                Dikembalikan: <?= esc(date('d/m/Y', strtotime($transaksi['tanggal_dikembalikan']))) ?>
                                <?php if ((int) ($transaksi['hari_terlambat'] ?? 0) > 0) : ?>
                                    (telat <?= esc((int) $transaksi['hari_terlambat']) ?> hari)
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-12">
                        <div class="fw-semibold mb-2">Status</div>
                        <span class="badge text-bg-light"><?= esc(ucfirst(str_replace('_', ' ', $transaksi['status_transaksi']))) ?></span>
                        <?php if ($transaksi['status_pembayaran'] === 'lunas') : ?>
                            <span class="badge text-bg-success">Lunas</span>
                        <?php elseif ($transaksi['status_pembayaran'] === 'belum_lunas') : ?>
                            <span class="badge text-bg-primary">Belum Lunas</span>
                        <?php else : ?>
                            <span class="badge text-bg-secondary">Belum Bayar</span>
                        <?php endif; ?>
                    </div>
                    <div class="col-12">
                        <div class="fw-semibold mb-2">Catatan</div>
                        <div><?= nl2br(esc($transaksi['catatan'] ?: '-')) ?></div>
                    </div>
                </div>
            </div>

            <div class="panel p-4 mb-4">
                <div class="section-heading mb-0">Layanan Tambahan</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Layanan</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($layanan as $row) : ?>
                                <tr>
                                    <td><?= esc($row['nama_layanan']) ?></td>
                                    <td class="text-end"><?= esc((int) $row['qty']) ?></td>
                                    <td class="text-end">Rp <?= number_format((int) $row['harga_layanan'], 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format((int) $row['total_harga'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($layanan)) : ?>
                                <tr>
                                    <td colspan="4" class="text-center text-secondary">Tidak ada layanan tambahan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel p-4">
                <div class="section-heading mb-0">Riwayat Pembayaran</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Metode</th>
                                <th>Catatan</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pembayaran as $row) : ?>
                                <tr>
                                    <td><?= esc(date('d/m/Y', strtotime($row['tanggal_pembayaran']))) ?></td>
                                    <td><?= esc(ucfirst($row['metode_pembayaran'])) ?></td>
                                    <td><?= esc($row['catatan'] ?: '-') ?></td>
                                    <td class="text-end">Rp <?= number_format((int) $row['jumlah_bayar'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($pembayaran)) : ?>
                                <tr>
                                    <td colspan="4" class="text-center text-secondary">Belum ada pembayaran.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
