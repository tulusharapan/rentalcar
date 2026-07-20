<?php
$appSetting = function_exists('app_setting') ? app_setting() : [];
$appName    = trim((string) ($appSetting['nama_aplikasi'] ?? 'Aplikasi')) ?: 'Aplikasi';
$appTagline = trim((string) ($appSetting['tagline'] ?? 'Kelola operasional rental kendaraan dalam satu panel.')) ?: 'Kelola operasional rental kendaraan dalam satu panel.';
$appLogo    = $appSetting['logo_2'] ?? ($appSetting['logo_1'] ?? ($appSetting['logo'] ?? null));
$appFavicon = $appSetting['favicon'] ?? null;
$logoUrl    = ! empty($appLogo) ? base_url('uploads/settings/' . $appLogo) : null;
$faviconUrl = ! empty($appFavicon) ? base_url('uploads/settings/' . $appFavicon) : ($logoUrl ?: base_url('favicon.ico'));
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - <?= esc($appName) ?></title>
    <link rel="icon" href="<?= esc($faviconUrl, 'attr') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --login-bg: #f6f8fb;
            --login-ink: #101828;
            --login-muted: #667085;
            --login-border: #d9e0ea;
            --login-panel: #ffffff;
            --login-dark: #101828;
            --login-blue: #2563eb;
            --login-green: #16a34a;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background:
                linear-gradient(135deg, rgba(37, 99, 235, .08), rgba(22, 163, 74, .05) 42%, rgba(246, 248, 251, 1) 70%),
                var(--login-bg);
            color: var(--login-ink);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            letter-spacing: 0;
        }

        .auth-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(420px, 1.35fr) minmax(390px, .98fr);
        }

        .brand-panel {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 34px;
            background: var(--login-dark);
            color: #ffffff;
        }

        .brand-lockup,
        .mobile-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .brand-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand-logo,
        .brand-mark {
            width: 50px;
            height: 50px;
            flex: 0 0 50px;
            border-radius: 8px;
            background: #ffffff;
        }

        .brand-logo {
            object-fit: contain;
            padding: 5px;
        }

        .brand-mark {
            display: grid;
            place-items: center;
            color: var(--login-dark);
            font-weight: 800;
            font-size: 20px;
        }

        .brand-name {
            font-size: 1.08rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .brand-tagline {
            color: #cbd5e1;
            font-size: .82rem;
            line-height: 1.35;
        }

        .back-site-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 36px;
            padding: 7px 12px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, .16);
            color: #ffffff;
            text-decoration: none;
            font-size: .84rem;
            font-weight: 800;
            background: rgba(255, 255, 255, .06);
            transition: background .18s ease, border-color .18s ease, color .18s ease;
            white-space: nowrap;
        }

        .back-site-link:hover,
        .back-site-link:focus {
            color: #ffffff;
            border-color: rgba(255, 255, 255, .28);
            background: rgba(255, 255, 255, .12);
        }

        .form-back-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 14px;
            color: var(--login-muted);
            text-decoration: none;
            font-size: .86rem;
            font-weight: 800;
        }

        .form-back-link:hover,
        .form-back-link:focus {
            color: var(--login-blue);
        }

        .brand-copy {
            max-width: 580px;
            padding: 48px 0;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 28px;
            padding: 5px 10px;
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 999px;
            background: rgba(255, 255, 255, .06);
            color: #dbeafe;
            font-size: .78rem;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .brand-copy h1 {
            max-width: 500px;
            margin: 0 0 14px;
            font-size: clamp(2.15rem, 4vw, 3rem);
            line-height: 1.03;
            font-weight: 850;
        }

        .brand-copy p {
            max-width: 465px;
            margin: 0;
            color: #cbd5e1;
            font-size: 1rem;
            line-height: 1.7;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            max-width: 520px;
        }

        .feature-item {
            display: flex;
            gap: 10px;
            padding: 14px;
            border: 1px solid rgba(255, 255, 255, .13);
            border-radius: 8px;
            background: rgba(255, 255, 255, .05);
        }

        .feature-icon {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            flex: 0 0 34px;
            border-radius: 8px;
            background: rgba(37, 99, 235, .18);
            color: #93c5fd;
        }

        .feature-title {
            font-weight: 800;
            line-height: 1.2;
        }

        .feature-text {
            color: #cbd5e1;
            font-size: .8rem;
            line-height: 1.45;
            margin-top: 3px;
        }

        .form-panel {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px;
        }

        .form-wrap {
            width: min(100%, 432px);
        }

        .mobile-brand {
            display: none;
            margin-bottom: 18px;
        }

        .mobile-brand .brand-name {
            color: var(--login-ink);
        }

        .mobile-brand .brand-tagline {
            color: var(--login-muted);
        }

        .login-card {
            border: 1px solid var(--login-border);
            border-radius: 8px;
            background: var(--login-panel);
            box-shadow: 0 24px 60px rgba(16, 24, 40, .10);
        }

        .login-card-body {
            padding: 30px;
        }

        .form-heading {
            margin-bottom: 22px;
        }

        .form-heading h2 {
            margin: 0 0 7px;
            font-size: 1.65rem;
            font-weight: 850;
            line-height: 1.15;
        }

        .form-heading p {
            margin: 0;
            color: var(--login-muted);
            font-size: .92rem;
            line-height: 1.55;
        }

        .form-label {
            color: #344054;
            font-size: .88rem;
        }

        .input-group-text {
            width: 43px;
            justify-content: center;
            border-color: var(--login-border);
            border-radius: 4px 0 0 4px;
            background: #f8fafc;
            color: #64748b;
        }

        .form-control {
            min-height: 43px;
            border-color: var(--login-border);
            border-radius: 0 4px 4px 0;
            padding: .58rem .78rem;
            font-size: .92rem;
        }

        .form-control:focus {
            border-color: var(--login-blue);
            box-shadow: 0 0 0 .2rem rgba(37, 99, 235, .10);
        }

        .password-input {
            border-radius: 0;
        }

        .password-toggle {
            width: 43px;
            border-color: var(--login-border);
            border-left: 0;
            border-radius: 0 4px 4px 0;
            background: #ffffff;
            color: #64748b;
        }

        .password-toggle:hover,
        .password-toggle:focus {
            border-color: var(--login-border);
            background: #f8fafc;
            color: var(--login-ink);
        }

        .btn-login {
            min-height: 44px;
            border-radius: 4px;
            border-color: var(--login-dark);
            background: var(--login-dark);
            font-weight: 800;
        }

        .btn-login:hover,
        .btn-login:focus {
            border-color: #1d2939;
            background: #1d2939;
        }

        .login-footnote {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            margin-top: 16px;
            color: var(--login-muted);
            font-size: .82rem;
        }

        .alert {
            border-radius: 6px;
            font-size: .9rem;
        }

        @media (max-width: 991.98px) {
            .auth-shell {
                display: block;
            }

            .brand-panel {
                display: none;
            }

            .form-panel {
                min-height: 100vh;
                padding: 24px 18px;
            }

            .mobile-brand {
                display: flex;
            }
        }

        @media (max-width: 575.98px) {
            .form-panel {
                align-items: flex-start;
                padding: 18px 14px;
            }

            .login-card-body {
                padding: 22px;
            }
        }
    </style>
</head>

<body>
    <main class="auth-shell">
        <section class="brand-panel" aria-label="<?= esc($appName, 'attr') ?>">
            <div class="brand-top">
                <div class="brand-lockup">
                    <?php if ($logoUrl) : ?>
                        <img src="<?= esc($logoUrl, 'attr') ?>" alt="Logo <?= esc($appName) ?>" class="brand-logo">
                    <?php else : ?>
                        <div class="brand-mark"><?= esc(strtoupper(substr($appName, 0, 1))) ?></div>
                    <?php endif; ?>
                    <div class="min-w-0">
                        <div class="brand-name"><?= esc($appName) ?></div>
                        <div class="brand-tagline"><?= esc($appTagline) ?></div>
                    </div>
                </div>
                <a href="<?= site_url('/') ?>" class="back-site-link">
                    <i class="bi bi-arrow-left"></i>
                    Website
                </a>
            </div>

            <div class="brand-copy">
                <div class="eyebrow">
                    <i class="bi bi-shield-check"></i>
                    Control Panel
                </div>
                <h1>Kelola rental kendaraan dengan lebih rapi.</h1>
                <p>Masuk untuk memantau armada, transaksi sewa, pembayaran, laporan, dan aktivitas operasional harian.</p>
            </div>

            <div class="feature-grid">
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-car-front-fill"></i></div>
                    <div>
                        <div class="feature-title">Armada</div>
                        <div class="feature-text">Pantau kendaraan ready, booking, disewa, dan maintenance.</div>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-receipt-cutoff"></i></div>
                    <div>
                        <div class="feature-title">Transaksi</div>
                        <div class="feature-text">Kelola invoice, pembayaran, denda, dan kuitansi.</div>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                    <div>
                        <div class="feature-title">Dashboard</div>
                        <div class="feature-text">Lihat ringkasan keuangan dan status operasional.</div>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon"><i class="bi bi-lock-fill"></i></div>
                    <div>
                        <div class="feature-title">Akses Aman</div>
                        <div class="feature-text">Gunakan akun internal untuk masuk ke panel admin.</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="form-panel">
            <div class="form-wrap">
                <a href="<?= site_url('/') ?>" class="form-back-link">
                    <i class="bi bi-arrow-left"></i>
                    Kembali ke Website
                </a>

                <div class="mobile-brand">
                    <?php if ($logoUrl) : ?>
                        <img src="<?= esc($logoUrl, 'attr') ?>" alt="Logo <?= esc($appName) ?>" class="brand-logo">
                    <?php else : ?>
                        <div class="brand-mark"><?= esc(strtoupper(substr($appName, 0, 1))) ?></div>
                    <?php endif; ?>
                    <div class="min-w-0">
                        <div class="brand-name"><?= esc($appName) ?></div>
                        <div class="brand-tagline"><?= esc($appTagline) ?></div>
                    </div>
                </div>

                <div class="login-card">
                    <div class="login-card-body">
                        <div class="form-heading">
                            <h2>Masuk</h2>                            
                        </div>

                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?= esc(session()->getFlashdata('error')) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('success')) : ?>
                            <div class="alert alert-success" role="alert">
                                <?= esc(session()->getFlashdata('success')) ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= site_url('login') ?>" method="post">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        value="<?= old('email') ?>"
                                        placeholder="nama@email.com"
                                        autocomplete="email"
                                        required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input
                                        type="password"
                                        class="form-control password-input"
                                        id="password"
                                        name="password"
                                        placeholder="Masukkan password"
                                        autocomplete="current-password"
                                        required>
                                    <button class="btn password-toggle" type="button" id="passwordToggle" aria-label="Tampilkan password" title="Tampilkan password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-dark btn-login w-100">
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                Masuk
                            </button>
                        </form>

                        <div class="login-footnote">
                            <i class="bi bi-info-circle"></i>
                            Akses hanya untuk pengguna yang berwenang.
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordToggle = document.getElementById('passwordToggle');

        if (passwordInput && passwordToggle) {
            passwordToggle.addEventListener('click', function() {
                const isHidden = passwordInput.type === 'password';
                const icon = passwordToggle.querySelector('i');

                passwordInput.type = isHidden ? 'text' : 'password';
                passwordToggle.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
                passwordToggle.setAttribute('title', isHidden ? 'Sembunyikan password' : 'Tampilkan password');

                if (icon) {
                    icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
                }
            });
        }
    </script>
</body>

</html>
