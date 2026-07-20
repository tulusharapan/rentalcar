<?php
$activeMenu = $activeMenu ?? '';
$userName = $userName ?? session()->get('userName');
$userEmail = $userEmail ?? session()->get('userEmail');
$userRole = $userRole ?? session()->get('userRole');
$appSetting = function_exists('app_setting') ? app_setting() : [];
$appName = trim((string) ($appSetting['nama_aplikasi'] ?? 'Aplikasi')) ?: 'Aplikasi';
$expandedLogo = $appSetting['logo_2'] ?? ($appSetting['logo_1'] ?? ($appSetting['logo'] ?? null));
$collapsedLogo = $appSetting['logo_1'] ?? ($appSetting['logo_2'] ?? ($appSetting['logo'] ?? null));
$roleLabel = in_array(($userRole ?? ''), ['admin', 'administrator'], true) ? 'Administrator' : ucfirst((string) ($userRole ?? '-'));
$isAdministrator = in_array(($userRole ?? ''), ['admin', 'administrator'], true);
$userPhoto = session()->get('userPhoto');
?>

<aside class="sidebar" id="appSidebar">
    <div class="brand-box">
        <div class="brand-logo-wrap">
            <?php if (! empty($expandedLogo)) : ?>
                <img src="<?= base_url('uploads/settings/' . $expandedLogo) ?>" alt="Logo <?= esc($appName) ?>" class="brand-logo brand-logo-expanded">
            <?php else : ?>
                <div class="brand-mark brand-logo-expanded">
                    <?= esc(strtoupper(substr($appName, 0, 1))) ?>
                </div>
            <?php endif; ?>

            <?php if (! empty($collapsedLogo)) : ?>
                <img src="<?= base_url('uploads/settings/' . $collapsedLogo) ?>" alt="Logo <?= esc($appName) ?>" class="brand-logo brand-logo-collapsed">
            <?php else : ?>
                <div class="brand-mark brand-logo-collapsed">
                    <?= esc(strtoupper(substr($appName, 0, 1))) ?>
                </div>
            <?php endif; ?>

            <div class="brand-text">
                <div class="brand-title"><?= esc($appName) ?></div>
                <div class="brand-subtitle">Control Panel</div>
            </div>
        </div>

        <button class="btn btn-sm btn-light mobile-menu-button" type="button" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <div class="sidebar-current-user">
        <?php if (! empty($userPhoto)) : ?>
            <img src="<?= base_url('uploads/users/' . $userPhoto) ?>" alt="Foto <?= esc($userName ?? '-') ?>" class="sidebar-user-photo">
        <?php else : ?>
            <div class="sidebar-user-photo-placeholder"><?= esc(strtoupper(substr((string) ($userName ?? '-'), 0, 1))) ?></div>
        <?php endif; ?>
        <div class="min-w-0">
            <div class="fw-semibold text-truncate"><?= esc($userName ?? '-') ?></div>
            <div class="small text-white-50 text-truncate"><?= esc($roleLabel) ?></div>
        </div>
    </div>

    <div class="sidebar-menu-wrap" id="sidebarMenu">


        <div class="menu-label">Menu Utama</div>
        <nav class="sidebar-menu">
            <a href="<?= site_url('admin/dashboard') ?>" class="sidebar-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= site_url('admin/pelanggan') ?>" class="sidebar-link <?= $activeMenu === 'pelanggan' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Data Pelanggan">
                <i class="bi bi-person-vcard-fill"></i>
                <span>Data Pelanggan</span>
            </a>
            <a href="<?= site_url('admin/kendaraan') ?>" class="sidebar-link <?= $activeMenu === 'kendaraan' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Data Kendaraan">
                <i class="bi bi-car-front-fill"></i>
                <span>Data Kendaraan</span>
            </a>
            <a href="<?= site_url('admin/layanan-tambahan') ?>" class="sidebar-link <?= $activeMenu === 'layanan-tambahan' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Layanan Tambahan">
                <i class="bi bi-stars"></i>
                <span>Layanan Tambahan</span>
            </a>
            <a href="<?= site_url('admin/transaksi-sewa') ?>" class="sidebar-link <?= $activeMenu === 'transaksi-sewa' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Transaksi Sewa">
                <i class="bi bi-receipt-cutoff"></i>
                <span>Transaksi Sewa</span>
            </a>
            <a href="<?= site_url('admin/kendaraan/monitor') ?>" class="sidebar-link <?= $activeMenu === 'monitor-kendaraan' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Monitor Kendaraan">
                <i class="bi bi-clipboard-data-fill"></i>
                <span>Monitor Kendaraan</span>
            </a>
           
        </nav>

        <?php if ($isAdministrator) : ?>
            <div class="menu-label">Keuangan</div>
            <nav class="sidebar-menu">
                <a href="<?= site_url('admin/kategori-keuangan') ?>" class="sidebar-link <?= $activeMenu === 'kategori-keuangan' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Kategori Keuangan">
                    <i class="bi bi-tags-fill"></i>
                    <span>Kategori Keuangan</span>
                </a>
                <a href="<?= site_url('admin/transaksi-keuangan') ?>" class="sidebar-link <?= $activeMenu === 'transaksi-keuangan' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Transaksi Keuangan">
                    <i class="bi bi-wallet2"></i>
                    <span>Transaksi Keuangan</span>
                </a>
            </nav>
        <?php endif; ?>

        <div class="menu-label">Laporan</div>
        <nav class="sidebar-menu">
             <a href="<?= site_url('admin/transaksi-sewa/laporan') ?>" class="sidebar-link <?= $activeMenu === 'laporan-transaksi' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Laporan Transaksi">
                <i class="bi bi-file-earmark-bar-graph-fill"></i>
                <span>Laporan Transaksi</span>
            </a>
            <?php if ($isAdministrator) : ?>
                <a href="<?= site_url('admin/transaksi-keuangan/laporan') ?>" class="sidebar-link <?= $activeMenu === 'laporan-keuangan' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Laporan Keuangan">
                    <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                    <span>Laporan Keuangan</span>
                </a>
            <?php endif; ?>
        </nav>


        <div class="menu-label">Pengaturan</div>
        <nav class="sidebar-menu">
            <?php if ($isAdministrator) : ?>
                <a href="<?= site_url('admin/users') ?>" class="sidebar-link <?= $activeMenu === 'users' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Data User">
                    <i class="bi bi-people-fill"></i>
                    <span>Data User</span>
                </a>
            <?php endif; ?>
            <a href="<?= site_url('admin/profile') ?>" class="sidebar-link <?= $activeMenu === 'profile' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Profil">
                <i class="bi bi-person-circle"></i>
                <span>Profil</span>
            </a>
            <a href="<?= site_url('admin/change-password') ?>" class="sidebar-link <?= $activeMenu === 'change-password' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Ganti Password">
                <i class="bi bi-key-fill"></i>
                <span>Ganti Password</span>
            </a>
            <?php if ($isAdministrator) : ?>
                <a href="<?= site_url('admin/setting') ?>" class="sidebar-link <?= $activeMenu === 'setting' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Setting">
                    <i class="bi bi-gear-fill"></i>
                    <span>Setting</span>
                </a>
                <a href="<?= site_url('admin/backup') ?>" class="sidebar-link <?= $activeMenu === 'backup' ? 'active' : '' ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Backup Database">
                    <i class="bi bi-database-down"></i>
                    <span>Backup Database</span>
                </a>
            <?php endif; ?>
            <a href="<?= site_url('logout') ?>" class="sidebar-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="small text-white-50 mb-1">Login sebagai</div>
            <div class="fw-semibold"><?= esc($userName ?? '-') ?></div>
            <div class="small text-white-50 text-truncate"><?= esc($userEmail ?? '-') ?></div>
        </div>
    </div>
</aside>
