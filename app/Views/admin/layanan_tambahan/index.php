<?= view('admin/layouts/header', ['title' => 'Layanan Tambahan - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'layanan-tambahan',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Layanan Tambahan',
    'pageSubtitle' => 'Kelola layanan ekstra seperti antar jemput, driver, dan biaya tambahan lain.',
    'topbarAction' => '<a href="' . site_url('admin/layanan-tambahan/create') . '" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i>Tambah Layanan</a>',
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
                        <th width="1%">Icon</th>
                        <th>Nama Layanan</th>
                        <th>Harga Layanan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($layananTambahan as $layanan) : ?>
                        <tr>
                            <td>
                                <div class="icon-box">
                                    <i class="bi <?= esc($layanan['icon']) ?>"></i>
                                </div>
                            </td>
                            <td class="fw-semibold"><?= esc($layanan['nama_layanan']) ?></td>
                            <td>Rp <?= number_format((int) $layanan['harga_layanan'], 0, ',', '.') ?></td>
                            <td class="text-end text-nowrap">
                                <?php if (! empty($layanan['is_locked'])) : ?>
                                    <span class="badge text-bg-secondary me-1"><i class="bi bi-lock-fill me-1"></i>Terkunci</span>
                                    <button type="button" class="btn btn-sm btn-secondary btn-icon" title="Layanan sudah dipakai transaksi, sehingga tidak bisa diedit atau dihapus." aria-label="Layanan terkunci" disabled>
                                        <i class="bi bi-lock-fill"></i>
                                    </button>
                                <?php else : ?>
                                    <a href="<?= site_url('admin/layanan-tambahan/edit/' . $layanan['id']) ?>" class="btn btn-sm btn-primary btn-icon" title="Edit layanan" aria-label="Edit layanan">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="<?= site_url('admin/layanan-tambahan/delete/' . $layanan['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus layanan tambahan ini?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-danger btn-icon" title="Hapus layanan" aria-label="Hapus layanan">
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
