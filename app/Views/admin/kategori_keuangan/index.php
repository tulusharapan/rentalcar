<?= view('admin/layouts/header', ['title' => 'Kategori Keuangan - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'kategori-keuangan',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Kategori Keuangan',
    'pageSubtitle' => 'Kelola kategori untuk pencatatan keuangan.',
    'topbarAction' => '<a href="' . site_url('admin/kategori-keuangan/create') . '" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i>Tambah Kategori</a>',
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

    <div class="panel p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kategoriKeuangan as $row) : ?>
                        <?php $isLocked = in_array((int) $row['id'], [1, 2], true); ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= esc($row['kategori']) ?></div>
                                <?php if ((int) $row['id'] === 1) : ?>
                                    <span class="badge text-bg-secondary">Lainnya</span>
                                <?php elseif ((int) $row['id'] === 2) : ?>
                                    <span class="badge text-bg-primary">Pemasukan Sewa Kendaraan</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end text-nowrap">
                                <?php if ($isLocked) : ?>
                                    <span class="badge text-bg-light"><i class="bi bi-lock-fill me-1"></i>Terkunci</span>
                                <?php else : ?>
                                    <a href="<?= site_url('admin/kategori-keuangan/edit/' . $row['id']) ?>" class="btn btn-sm btn-primary btn-icon" title="Edit kategori" aria-label="Edit kategori">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="<?= site_url('admin/kategori-keuangan/delete/' . $row['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus kategori keuangan ini? Semua transaksi keuangan yang menggunakan kategori ini akan dipindahkan ke kategori Lainnya.')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-danger btn-icon" title="Hapus kategori" aria-label="Hapus kategori">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
