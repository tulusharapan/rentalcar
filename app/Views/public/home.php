<?php
$appName       = trim((string) ($setting['nama_aplikasi'] ?? 'Rental Kendaraan')) ?: 'Rental Kendaraan';
$companyName   = trim((string) ($setting['nama_perusahaan'] ?? $appName)) ?: $appName;
$tagline       = trim((string) ($setting['tagline'] ?? 'Sewa kendaraan harian dengan armada terawat dan proses mudah.')) ?: 'Sewa kendaraan harian dengan armada terawat dan proses mudah.';
$logo          = $setting['logo_1'] ?? ($setting['logo'] ?? null);
$favicon       = $setting['favicon'] ?? null;
$logoUrl       = ! empty($logo) ? base_url('uploads/settings/' . $logo) : null;
$faviconUrl    = ! empty($favicon) ? base_url('uploads/settings/' . $favicon) : ($logoUrl ?: base_url('favicon.ico'));
$featuredPhoto = null;

foreach ($kendaraan as $item) {
    if (! empty($item['foto_utama'])) {
        $featuredPhoto = $item['foto_utama'];
        break;
    }
}

$heroPhoto = $featuredPhoto
    ? base_url('uploads/kendaraan/' . $featuredPhoto)
    : base_url('uploads/kendaraan/fortuner_1.jpg');

$whatsappRaw = preg_replace('/[^0-9]/', '', (string) ($setting['no_whatsapp'] ?? ''));
$whatsappUrl = $whatsappRaw !== '' ? 'https://wa.me/' . $whatsappRaw : '';
$socialLinks = [
    ['label' => 'TikTok', 'icon' => 'bi-tiktok', 'url' => $setting['link_tiktok'] ?? ''],
    ['label' => 'Instagram', 'icon' => 'bi-instagram', 'url' => $setting['link_instagram'] ?? ''],
    ['label' => 'YouTube', 'icon' => 'bi-youtube', 'url' => $setting['link_youtube'] ?? ''],
    ['label' => 'Facebook', 'icon' => 'bi-facebook', 'url' => $setting['link_facebook'] ?? ''],
];
$pagerDetails = isset($pager) ? $pager->getDetails('kendaraan') : null;
?>
<?= $this->extend('public/layouts/main') ?>

<?= $this->section('styles') ?>
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
            letter-spacing: 0;
            font-size: .94rem;
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

        .hero-section {
            position: relative;
            overflow: hidden;
            min-height: 720px;
            display: flex;
            align-items: center;
            padding: 118px 0 72px;
            color: #ffffff;
            background:
                linear-gradient(90deg, rgba(16, 24, 40, .92), rgba(16, 24, 40, .76) 48%, rgba(16, 24, 40, .42)),
                url("<?= esc($heroPhoto, 'attr') ?>") center / cover no-repeat;
        }

        .hero-section::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 18% 20%, rgba(37, 99, 235, .48), transparent 28%),
                radial-gradient(circle at 76% 16%, rgba(20, 184, 166, .36), transparent 30%),
                radial-gradient(circle at 64% 80%, rgba(245, 158, 11, .26), transparent 30%),
                linear-gradient(120deg, rgba(37, 99, 235, .20), rgba(16, 24, 40, .16));
            mix-blend-mode: screen;
            opacity: .86;
            animation: heroColorShift 12s ease-in-out infinite alternate;
            pointer-events: none;
        }

        .hero-section::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, .055) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .055) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: linear-gradient(90deg, rgba(0,0,0,.82), rgba(0,0,0,.18));
            opacity: .34;
            pointer-events: none;
        }

        .hero-section > .container {
            position: relative;
            z-index: 2;
        }

        .hero-glow {
            position: absolute;
            width: 360px;
            height: 360px;
            border-radius: 999px;
            filter: blur(22px);
            opacity: .46;
            pointer-events: none;
            z-index: 1;
        }

        .hero-glow.one {
            right: 8%;
            top: 18%;
            background: rgba(37, 99, 235, .52);
            animation: heroFloatOne 10s ease-in-out infinite;
        }

        .hero-glow.two {
            left: 7%;
            bottom: 7%;
            width: 280px;
            height: 280px;
            background: rgba(20, 184, 166, .34);
            animation: heroFloatTwo 11s ease-in-out infinite;
        }

        .hero-accent-line {
            position: absolute;
            right: -120px;
            bottom: 90px;
            width: 460px;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.72), transparent);
            transform: rotate(-32deg);
            opacity: .45;
            animation: heroLineMove 7s ease-in-out infinite;
            pointer-events: none;
            z-index: 1;
        }

        @keyframes heroColorShift {
            0% {
                transform: scale(1) translate3d(0, 0, 0);
                filter: hue-rotate(0deg) saturate(1);
            }
            100% {
                transform: scale(1.06) translate3d(-18px, 10px, 0);
                filter: hue-rotate(14deg) saturate(1.2);
            }
        }

        @keyframes heroFloatOne {
            0%, 100% {
                transform: translate3d(0, 0, 0) scale(1);
            }
            50% {
                transform: translate3d(-34px, 24px, 0) scale(1.08);
            }
        }

        @keyframes heroFloatTwo {
            0%, 100% {
                transform: translate3d(0, 0, 0) scale(1);
            }
            50% {
                transform: translate3d(28px, -18px, 0) scale(1.06);
            }
        }

        @keyframes heroLineMove {
            0%, 100% {
                transform: rotate(-32deg) translateX(0);
            }
            50% {
                transform: rotate(-32deg) translateX(-42px);
            }
        }

        .hero-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 11px;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            color: #ffffff;
            font-size: .76rem;
            font-weight: 800;
        }

        .hero-title {
            max-width: 760px;
            font-size: clamp(1.3rem, 2.5vw, 2rem);
            line-height: 1.2;
            font-weight: 900;
            letter-spacing: 0;
        }

        .hero-text {
            max-width: 610px;
            color: rgba(255, 255, 255, .83);
            line-height: 1.75;
            font-size: 1rem;
        }

        .hero-card {
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 8px;
            background: rgba(255, 255, 255, .92);
            color: #101828;
            box-shadow: 0 24px 70px rgba(0, 0, 0, .22);
            backdrop-filter: blur(16px);
        }

        .stat-card {
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 8px;
            background: rgba(255, 255, 255, .11);
        }

        .section-block {
            padding: 80px 0;
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

        .vehicle-card {
            border: 1px solid var(--site-border);
            border-radius: 8px;
            overflow: hidden;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .vehicle-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 42px rgba(16, 24, 40, .10);
        }

        .vehicle-card:hover .vehicle-title {
            color: var(--site-primary);
        }

        .vehicle-img {
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
            background: #eef2f6;
        }

        .icon-box {
            width: 48px;
            height: 48px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: #eff6ff;
            color: var(--site-primary);
            font-size: 1.15rem;
        }

        .stat-number {
            font-size: 1.45rem;
        }

        .card-heading {
            font-size: 1rem;
        }

        .step-number {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: var(--site-dark);
            color: #ffffff;
            font-weight: 900;
        }

        .contact-panel {
            border-radius: 8px;
            background:
                linear-gradient(135deg, rgba(37, 99, 235, .95), rgba(23, 57, 126, 0.98)),
                var(--site-dark);
            color: #ffffff;
            overflow: hidden;
        }

        .contact-link,
        .social-link {
            color: #ffffff;
            text-decoration: none;
        }

        .contact-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 8px;
            background: rgba(255, 255, 255, .08);
            font-weight: 800;
        }

        .social-link {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 8px;
            background: rgba(255, 255, 255, .08);
        }

        .footer-public {
            border-top: 1px solid var(--site-border);
            color: var(--site-muted);
        }

        @media (max-width: 991.98px) {
            .hero-section {
                min-height: auto;
                padding: 112px 0 56px;
            }
        }
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <header class="hero-section">
        <span class="hero-glow one"></span>
        <span class="hero-glow two"></span>
        <span class="hero-accent-line"></span>
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-7">
                    <div class="hero-kicker mb-3"><i class="bi bi-stars"></i><?= esc($companyName) ?></div>
                    <h1 class="hero-title mb-3">Rental kendaraan siap jalan untuk kebutuhan harian Anda.</h1>
                    <p class="hero-text mb-4"><?= esc($tagline) ?> Pilih armada sesuai kebutuhan keluarga, bisnis, perjalanan luar kota, atau operasional lapangan.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="#armada" class="btn btn-light btn-public px-4">
                            <i class="bi bi-car-front me-1"></i>Lihat Armada
                        </a>
                        <?php if ($whatsappUrl !== '') : ?>
                            <a href="<?= esc($whatsappUrl, 'attr') ?>" class="btn btn-success btn-public px-4" target="_blank" rel="noopener">
                                <i class="bi bi-whatsapp me-1"></i>Booking WhatsApp
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="row g-3 mt-4">
                        <div class="col-6 col-md-3">
                            <div class="stat-card p-3 h-100">
                                <div class="stat-number fw-black fw-bold"><?= number_format((int) ($summary['total_kendaraan'] ?? 0), 0, ',', '.') ?></div>
                                <div class="small text-white-50 fw-semibold">Total Armada</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-card p-3 h-100">
                                <div class="stat-number fw-black fw-bold"><?= number_format((int) ($summary['ready'] ?? 0), 0, ',', '.') ?></div>
                                <div class="small text-white-50 fw-semibold">Siap Disewa</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-card p-3 h-100">
                                <div class="stat-number fw-black fw-bold"><?= number_format((int) ($summary['mobil'] ?? 0), 0, ',', '.') ?></div>
                                <div class="small text-white-50 fw-semibold">Mobil Ready</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-card p-3 h-100">
                                <div class="stat-number fw-black fw-bold"><?= number_format((int) ($summary['motor'] ?? 0), 0, ',', '.') ?></div>
                                <div class="small text-white-50 fw-semibold">Motor Ready</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="hero-card p-4 p-xl-5">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="icon-box"><i class="bi bi-calendar2-check"></i></div>
                            <div>
                                <div class="fw-bold card-heading">Cari mobil</div>
                                <div class="text-secondary small">Hubungi admin untuk cek unit dan jadwal.</div>
                            </div>
                        </div>
                        <form action="<?= site_url('cek-ketersediaan') ?>" method="get">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label for="hero_tanggal_sewa" class="form-label fw-semibold">Tanggal Sewa</label>
                                    <input type="date" class="form-control" id="hero_tanggal_sewa" name="tanggal_sewa" value="<?= date('Y-m-d') ?>" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="hero_tanggal_kembali" class="form-label fw-semibold">Tanggal Kembali</label>
                                    <input type="date" class="form-control" id="hero_tanggal_kembali" name="tanggal_kembali" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                                </div>
                                <div class="col-12">
                                    <label for="hero_jenis_kendaraan" class="form-label fw-semibold">Jenis Kendaraan</label>
                                    <select class="form-select" id="hero_jenis_kendaraan" name="jenis_kendaraan">
                                        <option value="">Semua kendaraan</option>
                                        <option value="Mobil">Mobil</option>
                                        <option value="Sepeda Motor">Sepeda Motor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="alert alert-primary d-flex gap-2 mt-4 mb-0" role="alert">
                                <i class="bi bi-info-circle"></i>
                                <div class="small">Hasil cek akan menampilkan kendaraan yang tersedia pada rentang tanggal tersebut.</div>
                            </div>
                            <button type="submit" class="btn btn-dark btn-public w-100 mt-3">
                                <i class="bi bi-search me-1"></i>Cek Ketersediaan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="section-block" id="armada">
            <div class="container">
                <div class="row align-items-end g-3 mb-4">
                    <div class="col-lg-8">
                        <div class="section-label mb-2">Armada Ready</div>
                        <h2 class="section-title mb-2">Data kendaraan rental</h2>
                        <p class="section-desc mb-0">Katalog ini menampilkan semua kendaraan publik yang tercatat aktif di sistem.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <?php if ($whatsappUrl !== '') : ?>
                            <a href="<?= esc($whatsappUrl, 'attr') ?>" class="btn btn-dark btn-public px-4" target="_blank" rel="noopener">
                                <i class="bi bi-whatsapp me-1"></i>Booking Sekarang
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (! empty($kendaraan)) : ?>
                    <div class="row g-4">
                        <?php foreach ($kendaraan as $row) : ?>
                            <?php
                            $photoUrl = ! empty($row['foto_utama'])
                                ? base_url('uploads/kendaraan/' . $row['foto_utama'])
                                : base_url('uploads/kendaraan/fortuner_1.jpg');
                            $isReady = ($row['status'] ?? '') === 'ready';
                            ?>
                            <div class="col-md-6 col-xl-3">
                                <article class="card vehicle-card h-100 position-relative">
                                    <div class="position-relative">
                                        <img src="<?= esc($photoUrl, 'attr') ?>" alt="<?= esc($row['nama_kendaraan']) ?>" class="vehicle-img">
                                        <span class="badge text-bg-dark position-absolute top-0 start-0 m-3 rounded-pill px-3 py-2"><?= esc($row['jenis_kendaraan'] ?? 'Kendaraan') ?></span>
                                        <span class="badge <?= $isReady ? 'text-bg-success' : 'text-bg-warning' ?> position-absolute top-0 end-0 m-3 rounded-pill px-3 py-2"><?= $isReady ? 'Ready' : 'Maintenance' ?></span>
                                    </div>
                                    <div class="card-body">
                                        <h3 class="card-heading fw-bold mb-1 vehicle-title"><?= esc($row['nama_kendaraan']) ?></h3>
                                        <div class="text-secondary small mb-3"><?= esc($row['merk']) ?> - <?= esc($row['plat_nomor']) ?></div>
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            <span class="badge rounded-pill text-bg-light border"><?= esc($row['tahun']) ?></span>
                                            <span class="badge rounded-pill text-bg-light border"><?= esc($row['warna']) ?></span>
                                        </div>
                                        <a href="<?= site_url('kendaraan/' . $row['id']) ?>" class="stretched-link" aria-label="Lihat detail <?= esc($row['nama_kendaraan'], 'attr') ?>"></a>
                                    </div>
                                    <div class="card-footer bg-white border-top d-flex align-items-center justify-content-between gap-3">
                                        <span class="text-secondary small fw-semibold">Harga</span>
                                        <span class="fw-bold text-primary">Rp <?= number_format((int) $row['harga_sewa_per_hari'], 0, ',', '.') ?>/hari</span>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (! empty($pagerDetails) && (int) ($pagerDetails['pageCount'] ?? 1) > 1) : ?>
                        <?php
                        $currentPage = (int) $pagerDetails['currentPage'];
                        $pageCount    = (int) $pagerDetails['pageCount'];
                        ?>
                        <nav class="mt-5" aria-label="Pagination kendaraan">
                            <ul class="pagination justify-content-center mb-0">
                                <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= site_url('/') ?>?page_kendaraan=<?= max(1, $currentPage - 1) ?>#armada" aria-label="Halaman sebelumnya">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                <?php for ($page = 1; $page <= $pageCount; $page++) : ?>
                                    <li class="page-item <?= $page === $currentPage ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= site_url('/') ?>?page_kendaraan=<?= $page ?>#armada"><?= $page ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?= $currentPage >= $pageCount ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= site_url('/') ?>?page_kendaraan=<?= min($pageCount, $currentPage + 1) ?>#armada" aria-label="Halaman berikutnya">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="alert alert-light border text-center p-4">
                        <div class="fw-bold">Belum ada kendaraan ready yang ditampilkan.</div>
                        <div class="text-secondary mt-1">Tambahkan data kendaraan atau ubah status kendaraan menjadi ready dari panel admin.</div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section class="section-block section-soft" id="keunggulan">
            <div class="container">
                <div class="row justify-content-between align-items-end g-3 mb-4">
                    <div class="col-lg-7">
                        <div class="section-label mb-2">Keunggulan</div>
                        <h2 class="section-title mb-2">Operasional rental lebih jelas dari awal.</h2>
                        <p class="section-desc mb-0">Informasi armada, harga, pembayaran, dan layanan tambahan dikelola rapi agar pelanggan mudah mengambil keputusan.</p>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="icon-box mb-3"><i class="bi bi-shield-check"></i></div>
                                <h3 class="card-heading fw-bold">Armada terawat</h3>
                                <p class="text-secondary mb-0">Kendaraan dipantau statusnya sehingga unit ready, maintenance, dan sedang disewa bisa dibedakan dengan jelas.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="icon-box mb-3" style="background:#ecfdf3;color:var(--site-success);"><i class="bi bi-calendar2-check"></i></div>
                                <h3 class="card-heading fw-bold">Jadwal mudah dicek</h3>
                                <p class="text-secondary mb-0">Admin dapat memastikan ketersediaan armada berdasarkan tanggal sewa dan tanggal kembali.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="icon-box mb-3" style="background:#fff7ed;color:#f97316;"><i class="bi bi-receipt"></i></div>
                                <h3 class="card-heading fw-bold">Biaya transparan</h3>
                                <p class="text-secondary mb-0">Harga sewa, layanan tambahan, pembayaran, invoice, dan denda tercatat dalam sistem operasional.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-block">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-5">
                        <div class="section-label mb-2">Cara Sewa</div>
                        <h2 class="section-title mb-3">Alur sewa sederhana dan cepat.</h2>
                        <p class="section-desc mb-0">Pelanggan cukup pilih kendaraan, konfirmasi jadwal, lalu admin menyiapkan transaksi dan invoice.</p>
                    </div>
                    <div class="col-lg-7">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body p-4">
                                        <div class="step-number mb-3">1</div>
                                        <h3 class="card-heading fw-bold">Pilih armada</h3>
                                        <p class="small text-secondary mb-0">Tentukan mobil atau motor sesuai kebutuhan perjalanan.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body p-4">
                                        <div class="step-number mb-3">2</div>
                                        <h3 class="card-heading fw-bold">Cek jadwal</h3>
                                        <p class="small text-secondary mb-0">Admin mengecek ketersediaan kendaraan pada tanggal sewa.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-body p-4">
                                        <div class="step-number mb-3">3</div>
                                        <h3 class="card-heading fw-bold">Konfirmasi sewa</h3>
                                        <p class="small text-secondary mb-0">Pembayaran dicatat dan invoice disiapkan dari sistem.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-block section-soft" id="layanan">
            <div class="container">
                <div class="row align-items-end g-3 mb-4">
                    <div class="col-lg-8">
                        <div class="section-label mb-2">Layanan Tambahan</div>
                        <h2 class="section-title mb-2">Tambahan layanan sesuai kebutuhan.</h2>
                        <p class="section-desc mb-0">Layanan pendukung dapat ditambahkan saat proses transaksi sewa.</p>
                    </div>
                </div>
                <div class="row g-4">
                    <?php if (! empty($layanan)) : ?>
                        <?php foreach ($layanan as $item) : ?>
                            <div class="col-md-6 col-xl-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <div class="icon-box mb-3"><i class="bi <?= esc($item['icon'] ?: 'bi-plus-circle') ?>"></i></div>
                                        <h3 class="card-heading fw-bold"><?= esc($item['nama_layanan']) ?></h3>
                                        <p class="text-secondary mb-3">Dapat ditambahkan saat proses transaksi sewa.</p>
                                        <div class="fw-bold text-primary">Rp <?= number_format((int) $item['harga_layanan'], 0, ',', '.') ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="icon-box mb-3"><i class="bi bi-geo-alt"></i></div>
                                    <h3 class="card-heading fw-bold">Antar jemput</h3>
                                    <p class="text-secondary mb-0">Layanan pengantaran dan penjemputan kendaraan sesuai area operasional.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="icon-box mb-3"><i class="bi bi-person-badge"></i></div>
                                    <h3 class="card-heading fw-bold">Driver</h3>
                                    <p class="text-secondary mb-0">Tambahan driver untuk perjalanan bisnis, keluarga, atau operasional.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="icon-box mb-3"><i class="bi bi-clock-history"></i></div>
                                    <h3 class="card-heading fw-bold">Sewa harian</h3>
                                    <p class="text-secondary mb-0">Durasi sewa fleksibel dengan perhitungan yang jelas per hari.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="section-block" id="kontak">
            <div class="container">
                <div class="contact-panel p-4 p-lg-5">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-7">
                            <div class="hero-kicker mb-3"><i class="bi bi-headset"></i>Kontak Rental</div>
                            <h2 class="section-title text-white mb-3">Butuh kendaraan untuk hari ini?</h2>
                            <p class="mb-0" style="color:rgba(255,255,255,.76);line-height:1.75;">Hubungi admin untuk cek unit, jadwal, persyaratan sewa, layanan tambahan, dan estimasi total biaya.</p>
                        </div>
                        <div class="col-lg-5">
                            <div class="d-grid gap-3">
                                <?php if (! empty($setting['email'])) : ?>
                                    <a class="contact-link" href="mailto:<?= esc($setting['email'], 'attr') ?>">
                                        <i class="bi bi-envelope"></i><?= esc($setting['email']) ?>
                                    </a>
                                <?php endif; ?>
                                <?php if ($whatsappUrl !== '') : ?>
                                    <a class="contact-link" href="<?= esc($whatsappUrl, 'attr') ?>" target="_blank" rel="noopener">
                                        <i class="bi bi-whatsapp"></i><?= esc($setting['no_whatsapp']) ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <?php foreach ($socialLinks as $social) : ?>
                                    <?php if (! empty($social['url'])) : ?>
                                        <a class="social-link" href="<?= esc($social['url'], 'attr') ?>" target="_blank" rel="noopener" aria-label="<?= esc($social['label']) ?>">
                                            <i class="bi <?= esc($social['icon']) ?>"></i>
                                        </a>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
<?= $this->endSection() ?>
