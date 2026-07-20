<?= view('admin/layouts/header', ['title' => 'Tambah User - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'users',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Tambah User',
    'pageSubtitle' => 'Isi data user baru dan upload foto bila tersedia.',
    'topbarAction' => '<a href="' . site_url('admin/users') . '" class="btn btn-outline-dark"><i class="bi bi-arrow-left me-1"></i>Kembali</a>',
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
        <form action="<?= site_url('admin/users/store') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <label for="name" class="form-label fw-semibold">Nama</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                </div>

                <div class="col-12 col-lg-6">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                </div>

                <div class="col-12 col-lg-6">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="form-text">Minimal 6 karakter.</div>
                </div>

                <div class="col-12 col-lg-3">
                    <label for="role" class="form-label fw-semibold">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="administrator" <?= old('role') === 'administrator' ? 'selected' : '' ?>>Administrator</option>
                        <option value="staff" <?= old('role') === 'staff' ? 'selected' : '' ?>>Staff</option>
                    </select>
                </div>

                <div class="col-12 col-lg-3">
                    <label for="is_active" class="form-label fw-semibold">Status</label>
                    <select class="form-select" id="is_active" name="is_active" required>
                        <option value="1" <?= old('is_active', '1') === '1' ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= old('is_active') === '0' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>

                <div class="col-12">
                    <label for="photo" class="form-label fw-semibold">Foto User</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept=".jpg,.jpeg,.png,.webp,image/*">
                    <div class="form-text">Format jpg, jpeg, png, atau webp. Maksimal 2 MB.</div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="<?= site_url('admin/users') ?>" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-dark">Simpan User</button>
            </div>
        </form>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
