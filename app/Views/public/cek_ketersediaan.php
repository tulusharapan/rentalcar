<?php
$appName     = trim((string) ($setting['nama_aplikasi'] ?? 'Rental Kendaraan')) ?: 'Rental Kendaraan';
$companyName = trim((string) ($setting['nama_perusahaan'] ?? $appName)) ?: $appName;
$logo        = $setting['logo_1'] ?? ($setting['logo'] ?? null);
$favicon     = $setting['favicon'] ?? null;
$logoUrl     = ! empty($logo) ? base_url('uploads/settings/' . $logo) : null;
$faviconUrl  = ! empty($favicon) ? base_url('uploads/settings/' . $favicon) : ($logoUrl ?: base_url('favicon.ico'));
$whatsappRaw = preg_replace('/[^0-9]/', '', (string) ($setting['no_whatsapp'] ?? ''));
$tanggalSewa = $filters['tanggal_sewa'] ?? '';
$tanggalKembali = $filters['tanggal_kembali'] ?? '';
$jenisKendaraan = $filters['jenis_kendaraan'] ?? '';
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
            padding: 116px 0 52px;
            background:
                radial-gradient(circle at 14% 10%, rgba(37, 99, 235, .18), transparent 28%),
                radial-gradient(circle at 84% 20%, rgba(20, 184, 166, .16), transparent 30%),
                linear-gradient(180deg, #f8fafc, #ffffff);
            border-bottom: 1px solid var(--site-border);
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
        }

        .section-block {
            padding: 56px 0;
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

        .vehicle-img {
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
            background: #eef2f6;
        }

        .summary-card {
            border: 1px solid var(--site-border);
            border-radius: 8px;
            background: #ffffff;
        }

        .card-heading {
            font-size: 1rem;
        }

        .footer-public {
            border-top: 1px solid var(--site-border);
            color: var(--site-muted);
        }
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <header class="page-hero">
        <div class="container">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('/') ?>" class="text-decoration-none">Beranda</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cek Ketersediaan</li>
                </ol>
            </nav>
            <div class="row g-4 align-items-end">
                <div class="col-lg-8">
                    <div class="section-label mb-2">Cek Ketersediaan</div>
                    <h1 class="section-title mb-3">Kendaraan tersedia pada tanggal pilihan Anda</h1>
                    <p class="text-secondary mb-0">Pilih kendaraan yang cocok, lalu klik tombol booking untuk menghubungi admin melalui WhatsApp.</p>
                </div>
                <div class="col-lg-4">
                    <div class="summary-card p-3">
                        <div class="row g-2 small">
                            <div class="col-6">
                                <div class="text-secondary">Tanggal Sewa</div>
                                <div class="fw-bold"><?= esc($tanggalSewa ?: '-') ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-secondary">Tanggal Kembali</div>
                                <div class="fw-bold"><?= esc($tanggalKembali ?: '-') ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-secondary">Jenis</div>
                                <div class="fw-bold"><?= esc($jenisKendaraan ?: 'Semua') ?></div>
                            </div>
                            <div class="col-6">
                                <div class="text-secondary">Lama Sewa</div>
                                <div class="fw-bold"><?= (int) $lamaSewa ?> hari</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="section-block">
            <div class="container">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <form action="<?= site_url('cek-ketersediaan') ?>" method="get" class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label for="tanggal_sewa" class="form-label fw-semibold">Tanggal Sewa</label>
                                <input type="date" class="form-control" id="tanggal_sewa" name="tanggal_sewa" value="<?= esc($tanggalSewa) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="tanggal_kembali" class="form-label fw-semibold">Tanggal Kembali</label>
                                <input type="date" class="form-control" id="tanggal_kembali" name="tanggal_kembali" value="<?= esc($tanggalKembali) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label for="jenis_kendaraan" class="form-label fw-semibold">Jenis Kendaraan</label>
                                <select class="form-select" id="jenis_kendaraan" name="jenis_kendaraan">
                                    <option value="">Semua kendaraan</option>
                                    <option value="Mobil" <?= $jenisKendaraan === 'Mobil' ? 'selected' : '' ?>>Mobil</option>
                                    <option value="Sepeda Motor" <?= $jenisKendaraan === 'Sepeda Motor' ? 'selected' : '' ?>>Sepeda Motor</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-dark btn-public w-100">
                                    <i class="bi bi-search me-1"></i>Cek Ulang
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if (! $isValidRange) : ?>
                    <div class="alert alert-warning border-0">
                        Tanggal kembali tidak boleh lebih kecil dari tanggal sewa. Silakan pilih rentang tanggal yang benar.
                    </div>
                <?php elseif (! empty($kendaraan)) : ?>
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-4">
                        <div>
                            <div class="section-label mb-2">Hasil Pencarian</div>
                            <h2 class="section-title mb-0"><?= count($kendaraan) ?> kendaraan tersedia</h2>
                        </div>
                        <a href="<?= site_url('/') ?>#armada" class="btn btn-outline-dark btn-public">Lihat Semua Armada</a>
                    </div>
                    <div class="row g-4">
                        <?php foreach ($kendaraan as $row) : ?>
                            <?php
                            $photoUrl = ! empty($row['foto_utama'])
                                ? base_url('uploads/kendaraan/' . $row['foto_utama'])
                                : base_url('uploads/kendaraan/fortuner_1.jpg');
                            $bookingText = rawurlencode(
                                'Halo admin, saya ingin booking ' . ($row['nama_kendaraan'] ?? 'kendaraan') .
                                ' (' . ($row['plat_nomor'] ?? '-') . ') dari tanggal ' . $tanggalSewa .
                                ' sampai ' . $tanggalKembali . '.'
                            );
                            $bookingUrl = $whatsappRaw !== '' ? 'https://wa.me/' . $whatsappRaw . '?text=' . $bookingText : '';
                            ?>
                            <div class="col-md-6 col-xl-3">
                                <article class="card vehicle-card h-100">
                                    <div class="position-relative">
                                        <img src="<?= esc($photoUrl, 'attr') ?>" alt="<?= esc($row['nama_kendaraan']) ?>" class="vehicle-img">
                                        <span class="badge text-bg-dark position-absolute top-0 start-0 m-3 rounded-pill px-3 py-2"><?= esc($row['jenis_kendaraan'] ?? 'Kendaraan') ?></span>
                                        <span class="badge text-bg-success position-absolute top-0 end-0 m-3 rounded-pill px-3 py-2">Tersedia</span>
                                    </div>
                                    <div class="card-body">
                                        <h3 class="card-heading fw-bold mb-1"><?= esc($row['nama_kendaraan']) ?></h3>
                                        <div class="text-secondary small mb-3"><?= esc($row['merk']) ?> - <?= esc($row['plat_nomor']) ?></div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge rounded-pill text-bg-light border"><?= esc($row['tahun']) ?></span>
                                            <span class="badge rounded-pill text-bg-light border"><?= esc($row['warna']) ?></span>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-white border-top">
                                        <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                                            <span class="text-secondary small fw-semibold">Estimasi sewa</span>
                                            <span class="fw-bold text-primary">Rp <?= number_format((int) $row['harga_sewa_per_hari'] * (int) $lamaSewa, 0, ',', '.') ?></span>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <?php if ($bookingUrl !== '') : ?>
                                                <a href="<?= esc($bookingUrl, 'attr') ?>" class="btn btn-success btn-public" target="_blank" rel="noopener">
                                                    <i class="bi bi-whatsapp me-1"></i>Booking
                                                </a>
                                            <?php else : ?>
                                                <button type="button" class="btn btn-secondary btn-public" disabled>WhatsApp belum diatur</button>
                                            <?php endif; ?>
                                            <a href="<?= site_url('kendaraan/' . $row['id']) ?>" class="btn btn-outline-dark btn-public">Detail</a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="alert alert-light border p-4 text-center">
                        <div class="fw-bold mb-1">Belum ada kendaraan tersedia pada rentang tanggal ini.</div>
                        <div class="text-secondary">Coba tanggal lain atau hubungi admin untuk rekomendasi jadwal terdekat.</div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
<?= $this->endSection() ?>
