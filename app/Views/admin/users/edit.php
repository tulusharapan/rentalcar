<?= view('admin/layouts/header', ['title' => 'Edit User - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'users',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Edit User',
    'pageSubtitle' => 'Perbarui data akun dan foto user.',
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
        <form action="<?= site_url('admin/users/update/' . $user['id']) ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="mb-4">
                <div class="fw-semibold mb-2">Foto Saat Ini</div>
                <?php if (! empty($user['photo'])) : ?>
                    <img src="<?= base_url('uploads/users/' . $user['photo']) ?>" alt="Foto <?= esc($user['name']) ?>" class="preview-photo">
                <?php else : ?>
                    <div class="photo-placeholder preview-placeholder"><?= esc(strtoupper(substr($user['name'], 0, 1))) ?></div>
                <?php endif; ?>
            </div>

            <div class="row g-4">
                <div class="col-12 col-lg-6">
                    <label for="name" class="form-label fw-semibold">Nama</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $user['name']) ?>" required>
                </div>

                <div class="col-12 col-lg-6">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $user['email']) ?>" required>
                </div>

                <div class="col-12 col-lg-6">
                    <label for="password" class="form-label fw-semibold">Password Baru</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <div class="form-text">Kosongkan jika password tidak ingin diganti.</div>
                </div>

                <div class="col-12 col-lg-3">
                    <label for="role" class="form-label fw-semibold">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="administrator" <?= in_array(old('role', $user['role']), ['admin', 'administrator'], true) ? 'selected' : '' ?>>Administrator</option>
                        <option value="staff" <?= old('role', $user['role']) === 'staff' ? 'selected' : '' ?>>Staff</option>
                    </select>
                </div>

                <div class="col-12 col-lg-3">
                    <label for="is_active" class="form-label fw-semibold">Status</label>
                    <select class="form-select" id="is_active" name="is_active" required>
                        <option value="1" <?= (string) old('is_active', $user['is_active']) === '1' ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= (string) old('is_active', $user['is_active']) === '0' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>

                <div class="col-12">
                    <label for="photo" class="form-label fw-semibold">Ganti Foto User</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept=".jpg,.jpeg,.png,.webp,image/*">
                    <div class="form-text">Kosongkan jika foto tidak ingin diganti.</div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="<?= site_url('admin/users') ?>" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-dark">Update User</button>
            </div>
        </form>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
