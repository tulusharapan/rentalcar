<?= view('admin/layouts/header', ['title' => 'Backup Database - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'backup',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
    'userRole'   => $userRole,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Backup Database',
    'pageSubtitle' => 'Unduh salinan database dalam format SQL.',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<section class="content-area">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div class="panel p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-4">
                    <div>
                        <div class="icon-box warning mb-3">
                            <i class="bi bi-database-down"></i>
                        </div>
                        <h2 class="h5 fw-bold mb-2">Backup Database SQL</h2>
                        <div class="text-secondary">
                            File backup berisi struktur tabel dan seluruh data dari database aktif.
                        </div>
                    </div>

                    <a href="<?= site_url('admin/backup/download') ?>" class="btn btn-dark">
                        <i class="bi bi-download me-1"></i>Download SQL
                    </a>
                </div>

                <div class="row g-3 mt-4">
                    <div class="col-12 col-md-6">
                        <div class="border rounded-3 p-3">
                            <div class="text-secondary small fw-semibold mb-1">Database</div>
                            <div class="fw-bold"><?= esc($databaseName) ?></div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="border rounded-3 p-3">
                            <div class="text-secondary small fw-semibold mb-1">Jumlah Tabel</div>
                            <div class="fw-bold"><?= esc($tableCount) ?> tabel</div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning small mt-4 mb-0" role="alert">
                    Simpan file backup di tempat yang aman karena file SQL dapat berisi data user dan pengaturan aplikasi.
                </div>
            </div>
        </div>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
