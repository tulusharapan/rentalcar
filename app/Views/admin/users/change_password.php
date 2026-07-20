<?= view('admin/layouts/header', ['title' => 'Ganti Password - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'change-password',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Ganti Password',
    'pageSubtitle' => 'Perbarui password akun yang sedang login.',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<section class="content-area">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-7">
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
                <form action="<?= site_url('admin/change-password/update') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label for="old_password" class="form-label fw-semibold">Password Lama</label>
                        <input
                            type="password"
                            class="form-control"
                            id="old_password"
                            name="old_password"
                            placeholder="Masukkan password lama"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label for="new_password" class="form-label fw-semibold">Password Baru</label>
                        <input
                            type="password"
                            class="form-control"
                            id="new_password"
                            name="new_password"
                            placeholder="Masukkan password baru"
                            autocomplete="new-password"
                            required
                        >
                        <div class="form-text">Gunakan minimal 6 karakter.</div>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_new_password" class="form-label fw-semibold">Konfirmasi Password Baru</label>
                        <input
                            type="password"
                            class="form-control"
                            id="confirm_new_password"
                            name="confirm_new_password"
                            placeholder="Ulangi password baru"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-check2-circle me-1"></i>Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
