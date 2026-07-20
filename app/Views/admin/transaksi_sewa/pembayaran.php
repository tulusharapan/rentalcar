<?= view('admin/layouts/header', ['title' => 'Pembayaran Sewa - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'transaksi-sewa',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Pembayaran Sewa',
    'pageSubtitle' => $transaksi['kode_transaksi'] . ' - ' . $transaksi['nama_lengkap'],
    'topbarAction' => '<a href="' . site_url('admin/transaksi-sewa') . '" class="btn btn-outline-dark"><i class="bi bi-arrow-left me-1"></i>Kembali</a>',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<section class="content-area">
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?php $errors = session()->getFlashdata('errors'); ?>
    <?php if ($errors) : ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
                <div><?= esc($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php $sisaTagihan = max(0, (int) $transaksi['total_tagihan'] - (int) $totalBayar); ?>

    <div class="row g-4">
        <div class="col-12 col-xl-4">
            <div class="panel p-4 h-100">
                <div class="section-heading mb-0">Ringkasan Invoice</div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-secondary">Total Tagihan</span>
                    <span>Rp <?= number_format((int) $transaksi['total_tagihan'], 0, ',', '.') ?></span>
                </div>
                <?php if ((int) ($transaksi['denda'] ?? 0) > 0) : ?>
                    <div class="d-flex justify-content-between py-2 border-bottom text-danger">
                        <span>Denda <?= esc((int) ($transaksi['hari_terlambat'] ?? 0)) ?> hari</span>
                        <span>Rp <?= number_format((int) $transaksi['denda'], 0, ',', '.') ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-secondary">Harga Denda / Hari</span>
                        <span>Rp <?= number_format((int) ($transaksi['harga_denda_per_hari'] ?? 0), 0, ',', '.') ?></span>
                    </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-secondary">Total Bayar</span>
                    <span>Rp <?= number_format((int) $totalBayar, 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between py-3 fs-5 fw-bold">
                    <span>Sisa Tagihan</span>
                    <span>Rp <?= number_format($sisaTagihan, 0, ',', '.') ?></span>
                </div>

                <?php if ($transaksi['status_pembayaran'] === 'lunas') : ?>
                    <span class="badge text-bg-success">Lunas</span>
                <?php elseif ($transaksi['status_pembayaran'] === 'belum_lunas') : ?>
                    <span class="badge text-bg-primary">Belum Lunas</span>
                <?php else : ?>
                    <span class="badge text-bg-secondary">Belum Bayar</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="panel p-4">
                <div class="section-heading mb-0">Tambah Pembayaran</div>
                <form action="<?= site_url('admin/transaksi-sewa/pembayaran/store/' . $transaksi['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-12 col-lg-4">
                            <label for="tanggal_pembayaran" class="form-label fw-semibold">Tanggal Pembayaran</label>
                            <input type="date" class="form-control" id="tanggal_pembayaran" name="tanggal_pembayaran" value="<?= old('tanggal_pembayaran', date('Y-m-d')) ?>" required>
                        </div>
                        <div class="col-12 col-lg-4">
                            <label for="jumlah_bayar" class="form-label fw-semibold">Jumlah Bayar</label>
                            <input type="text" class="form-control currency-input" id="jumlah_bayar" name="jumlah_bayar" value="<?= old('jumlah_bayar', $sisaTagihan) ?>" required>
                        </div>
                        <div class="col-12 col-lg-4">
                            <label for="metode_pembayaran" class="form-label fw-semibold">Metode Pembayaran</label>
                            <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                                <?php foreach ($metodePembayaranOptions as $value => $label) : ?>
                                    <option value="<?= esc($value) ?>" <?= old('metode_pembayaran', 'tunai') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="catatan" class="form-label fw-semibold">Catatan</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="2"><?= old('catatan') ?></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-dark" <?= $sisaTagihan <= 0 ? 'disabled' : '' ?>>Simpan Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-12">
            <div class="panel p-4">
                <div class="section-heading mb-0">Riwayat Pembayaran</div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable datatable-no-order">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Metode</th>
                                <th>Catatan</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $lastPaymentId = ! empty($pembayaran) ? (int) max(array_column($pembayaran, 'id')) : 0; ?>
                            <?php foreach ($pembayaran as $row) : ?>
                                <tr>
                                    <td><?= esc(date('d/m/Y', strtotime($row['tanggal_pembayaran']))) ?></td>
                                    <td>Rp <?= number_format((int) $row['jumlah_bayar'], 0, ',', '.') ?></td>
                                    <td><?= esc(ucfirst($row['metode_pembayaran'])) ?></td>
                                    <td><?= esc($row['catatan'] ?: '-') ?></td>
                                    <td class="text-end text-nowrap">
                                        <a href="<?= site_url('admin/transaksi-sewa/pembayaran/kuitansi/' . $transaksi['id'] . '/' . $row['id']) ?>" class="btn btn-sm btn-dark btn-icon" title="Cetak kuitansi" aria-label="Cetak kuitansi" target="_blank">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                        <?php if ((int) $row['id'] === $lastPaymentId) : ?>
                                            <form action="<?= site_url('admin/transaksi-sewa/pembayaran/delete/' . $transaksi['id'] . '/' . $row['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus pembayaran terakhir ini?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger btn-icon" title="Hapus pembayaran terakhir" aria-label="Hapus pembayaran terakhir">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        <?php else : ?>
                                            <span class="text-secondary small">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
