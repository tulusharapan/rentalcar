<?= view('admin/layouts/header', ['title' => 'Detail Kendaraan - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'kendaraan',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Detail Kendaraan',
    'pageSubtitle' => $kendaraan['kode_kendaraan'] . ' - ' . $kendaraan['nama_kendaraan'],
    'topbarAction' => '<a href="' . site_url('admin/kendaraan') . '" class="btn btn-outline-dark"><i class="bi bi-arrow-left me-1"></i>Kembali</a>',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<section class="content-area">
    <div class="row g-4">
        <div class="col-12 col-xl-4">
            <div class="panel p-4 h-100">
                <div class="section-heading mb-0">Data Kendaraan</div>
                <div class="d-grid gap-3">
                    <div>
                        <div class="small text-secondary">Kode</div>
                        <div class="fw-semibold"><?= esc($kendaraan['kode_kendaraan']) ?></div>
                    </div>
                    <div>
                        <div class="small text-secondary">Jenis</div>
                        <div><?= esc($kendaraan['jenis_kendaraan'] ?? 'Mobil') ?></div>
                    </div>
                    <div>
                        <div class="small text-secondary">Nama Kendaraan</div>
                        <div><?= esc($kendaraan['nama_kendaraan']) ?></div>
                    </div>
                    <div>
                        <div class="small text-secondary">Plat Nomor</div>
                        <div><?= esc($kendaraan['plat_nomor']) ?></div>
                    </div>
                    <div>
                        <div class="small text-secondary">Merk, Tahun, Warna</div>
                        <div><?= esc($kendaraan['merk']) ?>, <?= esc($kendaraan['tahun']) ?>, <?= esc($kendaraan['warna']) ?></div>
                    </div>
                    <div>
                        <div class="small text-secondary">Harga Sewa / Hari</div>
                        <div>Rp <?= number_format((int) $kendaraan['harga_sewa_per_hari'], 0, ',', '.') ?></div>
                    </div>
                    <div>
                        <div class="small text-secondary">Status</div>
                        <?php if ($kendaraan['status'] === 'ready') : ?>
                            <span class="badge text-bg-success">Ready</span>
                        <?php elseif ($kendaraan['status'] === 'maintenance') : ?>
                            <span class="badge text-bg-warning">Maintenance</span>
                        <?php else : ?>
                            <span class="badge text-bg-secondary">Nonaktif</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="small text-secondary">Keterangan</div>
                        <div><?= nl2br(esc($kendaraan['keterangan'] ?: '-')) ?></div>
                    </div>
                    <?php if ($hasTransaksi) : ?>
                        <div class="alert alert-warning mb-0">
                            Kendaraan ini sudah memiliki riwayat transaksi, sehingga tidak bisa dihapus. Gunakan status Nonaktif jika tidak dipakai lagi.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="panel p-4 mb-4">
                <div class="section-heading mb-0">Foto Kendaraan</div>
                <?php if (! empty($fotoKendaraan)) : ?>
                    <div class="row g-3">
                        <?php foreach ($fotoKendaraan as $foto) : ?>
                            <div class="col-6 col-md-4">
                                <img src="<?= base_url('uploads/kendaraan/' . $foto['file_name']) ?>" alt="Foto kendaraan" class="w-100 rounded border" style="aspect-ratio: 4 / 3; object-fit: cover;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="text-secondary">Belum ada foto kendaraan.</div>
                <?php endif; ?>
            </div>

            <div class="panel p-4">
                <div class="section-heading mb-0">Riwayat Transaksi Terakhir</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Pelanggan</th>
                                <th>Periode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transaksiTerakhir as $transaksi) : ?>
                                <tr>
                                    <td class="fw-semibold"><?= esc($transaksi['kode_transaksi']) ?></td>
                                    <td>
                                        <div><?= esc($transaksi['nama_lengkap']) ?></div>
                                        <div class="small text-secondary"><?= esc($transaksi['kode_pelanggan']) ?></div>
                                    </td>
                                    <td>
                                        <?= esc(date('d/m/Y', strtotime($transaksi['tanggal_sewa']))) ?> -
                                        <?= esc(date('d/m/Y', strtotime($transaksi['tanggal_kembali']))) ?>
                                    </td>
                                    <td><span class="badge text-bg-light"><?= esc(ucfirst($transaksi['status_transaksi'])) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($transaksiTerakhir)) : ?>
                                <tr>
                                    <td colspan="4" class="text-center text-secondary">Belum ada riwayat transaksi.</td>
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
