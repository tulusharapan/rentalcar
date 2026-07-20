<?php
$appName     = trim((string) ($setting['nama_aplikasi'] ?? 'Rental Kendaraan')) ?: 'Rental Kendaraan';
$companyName = trim((string) ($setting['nama_perusahaan'] ?? $appName)) ?: $appName;
$logo        = $setting['logo_1'] ?? ($setting['logo'] ?? null);
$favicon     = $setting['favicon'] ?? null;
$logoUrl     = ! empty($logo) ? base_url('uploads/settings/' . $logo) : null;
$faviconUrl  = ! empty($favicon) ? base_url('uploads/settings/' . $favicon) : ($logoUrl ?: base_url('favicon.ico'));
$mainPhoto   = ! empty($fotoKendaraan[0]['file_name'])
    ? base_url('uploads/kendaraan/' . $fotoKendaraan[0]['file_name'])
    : base_url('uploads/kendaraan/fortuner_1.jpg');
$isReady     = ($kendaraan['status'] ?? '') === 'ready';
$whatsappRaw = preg_replace('/[^0-9]/', '', (string) ($setting['no_whatsapp'] ?? ''));
$message     = rawurlencode('Halo admin, saya ingin bertanya tentang sewa ' . ($kendaraan['nama_kendaraan'] ?? 'kendaraan') . ' plat ' . ($kendaraan['plat_nomor'] ?? '') . '.');
$whatsappUrl = $whatsappRaw !== '' ? 'https://wa.me/' . $whatsappRaw . '?text=' . $message : '';
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
            padding: 116px 0 46px;
            background:
                radial-gradient(circle at 12% 12%, rgba(37, 99, 235, .18), transparent 28%),
                radial-gradient(circle at 86% 22%, rgba(20, 184, 166, .16), transparent 30%),
                linear-gradient(180deg, #f8fafc, #ffffff);
            border-bottom: 1px solid var(--site-border);
            overflow: hidden;
        }

        .section-block {
            padding: 56px 0;
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

        .detail-shell {
            margin-top: -18px;
        }

        .photo-panel,
        .booking-panel,
        .info-panel {
            border: 1px solid var(--site-border);
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 16px 40px rgba(16, 24, 40, .06);
        }

        .main-photo {
            width: 100%;
            aspect-ratio: 16 / 10;
            object-fit: cover;
            border-radius: 8px;
            background: #eef2f6;
        }

        .thumb-photo {
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
            border-radius: 8px;
            background: #eef2f6;
            border: 1px solid var(--site-border);
        }

        .price-card {
            border-radius: 8px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
        }

        .price-text {
            font-size: 1.55rem;
            color: var(--site-primary);
            font-weight: 900;
        }

        .spec-item {
            padding: 14px;
            border: 1px solid var(--site-border);
            border-radius: 8px;
            background: #ffffff;
        }

        .spec-label {
            color: var(--site-muted);
            font-size: .72rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .spec-value {
            margin-top: 4px;
            font-weight: 850;
        }

        .sticky-panel {
            position: sticky;
            top: 96px;
        }

        .icon-box {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: #eff6ff;
            color: var(--site-primary);
        }

        .card-heading {
            font-size: 1rem;
        }

        .service-row {
            padding: 10px 0;
            border-bottom: 1px solid var(--site-border);
        }

        .service-row:last-child {
            border-bottom: 0;
        }

        .recommendation-card {
            border: 1px solid var(--site-border);
            border-radius: 8px;
            overflow: hidden;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .recommendation-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 42px rgba(16, 24, 40, .10);
        }

        .recommendation-img {
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
            background: #eef2f6;
        }

        .footer-public {
            border-top: 1px solid var(--site-border);
            color: var(--site-muted);
        }

        @media (max-width: 991.98px) {
            .sticky-panel {
                position: static;
            }
        }
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <header class="page-hero">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('/') ?>" class="text-decoration-none">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('/') ?>#armada" class="text-decoration-none">Armada</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= esc($kendaraan['nama_kendaraan']) ?></li>
                </ol>
            </nav>
            <div class="row g-4 align-items-end">
                <div class="col-lg-8">
                    <div class="section-label mb-2">Detail Kendaraan</div>
                    <h1 class="section-title mb-3"><?= esc($kendaraan['nama_kendaraan']) ?></h1>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge text-bg-dark rounded-pill px-3 py-2"><?= esc($kendaraan['jenis_kendaraan'] ?? 'Kendaraan') ?></span>
                        <span class="badge <?= $isReady ? 'text-bg-success' : 'text-bg-warning' ?> rounded-pill px-3 py-2"><?= $isReady ? 'Ready' : 'Maintenance' ?></span>
                        <span class="badge text-bg-light border rounded-pill px-3 py-2"><?= esc($kendaraan['merk']) ?></span>
                        <span class="badge text-bg-light border rounded-pill px-3 py-2"><?= esc($kendaraan['plat_nomor']) ?></span>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="price-card p-3 text-lg-end">
                        <div class="small text-secondary fw-semibold">Harga sewa per hari</div>
                        <div class="price-text">Rp <?= number_format((int) $kendaraan['harga_sewa_per_hari'], 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="section-block detail-shell">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="photo-panel p-3 mb-4">
                            <img src="<?= esc($mainPhoto, 'attr') ?>" alt="<?= esc($kendaraan['nama_kendaraan']) ?>" class="main-photo">
                            <?php if (! empty($fotoKendaraan)) : ?>
                                <div class="row g-3 mt-1">
                                    <?php foreach (array_slice($fotoKendaraan, 0, 4) as $foto) : ?>
                                        <div class="col-6 col-md-3">
                                            <img src="<?= base_url('uploads/kendaraan/' . $foto['file_name']) ?>" alt="Foto <?= esc($kendaraan['nama_kendaraan']) ?>" class="thumb-photo">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="info-panel p-4 mb-4">
                            <div class="section-label mb-2">Spesifikasi</div>
                            <h2 class="card-heading fw-bold mb-3">Informasi kendaraan</h2>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="spec-item h-100">
                                        <div class="spec-label">Kode</div>
                                        <div class="spec-value"><?= esc($kendaraan['kode_kendaraan']) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="spec-item h-100">
                                        <div class="spec-label">Plat Nomor</div>
                                        <div class="spec-value"><?= esc($kendaraan['plat_nomor']) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="spec-item h-100">
                                        <div class="spec-label">Jenis</div>
                                        <div class="spec-value"><?= esc($kendaraan['jenis_kendaraan'] ?? 'Kendaraan') ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="spec-item h-100">
                                        <div class="spec-label">Merk</div>
                                        <div class="spec-value"><?= esc($kendaraan['merk']) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="spec-item h-100">
                                        <div class="spec-label">Tahun</div>
                                        <div class="spec-value"><?= esc($kendaraan['tahun']) ?></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="spec-item h-100">
                                        <div class="spec-label">Warna</div>
                                        <div class="spec-value"><?= esc($kendaraan['warna']) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="info-panel p-4">
                            <div class="section-label mb-2">Keterangan</div>
                            <h2 class="card-heading fw-bold mb-3">Deskripsi kendaraan</h2>
                            <p class="text-secondary mb-0" style="line-height:1.75;"><?= nl2br(esc($kendaraan['keterangan'] ?: 'Informasi tambahan kendaraan belum tersedia. Hubungi admin untuk detail kondisi kendaraan, syarat sewa, dan ketersediaan jadwal.')) ?></p>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="sticky-panel">
                            <div class="booking-panel p-4 mb-4">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="icon-box"><i class="bi bi-calendar2-check"></i></div>
                                    <div>
                                        <div class="fw-bold card-heading">Booking kendaraan ini</div>
                                        <div class="text-secondary small">Cek jadwal dan syarat sewa ke admin.</div>
                                    </div>
                                </div>
                                <div class="price-card p-3 mb-3">
                                    <div class="small text-secondary fw-semibold">Harga sewa per hari</div>
                                    <div class="price-text">Rp <?= number_format((int) $kendaraan['harga_sewa_per_hari'], 0, ',', '.') ?></div>
                                </div>
                                <?php if ($whatsappUrl !== '') : ?>
                                    <a href="<?= esc($whatsappUrl, 'attr') ?>" class="btn btn-success btn-public w-100" target="_blank" rel="noopener">
                                        <i class="bi bi-whatsapp me-1"></i>Hubungi WhatsApp
                                    </a>
                                <?php else : ?>
                                    <div class="alert alert-light border mb-0">Nomor WhatsApp belum diatur.</div>
                                <?php endif; ?>
                                <a href="<?= site_url('cek-ketersediaan') ?>?jenis_kendaraan=<?= urlencode((string) ($kendaraan['jenis_kendaraan'] ?? '')) ?>" class="btn btn-outline-dark btn-public w-100 mt-2">
                                    Cek Ketersediaan
                                </a>
                            </div>

                            <div class="booking-panel p-4">
                                <div class="section-label mb-2">Layanan</div>
                                <h2 class="card-heading fw-bold mb-3">Layanan tambahan</h2>
                                <?php if (! empty($layanan)) : ?>
                                    <?php foreach ($layanan as $item) : ?>
                                        <div class="service-row d-flex align-items-center justify-content-between gap-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi <?= esc($item['icon'] ?: 'bi-plus-circle') ?> text-primary"></i>
                                                <span class="fw-semibold"><?= esc($item['nama_layanan']) ?></span>
                                            </div>
                                            <span class="small text-secondary">Rp <?= number_format((int) $item['harga_layanan'], 0, ',', '.') ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <div class="text-secondary">Belum ada layanan tambahan.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section-block section-soft">
            <div class="container">
                <div class="row align-items-end g-3 mb-4">
                    <div class="col-lg-8">
                        <div class="section-label mb-2">Rekomendasi</div>
                        <h2 class="section-title mb-2">Kendaraan lainnya</h2>
                        <p class="text-secondary mb-0">Pilihan kendaraan lain yang bisa dibandingkan sebelum menghubungi admin.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="<?= site_url('/') ?>#armada" class="btn btn-outline-dark btn-public">Lihat Semua Armada</a>
                    </div>
                </div>

                <?php if (! empty($rekomendasi)) : ?>
                    <div class="row g-4">
                        <?php foreach ($rekomendasi as $row) : ?>
                            <?php
                            $photoUrl = ! empty($row['foto_utama'])
                                ? base_url('uploads/kendaraan/' . $row['foto_utama'])
                                : base_url('uploads/kendaraan/fortuner_1.jpg');
                            $rowReady = ($row['status'] ?? '') === 'ready';
                            ?>
                            <div class="col-md-6 col-xl-3">
                                <article class="card recommendation-card h-100 position-relative">
                                    <div class="position-relative">
                                        <img src="<?= esc($photoUrl, 'attr') ?>" alt="<?= esc($row['nama_kendaraan']) ?>" class="recommendation-img">
                                        <span class="badge <?= $rowReady ? 'text-bg-success' : 'text-bg-warning' ?> position-absolute top-0 end-0 m-3 rounded-pill px-3 py-2"><?= $rowReady ? 'Ready' : 'Maintenance' ?></span>
                                    </div>
                                    <div class="card-body">
                                        <h3 class="card-heading fw-bold mb-1"><?= esc($row['nama_kendaraan']) ?></h3>
                                        <div class="small text-secondary mb-3"><?= esc($row['merk']) ?> - <?= esc($row['plat_nomor']) ?></div>
                                        <div class="fw-bold text-primary">Rp <?= number_format((int) $row['harga_sewa_per_hari'], 0, ',', '.') ?>/hari</div>
                                        <a href="<?= site_url('kendaraan/' . $row['id']) ?>" class="stretched-link" aria-label="Lihat detail <?= esc($row['nama_kendaraan'], 'attr') ?>"></a>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="alert alert-light border mb-0">Belum ada rekomendasi kendaraan lain.</div>
                <?php endif; ?>
            </div>
        </section>
    </main>
<?= $this->endSection() ?>
