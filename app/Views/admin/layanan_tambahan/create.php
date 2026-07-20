<?= view('admin/layouts/header', ['title' => 'Tambah Layanan Tambahan - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'layanan-tambahan',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Tambah Layanan Tambahan',
    'pageSubtitle' => 'Isi nama, harga, dan pilih icon layanan.',
    'topbarAction' => '<a href="' . site_url('admin/layanan-tambahan') . '" class="btn btn-outline-dark"><i class="bi bi-arrow-left me-1"></i>Kembali</a>',
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
        <form action="<?= site_url('admin/layanan-tambahan/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row g-4">

                <div class="col-12 col-lg-12">
                    <label class="form-label fw-semibold">Icon</label>
                    <div class="d-flex align-items-center gap-2">
                        <?php $selectedIcon = old('icon', 'bi-stars'); ?>
                        <div class="icon-box" id="selectedIconPreview">
                            <i class="bi <?= esc($selectedIcon) ?>"></i>
                        </div>
                        <input type="hidden" id="icon" name="icon" value="<?= esc($selectedIcon) ?>">
                        <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#iconPickerModal">
                            Pilih Icon
                        </button>
                    </div>
                </div>

                <div class="col-12 col-lg-12">
                    <label for="nama_layanan" class="form-label fw-semibold">Nama Layanan</label>
                    <input type="text" class="form-control" id="nama_layanan" name="nama_layanan" value="<?= old('nama_layanan') ?>" placeholder="Contoh: Antar Jemput" required>
                </div>

                <div class="col-12 col-lg-12">
                    <label for="harga_layanan" class="form-label fw-semibold">Harga Layanan</label>
                    <input type="text" class="form-control currency-input" id="harga_layanan" name="harga_layanan" value="<?= old('harga_layanan') ?>" required>
                </div>
                
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="<?= site_url('admin/layanan-tambahan') ?>" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-dark">Simpan Layanan</button>
            </div>
        </form>
    </div>
</section>

<?= view('admin/layanan_tambahan/icon_picker', ['iconOptions' => $iconOptions]) ?>
<?= view('admin/layouts/footer') ?>
