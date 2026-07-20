<?= view('admin/layouts/header', ['title' => 'Data Pelanggan - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'pelanggan',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Data Pelanggan',
    'pageSubtitle' => 'Kelola identitas pelanggan rental.',
    'topbarAction' => '<a href="' . site_url('admin/pelanggan/create') . '" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i>Tambah Pelanggan</a>',
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
                        <th>Foto</th>
                        <th>Kode</th>
                        <th>Nama Lengkap</th>
                        <th>NIK</th>
                        <th>No. HP</th>
                        <th>Tanggal Terdaftar</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pelanggan as $row) : ?>
                        <tr>
                            <td>
                                <?php if (! empty($row['foto'])) : ?>
                                    <img src="<?= base_url('uploads/pelanggan/' . $row['foto']) ?>" alt="Foto <?= esc($row['nama_lengkap']) ?>" class="user-photo">
                                <?php else : ?>
                                    <div class="photo-placeholder"><?= esc(strtoupper(substr($row['nama_lengkap'], 0, 1))) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="fw-semibold"><?= esc($row['kode_pelanggan']) ?></td>
                            <td><?= esc($row['nama_lengkap']) ?></td>
                            <td><?= esc($row['nik']) ?></td>
                            <td><?= esc($row['no_hp']) ?></td>
                            <td><?= esc(date('d/m/Y', strtotime($row['tanggal_terdaftar']))) ?></td>
                            <td>
                                <?php if ($row['status'] === 'aktif') : ?>
                                    <span class="badge text-bg-success">Aktif</span>
                                <?php else : ?>
                                    <span class="badge text-bg-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="<?= site_url('admin/pelanggan/detail/' . $row['id']) ?>" rel="noopener" class="btn btn-sm btn-info btn-icon text-white" title="Detail pelanggan" aria-label="Detail pelanggan">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?= site_url('admin/pelanggan/edit/' . $row['id']) ?>" class="btn btn-sm btn-primary btn-icon" title="Edit pelanggan" aria-label="Edit pelanggan">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="<?= site_url('admin/pelanggan/delete/' . $row['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus data pelanggan ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-danger btn-icon" title="Hapus pelanggan" aria-label="Hapus pelanggan">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
