<?= view('admin/layouts/header', ['title' => 'Tambah Kategori Keuangan - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'kategori-keuangan',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Tambah Kategori Keuangan',
    'pageSubtitle' => 'Isi nama kategori keuangan.',
    'topbarAction' => '<a href="' . site_url('admin/kategori-keuangan') . '" class="btn btn-outline-dark"><i class="bi bi-arrow-left me-1"></i>Kembali</a>',
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

    <div class="panel p-4">
        <form action="<?= site_url('admin/kategori-keuangan/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row g-4">
                <div class="col-12">
                    <label for="kategori" class="form-label fw-semibold">Kategori</label>
                    <input type="text" class="form-control" id="kategori" name="kategori" value="<?= old('kategori') ?>" placeholder="Contoh: Pendapatan Sewa" required>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="<?= site_url('admin/kategori-keuangan') ?>" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-dark">Simpan Kategori</button>
            </div>
        </form>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
