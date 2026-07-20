<?= view('admin/layouts/header', ['title' => 'Monitor Kendaraan - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'monitor-kendaraan',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Monitor Kendaraan',
    'pageSubtitle' => 'Cek ketersediaan kendaraan pada tanggal tertentu.',
    'topbarAction' => '<a href="' . site_url('admin/kendaraan') . '" class="btn btn-outline-dark"><i class="bi bi-arrow-left me-1"></i>Data Kendaraan</a>',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<section class="content-area">
    <?php
        $summary = $summary ?? [];
        $summaryItems = [
            ['label' => 'Total Armada', 'value' => $summary['total'] ?? count($kendaraan), 'class' => 'dark', 'icon' => 'bi-grid-3x3-gap-fill'],
            ['label' => 'Tersedia', 'value' => $summary['tersedia'] ?? 0, 'class' => 'success', 'icon' => 'bi-check-circle-fill'],
            ['label' => 'Disewa', 'value' => $summary['disewa'] ?? 0, 'class' => 'primary', 'icon' => 'bi-key-fill'],
            ['label' => 'Booking', 'value' => $summary['booking'] ?? 0, 'class' => 'warning', 'icon' => 'bi-calendar-check-fill'],
            ['label' => 'Telat', 'value' => $summary['telat'] ?? 0, 'class' => 'danger', 'icon' => 'bi-exclamation-octagon-fill'],
            ['label' => 'Maintenance', 'value' => $summary['maintenance'] ?? 0, 'class' => 'secondary', 'icon' => 'bi-tools'],
        ];
    ?>

    <div class="panel p-4 mb-4">
        <div class="d-flex flex-column flex-xl-row justify-content-between gap-3">
            <div>
                <div class="text-secondary small fw-semibold mb-1">Tanggal yang dipantau</div>
                <div class="h4 fw-bold mb-1"><?= esc(date('d/m/Y', strtotime($tanggal))) ?></div>
                <div class="text-secondary small">Status dihitung dari jadwal sewa dan status master kendaraan pada tanggal yang dipilih.</div>
            </div>
            <form method="get" action="<?= site_url('admin/kendaraan/monitor') ?>" class="d-flex flex-column flex-sm-row align-items-sm-end gap-2">
                <div>
                    <label for="tanggal" class="form-label fw-semibold mb-1">Ubah Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= esc($tanggal) ?>" required>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-dark"><i class="bi bi-search"></i>Cek</button>
                    <a href="<?= site_url('admin/kendaraan/monitor') ?>" class="btn btn-light">Hari Ini</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <?php foreach ($summaryItems as $item) : ?>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="summary-card p-3">
                    <div class="d-flex align-items-center justify-content-between gap-2">
                        <div>
                            <div class="text-secondary small fw-semibold"><?= esc($item['label']) ?></div>
                            <div class="h4 fw-bold mb-0"><?= esc((int) $item['value']) ?></div>
                        </div>
                        <div class="icon-box <?= $item['class'] === 'success' ? 'success' : ($item['class'] === 'warning' ? 'warning' : '') ?>">
                            <i class="bi <?= esc($item['icon']) ?>"></i>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (($summary['konflik'] ?? 0) > 0) : ?>
        <div class="alert alert-danger d-flex align-items-start gap-2">
            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
            <div>
                <div class="fw-semibold">Ada <?= esc((int) $summary['konflik']) ?> konflik jadwal.</div>
                <div>Kendaraan maintenance/nonaktif masih memiliki jadwal pada tanggal monitor. Periksa kartu berlabel konflik.</div>
            </div>
        </div>
    <?php endif; ?>

    <div class="panel p-4">
        <div class="row g-3">
            <?php if (empty($kendaraan)) : ?>
                <div class="col-12">
                    <div class="text-center text-secondary py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Belum ada data kendaraan.
                    </div>
                </div>
            <?php endif; ?>

            <?php foreach ($kendaraan as $row) : ?>
                <?php $status = $row['status_monitor']; ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="border rounded bg-white h-100 overflow-hidden">
                        <div class="d-flex gap-3 p-3 border-bottom">
                            <div style="width: 108px; flex: 0 0 108px;">
                                <?php if (! empty($row['foto_utama'])) : ?>
                                    <img src="<?= base_url('uploads/kendaraan/' . $row['foto_utama']) ?>" alt="Foto <?= esc($row['nama_kendaraan']) ?>" class="w-100 rounded" style="aspect-ratio: 4 / 3; object-fit: cover;">
                                <?php else : ?>
                                    <div class="photo-placeholder w-100" style="aspect-ratio: 4 / 3;"><i class="bi bi-car-front-fill"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="min-w-0 flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="fw-semibold text-truncate"><?= esc($row['nama_kendaraan']) ?></div>
                                    
                                </div>
                                <div class="small text-secondary mt-1"><?= esc(($row['jenis_kendaraan'] ?? 'Mobil') . ' - ' . $row['plat_nomor']) ?></div>
                                <div class="small text-secondary"><?= esc($row['merk']) ?>, <?= esc($row['tahun']) ?>, <?= esc($row['warna']) ?></div>
                                <span class="badge text-bg-<?= esc($status['class']) ?> text-nowrap">
                                        <i class="bi <?= esc($status['icon'] ?? 'bi-info-circle') ?> me-1"></i><?= esc($status['label']) ?>
                                    </span>
                                <div class="small fw-semibold mt-2">Rp <?= number_format((int) $row['harga_sewa_per_hari'], 0, ',', '.') ?>/hari</div>
                            </div>
                        </div>

                        <div class="p-3">
                            <div class="small text-secondary mb-2"><?= esc($status['description']) ?></div>

                            <?php if (! empty($status['meta'])) : ?>
                                <?php $meta = $status['meta']; ?>
                                <div class="d-grid gap-2 small">
                                    <div class="d-flex justify-content-between gap-3">
                                        <span class="text-secondary">Invoice</span>
                                        <span class="fw-semibold text-end"><?= esc($meta['kode_transaksi']) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between gap-3">
                                        <span class="text-secondary">Pelanggan</span>
                                        <span class="fw-semibold text-end"><?= esc($meta['nama_lengkap']) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between gap-3">
                                        <span class="text-secondary">No. HP</span>
                                        <span class="text-end"><?= esc($meta['no_hp'] ?: '-') ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between gap-3">
                                        <span class="text-secondary">Periode</span>
                                        <span class="text-end">
                                            <?= esc(date('d/m/Y', strtotime($meta['tanggal_sewa']))) ?> -
                                            <?= esc(date('d/m/Y', strtotime($meta['tanggal_kembali']))) ?>
                                        </span>
                                    </div>
                                    <?php if (! empty($meta['tanggal_dikembalikan'])) : ?>
                                        <div class="d-flex justify-content-between gap-3">
                                            <span class="text-secondary">Dikembalikan</span>
                                            <span class="text-end"><?= esc(date('d/m/Y', strtotime($meta['tanggal_dikembalikan']))) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ((int) $meta['hari_telat'] > 0) : ?>
                                        <div class="alert alert-danger py-2 px-3 mb-0">
                                            <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                            Telat <?= esc((int) $meta['hari_telat']) ?> hari dari tanggal kembali.
                                        </div>
                                    <?php elseif ($meta['hari_menuju_kembali'] !== null && $meta['status_transaksi'] === 'berjalan') : ?>
                                        <div class="alert alert-primary py-2 px-3 mb-0">
                                            <i class="bi bi-clock me-1"></i>
                                            Sisa <?= esc((int) $meta['hari_menuju_kembali']) ?> hari menuju tanggal kembali.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else : ?>
                                <div class="small text-secondary">
                                    Tidak ada pelanggan atau invoice yang mengunci kendaraan pada tanggal ini.
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between gap-2 px-3 py-2 bg-light border-top">
                            <a href="<?= site_url('admin/kendaraan/detail/' . $row['id']) ?>" class="btn btn-sm btn-outline-dark" target="_blank">
                                <i class="bi bi-eye"></i>Detail
                            </a>
                            <?php if (! empty($row['jadwal']['id'])) : ?>
                                <a href="<?= site_url('admin/transaksi-sewa/detail/' . $row['jadwal']['id']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                    <i class="bi bi-receipt"></i>Transaksi
                                </a>
                            <?php else : ?>
                                <a href="<?= site_url('admin/transaksi-sewa/create') ?>" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-plus-lg"></i>Sewa
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
