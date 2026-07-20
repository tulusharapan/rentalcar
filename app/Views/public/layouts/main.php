<?php
$setting = $setting ?? (function_exists('app_setting') ? app_setting() : []);
$appName = trim((string) ($setting['nama_aplikasi'] ?? 'Rental Kendaraan')) ?: 'Rental Kendaraan';
$logo    = $setting['logo_1'] ?? ($setting['logo'] ?? null);
$favicon = $setting['favicon'] ?? null;
$logoUrl = ! empty($logo) ? base_url('uploads/settings/' . $logo) : null;
$faviconUrl = ! empty($favicon) ? base_url('uploads/settings/' . $favicon) : ($logoUrl ?: base_url('favicon.ico'));
$title = $title ?? ($appName . ' - Rental Mobil dan Motor');
$active = $active ?? '';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?></title>
    <link rel="icon" href="<?= esc($faviconUrl, 'attr') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --site-dark: #101828;
            --site-muted: #667085;
            --site-border: #e4e7ec;
            --site-soft: #f8fafc;
            --site-primary: #2563eb;
            --site-success: #16a34a;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            color: #101828;
            background: #ffffff;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            font-size: .94rem;
            letter-spacing: 0;
        }

        .navbar-public {
            background: rgba(255, 255, 255, .94);
            border-bottom: 1px solid rgba(228, 231, 236, .9);
            backdrop-filter: blur(14px);
        }

        .brand-logo,
        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            flex: 0 0 auto;
        }

        .brand-logo {
            object-fit: contain;
            padding: 4px;
            border: 1px solid var(--site-border);
            background: #ffffff;
        }

        .brand-mark {
            display: grid;
            place-items: center;
            background: var(--site-dark);
            color: #ffffff;
            font-weight: 900;
        }

        .btn-public {
            min-height: 40px;
            border-radius: 8px;
            font-weight: 800;
        }

        .page-hero {
            position: relative;
            padding: 116px 0 52px;
            background:
                radial-gradient(circle at 14% 10%, rgba(37, 99, 235, .18), transparent 28%),
                radial-gradient(circle at 84% 20%, rgba(20, 184, 166, .16), transparent 30%),
                linear-gradient(180deg, #f8fafc, #ffffff);
            border-bottom: 1px solid var(--site-border);
            overflow: hidden;
        }

        .section-block {
            padding: 64px 0;
        }

        .section-soft {
            background: var(--site-soft);
        }

        .section-label {
            color: var(--site-primary);
            font-size: .72rem;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .section-title {
            font-size: clamp(1.45rem, 2.4vw, 2rem);
            line-height: 1.18;
            font-weight: 950;
            letter-spacing: 0;
        }

        .section-desc {
            color: var(--site-muted);
            line-height: 1.7;
        }

        .icon-box {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: #eff6ff;
            color: var(--site-primary);
            font-size: 1.1rem;
        }

        .vehicle-card,
        .recommendation-card {
            border: 1px solid var(--site-border);
            border-radius: 8px;
            overflow: hidden;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .vehicle-card:hover,
        .recommendation-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 42px rgba(16, 24, 40, .10);
        }

        .vehicle-img,
        .recommendation-img {
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
            background: #eef2f6;
        }

        .card-heading {
            font-size: 1rem;
        }

        .footer-public {
            border-top: 1px solid var(--site-border);
            color: var(--site-muted);
        }
    </style>
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <?= view('public/layouts/navbar', ['setting' => $setting, 'active' => $active]) ?>

    <?= $this->renderSection('content') ?>

    <?= view('public/layouts/footer', ['setting' => $setting]) ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
