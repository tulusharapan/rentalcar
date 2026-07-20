<?php
$currentUserName = $userName ?? session()->get('userName') ?? '-';
$currentUserRole = $userRole ?? session()->get('userRole') ?? '-';
$currentUserEmail = $userEmail ?? session()->get('userEmail') ?? '-';
$currentUserPhoto = session()->get('userPhoto');
$roleLabel = in_array(($currentUserRole ?? ''), ['admin', 'administrator'], true)
    ? 'Administrator'
    : ucfirst((string) $currentUserRole);
$initial = strtoupper(substr((string) $currentUserName, 0, 1));
$notifications = function_exists('rental_topbar_notifications') ? rental_topbar_notifications() : [
    'almost_due'    => [],
    'overdue'       => [],
    'starting_soon' => [],
    'total'         => 0,
];
?>

<main class="main-area">
    <header class="topbar">
        <button class="btn btn-light topbar-sidebar-toggle" type="button" id="sidebarCollapseToggle" aria-label="Perkecil sidebar" title="Perkecil sidebar">
            <i class="bi bi-layout-sidebar-inset"></i>
        </button>

        <div class="topbar-spacer"></div>

        <div class="dropdown">
            <button class="btn btn-light notification-toggle position-relative" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifikasi transaksi">
                <i class="bi bi-bell-fill"></i>
                <?php if ((int) $notifications['total'] > 0) : ?>
                    <span class="notification-badge position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-danger">
                        <?= esc((int) $notifications['total']) ?>
                    </span>
                <?php endif; ?>
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow-sm p-0 notification-menu">
                <div class="notification-head">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <div class="fw-semibold">Notifikasi Kendaraan</div>
                            <div class="small text-secondary">Pantau jadwal sewa yang perlu ditindaklanjuti.</div>
                        </div>
                        <span class="badge text-bg-dark"><?= esc((int) $notifications['total']) ?></span>
                    </div>
                </div>
                <div class="notification-body">
                    <?php if ((int) $notifications['total'] === 0) : ?>
                        <div class="notification-empty">
                            <div class="icon-box mx-auto mb-2">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <div class="fw-semibold">Semua aman</div>
                            <div class="small">Tidak ada kendaraan jatuh tempo, telat, atau booking yang segera berjalan.</div>
                        </div>
                    <?php endif; ?>

                    <?php if (! empty($notifications['overdue'])) : ?>
                        <div class="notification-section-title">
                            <span>Telat Dikembalikan</span>
                            <span class="badge text-bg-danger"><?= count($notifications['overdue']) ?></span>
                        </div>
                        <?php foreach ($notifications['overdue'] as $row) : ?>
                            <a href="<?= site_url('admin/transaksi-sewa') ?>" class="notification-item danger">
                                <div class="notification-icon danger"><i class="bi bi-exclamation-triangle-fill"></i></div>
                                <div class="min-w-0 flex-grow-1">
                                    <div class="d-flex justify-content-between gap-2">
                                        <div class="fw-semibold text-truncate"><?= esc($row['kode_transaksi']) ?></div>
                                        <div class="small text-danger text-nowrap"><?= esc(date('d/m/Y', strtotime($row['tanggal_kembali']))) ?></div>
                                    </div>
                                    <div class="text-truncate"><?= esc($row['nama_kendaraan']) ?> - <?= esc($row['plat_nomor']) ?></div>
                                    <div class="small text-secondary text-truncate"><?= esc($row['nama_lengkap']) ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (! empty($notifications['almost_due'])) : ?>
                        <div class="notification-section-title">
                            <span>Hampir Jatuh Tempo</span>
                            <span class="badge text-bg-warning"><?= count($notifications['almost_due']) ?></span>
                        </div>
                        <?php foreach ($notifications['almost_due'] as $row) : ?>
                            <a href="<?= site_url('admin/transaksi-sewa') ?>" class="notification-item warning">
                                <div class="notification-icon warning"><i class="bi bi-clock-fill"></i></div>
                                <div class="min-w-0 flex-grow-1">
                                    <div class="d-flex justify-content-between gap-2">
                                        <div class="fw-semibold text-truncate"><?= esc($row['kode_transaksi']) ?></div>
                                        <div class="small text-warning text-nowrap"><?= esc(date('d/m/Y', strtotime($row['tanggal_kembali']))) ?></div>
                                    </div>
                                    <div class="text-truncate"><?= esc($row['nama_kendaraan']) ?> - <?= esc($row['plat_nomor']) ?></div>
                                    <div class="small text-secondary text-truncate"><?= esc($row['nama_lengkap']) ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (! empty($notifications['starting_soon'])) : ?>
                        <div class="notification-section-title">
                            <span>Booking Akan Berjalan</span>
                            <span class="badge text-bg-primary"><?= count($notifications['starting_soon']) ?></span>
                        </div>
                        <?php foreach ($notifications['starting_soon'] as $row) : ?>
                            <a href="<?= site_url('admin/transaksi-sewa') ?>" class="notification-item primary">
                                <div class="notification-icon primary"><i class="bi bi-calendar-check-fill"></i></div>
                                <div class="min-w-0 flex-grow-1">
                                    <div class="d-flex justify-content-between gap-2">
                                        <div class="fw-semibold text-truncate"><?= esc($row['kode_transaksi']) ?></div>
                                        <div class="small text-primary text-nowrap"><?= esc(date('d/m/Y', strtotime($row['tanggal_sewa']))) ?></div>
                                    </div>
                                    <div class="text-truncate"><?= esc($row['nama_kendaraan']) ?> - <?= esc($row['plat_nomor']) ?></div>
                                    <div class="small text-secondary text-truncate"><?= esc($row['nama_lengkap']) ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="notification-footer">
                    <a href="<?= site_url('admin/transaksi-sewa') ?>" class="btn btn-light w-100">
                        <i class="bi bi-receipt-cutoff me-1"></i>Lihat Transaksi Sewa
                    </a>
                </div>
            </div>
        </div>

        <div class="dropdown">
            <button class="profile-chip user-menu-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?php if (! empty($currentUserPhoto)) : ?>
                    <img src="<?= base_url('uploads/users/' . $currentUserPhoto) ?>" alt="Foto <?= esc($currentUserName) ?>" class="avatar user-menu-photo">
                <?php else : ?>
                    <div class="avatar"><?= esc($initial) ?></div>
                <?php endif; ?>

                <div class="text-start min-w-0">
                    <div class="fw-semibold lh-sm text-truncate"><?= esc($currentUserName) ?></div>
                    <div class="small text-secondary text-truncate"><?= esc($roleLabel) ?></div>
                </div>
                <i class="bi bi-chevron-down small text-secondary"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-sm user-menu">
                <li class="px-3 py-2">
                    <div class="fw-semibold text-truncate"><?= esc($currentUserName) ?></div>
                    <div class="small text-secondary text-truncate"><?= esc($currentUserEmail) ?></div>
                    <span class="badge text-bg-light mt-2"><?= esc($roleLabel) ?></span>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="<?= site_url('admin/profile') ?>">
                        <i class="bi bi-person-circle me-2"></i>Update Profil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= site_url('admin/change-password') ?>">
                        <i class="bi bi-key me-2"></i>Ganti Password
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="<?= site_url('logout') ?>">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </header>

    <section class="content-area page-heading-area">
        <div class="page-heading">
            <div class="min-w-0">
                <div class="page-title"><?= esc($pageTitle ?? 'Admin') ?></div>
                <?php if (! empty($pageSubtitle)) : ?>
                    <div class="text-secondary"><?= esc($pageSubtitle) ?></div>
                <?php endif; ?>
            </div>

            <?php if (! empty($topbarAction)) : ?>
                <div class="page-heading-action">
                    <?= $topbarAction ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
