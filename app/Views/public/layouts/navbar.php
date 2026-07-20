<?php
$setting = $setting ?? (function_exists('app_setting') ? app_setting() : []);
$appName = trim((string) ($setting['nama_aplikasi'] ?? 'Rental Kendaraan')) ?: 'Rental Kendaraan';
$logo    = $setting['logo_1'] ?? ($setting['logo'] ?? null);
$logoUrl = ! empty($logo) ? base_url('uploads/settings/' . $logo) : null;
$active  = $active ?? '';
?>
<nav class="navbar navbar-expand-lg navbar-public fixed-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2 me-lg-4" href="<?= site_url('/') ?>">
            <?php if ($logoUrl) : ?>
                <img src="<?= esc($logoUrl, 'attr') ?>" alt="<?= esc($appName) ?>" class="brand-logo">
            <?php else : ?>
                <span class="brand-mark"><?= esc(strtoupper(substr($appName, 0, 1))) ?></span>
            <?php endif; ?>
            <span class="lh-sm">
                <span class="d-block fw-bold"><?= esc($appName) ?></span>               
            </span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbar" aria-controls="publicNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="publicNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2 mt-3 mt-lg-0">
                <li class="nav-item"><a class="nav-link fw-bold <?= $active === 'armada' ? 'active' : '' ?>" href="<?= site_url('/') ?>#armada">Armada</a></li>
                <li class="nav-item"><a class="nav-link fw-bold <?= $active === 'keunggulan' ? 'active' : '' ?>" href="<?= site_url('/') ?>#keunggulan">Keunggulan</a></li>
                <li class="nav-item"><a class="nav-link fw-bold <?= $active === 'layanan' ? 'active' : '' ?>" href="<?= site_url('/') ?>#layanan">Layanan</a></li>
                <li class="nav-item"><a class="nav-link fw-bold <?= $active === 'kontak' ? 'active' : '' ?>" href="<?= site_url('/') ?>#kontak">Kontak</a></li>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-primary btn-public px-3" href="<?= site_url('login') ?>">
                        <i class="bi bi-person-lock me-1"></i>Login Admin
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
