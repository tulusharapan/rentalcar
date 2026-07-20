<?= view('admin/layouts/header', ['title' => 'Profil Saya - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'profile',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
    'userRole'   => $userRole,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Profil Saya',
    'pageSubtitle' => 'Perbarui informasi akun yang sedang login.',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<section class="content-area">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
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

            <div class="panel p-4">
                <form action="<?= site_url('admin/profile/update') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="d-flex align-items-center gap-3 mb-4">
                        <?php if (! empty($user['photo'])) : ?>
                            <img src="<?= base_url('uploads/users/' . $user['photo']) ?>" alt="Foto <?= esc($user['name']) ?>" class="preview-photo">
                        <?php else : ?>
                            <div class="photo-placeholder preview-placeholder">
                                <?= esc(strtoupper(substr((string) $user['name'], 0, 1))) ?>
                            </div>
                        <?php endif; ?>

                        <div>
                            <div class="fw-bold"><?= esc($user['name']) ?></div>
                            <div class="text-secondary"><?= esc($user['email']) ?></div>
                            <span class="badge text-bg-primary mt-2">
                                <?= esc(in_array(($user['role'] ?? ''), ['admin', 'administrator'], true) ? 'Administrator' : ucfirst((string) ($user['role'] ?? '-'))) ?>
                            </span>
                        </div>
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

                        <div class="col-12">
                            <label for="photo" class="form-label fw-semibold">Ganti Foto Profil</label>
                            <input type="file" class="form-control" id="photo" name="photo" accept=".jpg,.jpeg,.png,.webp,image/*">
                            <div class="form-text">Kosongkan jika foto tidak ingin diganti. Format jpg, jpeg, png, atau webp. Maksimal 2 MB.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center gap-2 mt-4">
                        <a href="<?= site_url('admin/change-password') ?>" class="btn btn-outline-dark">
                            <i class="bi bi-key me-1"></i>Ganti Password
                        </a>
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-check2-circle me-1"></i>Update Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
