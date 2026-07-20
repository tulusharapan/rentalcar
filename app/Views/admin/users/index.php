<?= view('admin/layouts/header', ['title' => 'Data User - Aplikasi']) ?>
<?php $canManageUsers = in_array(($userRole ?? ''), ['admin', 'administrator'], true); ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'users',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Data User',
    'pageSubtitle' => 'Kelola akun admin dan pengguna aplikasi.',
    'topbarAction' => $canManageUsers ? '<a href="' . site_url('admin/users/create') . '" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i>Tambah User</a>' : '',
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
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <?php if ($canManageUsers) : ?>
                            <th class="text-end">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr>
                            <td>
                                <?php if (! empty($user['photo'])) : ?>
                                    <img src="<?= base_url('uploads/users/' . $user['photo']) ?>" alt="Foto <?= esc($user['name']) ?>" class="user-photo">
                                <?php else : ?>
                                    <div class="photo-placeholder"><?= esc(strtoupper(substr($user['name'], 0, 1))) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="fw-semibold"><?= esc($user['name']) ?></td>
                            <td><?= esc($user['email']) ?></td>
                            <td><span class="badge text-bg-primary"><?= esc(in_array($user['role'], ['admin', 'administrator'], true) ? 'Administrator' : ucfirst((string) $user['role'])) ?></span></td>
                            <td>
                                <?php if ((int) $user['is_active'] === 1) : ?>
                                    <span class="badge text-bg-success">Aktif</span>
                                <?php else : ?>
                                    <span class="badge text-bg-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <?php if ($canManageUsers) : ?>
                                <td class="text-end text-nowrap">
                                    <a href="<?= site_url('admin/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-primary btn-icon" title="Edit user" aria-label="Edit user">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <?php if ((int) $user['id'] !== 1) : ?>
                                        <form action="<?= site_url('admin/users/delete/' . $user['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-danger btn-icon" title="Hapus user" aria-label="Hapus user">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
