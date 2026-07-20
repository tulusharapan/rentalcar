<?= view('admin/layouts/header', ['title' => 'Tambah Transaksi Keuangan - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'transaksi-keuangan',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Tambah Transaksi Keuangan',
    'pageSubtitle' => 'Catat pemasukan atau pengeluaran baru.',
    'topbarAction' => '<a href="' . site_url('admin/transaksi-keuangan') . '" class="btn btn-outline-dark"><i class="bi bi-arrow-left me-1"></i>Kembali</a>',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<section class="content-area">
    <?php $errors = session()->getFlashdata('errors'); ?>
    <?php if ($errors) : ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
                <div><?= esc($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="panel p-4">
        <form action="<?= site_url('admin/transaksi-keuangan/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row g-4">
                <div class="col-12 col-lg-3">
                    <label for="tanggal" class="form-label fw-semibold">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= old('tanggal', date('Y-m-d')) ?>" required>
                </div>
                <div class="col-12 col-lg-3">
                    <label for="jenis" class="form-label fw-semibold">Jenis</label>
                    <select class="form-select" id="jenis" name="jenis" required>
                        <?php foreach ($jenisOptions as $jenis) : ?>
                            <option value="<?= esc($jenis) ?>" <?= old('jenis', 'Pemasukan') === $jenis ? 'selected' : '' ?>><?= esc($jenis) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-lg-3">
                    <label for="kategori_keuangan_id" class="form-label fw-semibold">Kategori</label>
                    <select class="form-select" id="kategori_keuangan_id" name="kategori_keuangan_id" required>
                        <option value="">Pilih kategori</option>
                        <?php foreach ($kategoriOptions as $kategori) : ?>
                            <option value="<?= esc($kategori['id']) ?>" <?= old('kategori_keuangan_id') === (string) $kategori['id'] ? 'selected' : '' ?>><?= esc($kategori['kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-lg-3">
                    <label for="nominal" class="form-label fw-semibold">Nominal</label>
                    <input type="text" class="form-control currency-input" id="nominal" name="nominal" value="<?= old('nominal') ?>" required>
                </div>
                <div class="col-12">
                    <label for="catatan" class="form-label fw-semibold">Catatan</label>
                    <textarea class="form-control" id="catatan" name="catatan" rows="3"><?= old('catatan') ?></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="<?= site_url('admin/transaksi-keuangan') ?>" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-dark">Simpan Transaksi</button>
            </div>
        </form>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
