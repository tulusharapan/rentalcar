<?php
$appSetting = function_exists('app_setting') ? app_setting() : [];
$appName    = trim((string) ($appSetting['nama_aplikasi'] ?? 'Aplikasi')) ?: 'Aplikasi';
$appLogo    = $appSetting['logo_1'] ?? ($appSetting['logo'] ?? null);
$appFavicon = $appSetting['favicon'] ?? null;
$logoUrl    = ! empty($appLogo) ? base_url('uploads/settings/' . $appLogo) : null;
$pageTitle  = isset($title) ? str_replace('Aplikasi', $appName, $title) : 'Admin - ' . $appName;
$faviconUrl = ! empty($appFavicon) ? base_url('uploads/settings/' . $appFavicon) : ($logoUrl ?: base_url('favicon.ico'));
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle) ?></title>
    <link rel="icon" href="<?= esc($faviconUrl, 'attr') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/vendor/datatables/dataTables.bootstrap5.min.css') ?>" rel="stylesheet">
    <?php if (! empty($extraHead)) : ?>
        <?= $extraHead ?>
    <?php endif; ?>
    <script src="<?= base_url('assets/vendor/jquery/jquery-3.7.1.min.js') ?>"></script>

    <style>
        :root {
            --admin-bg: #f7f9fc;
            --admin-sidebar: #101828;
            --admin-sidebar-soft: #1d2939;
            --admin-text: #101828;
            --admin-border: #e4e7ec;
            --admin-card: #ffffff;
            --admin-primary: #2563eb;
            --admin-success: #16a34a;
            --admin-warning: #f59e0b;
        }

        body {
            min-height: 100vh;
            background: var(--admin-bg);
            color: var(--admin-text);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .admin-layout {
            height: 100vh;
            display: flex;
            overflow: hidden;
        }

        .sidebar {            
            width: 238px;
            flex: 0 0 238px;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: var(--admin-sidebar);
            color: #ffffff;
            padding: 20px 14px;
            transition: width .2s ease, flex-basis .2s ease, padding .2s ease;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,.15) transparent;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,.15);
            border-radius: 4px;
        }

        .sidebar.is-collapsed {
            width: 82px;
            flex-basis: 82px;
            padding: 20px 12px;
        }

        .brand-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 0 8px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.10);
            flex-shrink: 0;
        }

        .sidebar.is-collapsed .brand-box {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        .brand-mark {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: #ffffff;
            color: var(--admin-sidebar);
            font-weight: 800;
        }

        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .brand-text {
            min-width: 0;
            color: #ffffff;
        }

        .brand-title {
            font-size: .95rem;
            font-weight: 800;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 112px;
        }

        .brand-subtitle {
            margin-top: 2px;
            color: #98a2b3;
            font-size: .75rem;
            font-weight: 600;
            line-height: 1.1;
            white-space: nowrap;
        }

        .brand-logo {
            height: 44px;
            object-fit: contain;
            padding: 2px;
            border-radius: 8px;
            background: #ffffff;
            flex: 0 0 auto;
        }

        .brand-logo.brand-logo-expanded {
            width: auto;
            max-width: 166px;
        }

        .brand-logo-collapsed {
            display: none;
            width: 44px;
        }

        .sidebar.is-collapsed .brand-logo-expanded {
            display: none;
        }

        .sidebar.is-collapsed .brand-logo-collapsed {
            display: grid;
        }

        .sidebar.is-collapsed img.brand-logo-collapsed {
            display: block;
        }

        .sidebar.is-collapsed .brand-text {
            display: none;
        }

        .sidebar-link span,
        .sidebar-footer,
        .menu-label {
            transition: opacity .15s ease, width .15s ease;
        }

        .sidebar.is-collapsed .sidebar-link span,
        .sidebar.is-collapsed .sidebar-footer {
            width: 0;
            opacity: 0;
            overflow: hidden;
            white-space: nowrap;
        }

        .sidebar.is-collapsed .menu-label {
            height: 1px;
            margin: 18px 8px 8px;
            overflow: hidden;
            opacity: .35;
            color: transparent;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        .menu-label {
            margin: 20px 10px 8px;
            color: #98a2b3;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .sidebar-menu {
            display: grid;
            gap: 3px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            font-size: 0.90rem;
            gap: 11px;
            min-height: 38px;
            padding: 8px 10px;
            border-radius: 8px;
            color: #d0d5dd;
            text-decoration: none;
            font-weight: 600;
        }

        .sidebar.is-collapsed .sidebar-link {
            justify-content: center;
            gap: 0;
            padding: 10px;
        }

        .sidebar-link i {
            width: 20px;
            flex: 0 0 20px;
            text-align: center;
            font-size: 1rem;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background: var(--admin-sidebar-soft);
            color: #ffffff;
        }

        .sidebar-footer {
            margin-top: 28px;
            padding: 14px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.06);
            color: #d0d5dd;
        }

        .sidebar-current-user {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 14px 0 8px;
            padding: 10px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.06);
            color: #ffffff;
            flex-shrink: 0;
        }

        .sidebar.is-collapsed .sidebar-current-user {
            justify-content: center;
            padding: 8px;
        }

        .sidebar.is-collapsed .sidebar-current-user .min-w-0 {
            display: none;
        }

        .sidebar-user-photo,
        .sidebar-user-photo-placeholder {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            flex: 0 0 auto;
        }

        .sidebar-user-photo {
            object-fit: cover;
            background: #ffffff;
        }

        .sidebar-user-photo-placeholder {
            display: grid;
            place-items: center;
            background: #ffffff;
            color: var(--admin-sidebar);
            font-weight: 800;
        }

        .min-w-0 {
            min-width: 0;
        }

        .main-area {
            flex: 1;
            min-width: 0;
            font-size: 0.90rem;
            overflow-y: auto;
            height: 100vh;
            scrollbar-width: thin;
            scrollbar-color: #d1d5db transparent;
        }

        .main-area::-webkit-scrollbar {
            width: 5px;
        }
        .main-area::-webkit-scrollbar-track {
            background: transparent;
        }
        .main-area::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 50;
            min-height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 10px 20px;
            background: #ffffff;
            border-bottom: 1px solid var(--admin-border);
            box-shadow: 0 1px 3px rgba(16, 24, 40, 0.04);
        }

        .topbar-sidebar-toggle {
            width: 32px;
            height: 32px;
            padding: 0;
            border-color: var(--admin-border);
        }

        .topbar-spacer {
            flex: 1;
        }

        .notification-toggle {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            border-color: var(--admin-border);
        }

        .notification-badge {
            min-width: 19px;
            height: 19px;
            display: inline-grid;
            place-items: center;
            padding: 0 5px;
            font-size: .68rem;
            border: 2px solid #ffffff;
        }

        .notification-menu {
            width: 390px;
            max-width: calc(100vw - 32px);
            border-color: var(--admin-border);
            border-radius: 8px;
            overflow: hidden;
        }

        .notification-head {
            padding: 14px 16px;
            background: #f8fafc;
            border-bottom: 1px solid var(--admin-border);
        }

        .notification-body {
            max-height: 430px;
            overflow-y: auto;
        }

        .notification-section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 12px 16px 6px;
            font-size: .75rem;
            font-weight: 800;
            text-transform: uppercase;
            color: #667085;
        }

        .notification-item {
            display: flex;
            gap: 10px;
            padding: 10px 16px;
            color: var(--admin-text);
            text-decoration: none;
            border-left: 3px solid transparent;
        }

        .notification-item:hover {
            background: #f8fafc;
        }

        .notification-item.danger {
            border-left-color: #dc2626;
        }

        .notification-item.warning {
            border-left-color: #f59e0b;
        }

        .notification-item.primary {
            border-left-color: #2563eb;
        }

        .notification-icon {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            flex: 0 0 34px;
        }

        .notification-icon.danger {
            background: #fef2f2;
            color: #dc2626;
        }

        .notification-icon.warning {
            background: #fffbeb;
            color: #f59e0b;
        }

        .notification-icon.primary {
            background: #eff6ff;
            color: #2563eb;
        }

        .notification-empty {
            padding: 34px 18px;
            text-align: center;
            color: #667085;
        }

        .notification-footer {
            border-top: 1px solid var(--admin-border);
            padding: 10px 16px;
            background: #ffffff;
        }

        .content-area {
            padding: 20px;
        }

        .page-heading-area {
            position: sticky;
            top: 56px;
            z-index: 40;
            padding-bottom: 0;
            background: var(--admin-bg);
        }

        .page-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--admin-border);
        }

        .page-heading-action {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex: 0 0 auto;
        }

        .page-title {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 1px;
        }

        .panel,
        .summary-card {
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            background: var(--admin-card);
            box-shadow: 0 10px 28px rgba(16, 24, 40, 0.04);
        }

        .summary-card {
            height: 100%;
        }

        .icon-box {
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: #eff6ff;
            color: var(--admin-primary);
            font-size: 20px;
        }

        .icon-box.success {
            background: #ecfdf3;
            color: var(--admin-success);
        }

        .icon-box.warning {
            background: #fffbeb;
            color: var(--admin-warning);
        }

        .activity-item {
            display: flex;
            gap: 12px;
            padding: 16px 0;
            border-bottom: 1px solid var(--admin-border);
        }

        .activity-item:last-child {
            border-bottom: 0;
        }

        .activity-dot {
            width: 10px;
            height: 10px;
            margin-top: 7px;
            border-radius: 999px;
            background: var(--admin-primary);
            flex: 0 0 auto;
        }

        .profile-chip {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            background: #ffffff;
            color: var(--admin-text);
            text-decoration: none;
        }

        .user-menu-toggle {
            min-height: 42px;
            max-width: 280px;
        }

        .user-menu-photo {
            object-fit: cover;
        }

        .user-menu {
            min-width: 230px;
            border-color: var(--admin-border);
            border-radius: 8px;
        }

        .user-menu .dropdown-item {
            display: flex;
            align-items: center;
            padding: .45rem .9rem;
            font-size: .9rem;
        }

        .avatar,
        .photo-placeholder {
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: #eef2ff;
            color: #4338ca;
            font-weight: 800;
        }

        .avatar {
            width: 36px;
            height: 36px;
        }

        .user-photo,
        .photo-placeholder {
            width: 44px;
            height: 44px;
        }

        .preview-photo,
        .preview-placeholder {
            width: 88px;
            height: 88px;
        }

        .user-photo,
        .preview-photo {
            object-fit: cover;
            border-radius: 8px;
            background: #eef2ff;
        }

        .setting-logo-preview {
            object-fit: contain;
            padding: 4px;
            border: 1px solid var(--admin-border);
            background: #ffffff;
        }

        .form-control,
        .form-select {
            border-radius: 2px;
            padding: .3rem .9rem;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 31px;
            border-radius: 2px;
            border-color: var(--admin-border);
            font-size: .9rem;
        }

        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
            padding-top: .15rem;
        }

        .select2-container--bootstrap-5 .select2-dropdown {
            border-color: var(--admin-border);
            border-radius: 4px;
        }

        .btn {
            --bs-btn-padding-y: .25rem;
            --bs-btn-padding-x: .5rem;
            --bs-btn-font-size: .875rem;
            --bs-btn-border-radius: .25rem;
            min-height: 31px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .25rem;
        }

        .btn i {
            font-size: .875rem;
            line-height: 1;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            display: inline-grid;
            place-items: center;
            padding: 0;
            line-height: 1;
        }

        .dt-container {
            color: var(--admin-text);
        }

        .dataTables_wrapper {
            color: var(--admin-text);
        }

        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select,
        .dt-container .dt-search input,
        .dt-container .dt-length select {
            border: 1px solid var(--admin-border);
            border-radius: 8px;
            padding: .5rem .75rem;
        }

        .dataTables_wrapper .dataTables_filter input,
        .dt-container .dt-search input {
            margin-left: .5rem;
        }

        .dataTables_wrapper .dataTables_length select,
        .dt-container .dt-length select {
            margin: 0 .4rem;
        }

        .dataTables_wrapper .page-link,
        .dt-container .page-link {
            color: var(--admin-sidebar);
            border-color: var(--admin-border);
        }

        .dataTables_wrapper .active > .page-link,
        .dataTables_wrapper .page-link.active,
        .dt-container .active > .page-link,
        .dt-container .page-link.active {
            background: var(--admin-sidebar);
            border-color: var(--admin-sidebar);
            color: #ffffff;
        }

        .panel .table thead th {
            background: #f8fafc;
            font-weight: 700;
            font-size: .78rem;
            color: #667085;
            text-transform: uppercase;
            letter-spacing: .04em;
            border-bottom: 2px solid var(--admin-border);
            padding: .55rem .75rem;
        }

        .panel .table thead th.text-end {
            text-align: right;
        }

        .panel .section-heading {
            font-size: .92rem;
            font-weight: 700;
            color: var(--admin-text);
            padding-bottom: 10px;
            border-bottom: 1px solid var(--admin-border);
            margin-bottom: 14px;
        }

        .mobile-menu-button {
            display: none;
        }

        @media (max-width: 991.98px) {
            .admin-layout {
                display: block;
                height: auto;
                overflow: visible;
            }

            .main-area {
                height: auto;
                overflow: visible;
            }

            .sidebar {
                width: 100%;
                flex-basis: auto;
                height: auto;
                overflow: visible;
                position: static;
                padding: 16px;
            }

            .sidebar.is-collapsed {
                width: 100%;
                flex-basis: auto;
                padding: 16px;
            }

            .sidebar.is-collapsed .brand-box {
                justify-content: space-between;
            }

            .sidebar.is-collapsed .sidebar-link span,
            .sidebar.is-collapsed .sidebar-footer,
            .sidebar.is-collapsed .menu-label {
                width: auto;
                height: auto;
                opacity: 1;
                overflow: visible;
                color: inherit;
                white-space: normal;
                border-top: 0;
            }

            .sidebar.is-collapsed img.brand-logo-expanded {
                display: block;
            }

            .sidebar.is-collapsed .brand-mark.brand-logo-expanded {
                display: grid;
            }

            .sidebar.is-collapsed .brand-logo-collapsed {
                display: none;
            }

            .sidebar.is-collapsed .sidebar-link {
                justify-content: flex-start;
                gap: 11px;
                padding: 10px 12px;
            }

            .topbar-sidebar-toggle {
                display: none;
            }

            .sidebar-menu-wrap {
                display: none;
                padding-top: 16px;
            }

            .sidebar-menu-wrap.show {
                display: block;
            }

            .brand-box {
                padding-bottom: 0;
                border-bottom: 0;
            }

            .mobile-menu-button {
                display: inline-flex;
            }

            .topbar {
                padding: 10px 20px;
                align-items: center;
                flex-direction: row;
            }

            .content-area {
                padding: 24px 20px;
            }

            .page-heading-area {
                position: sticky;
                top: 56px;
                z-index: 40;
                background: var(--admin-bg);
                padding-bottom: 0;
            }

            .page-heading {
                flex-direction: column;
                align-items: stretch;
            }

            .page-heading-action {
                justify-content: flex-start;
            }

            .user-menu-toggle {
                max-width: 220px;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
