<?= view('admin/layouts/header', [
    'title'     => 'Laporan Transaksi Sewa - Aplikasi',
    'extraHead' => '<style>
        .report-filter-title { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
        .report-filter-title .icon-box { width: 38px; height: 38px; font-size: 18px; }
        .report-summary-card { height: 100%; padding: 16px; }
        .report-summary-card .summary-icon { width: 42px; height: 42px; display: grid; place-items: center; border-radius: 8px; font-size: 19px; }
        .summary-icon.primary { background: #eff6ff; color: #2563eb; }
        .summary-icon.success { background: #ecfdf3; color: #16a34a; }
        .summary-icon.warning { background: #fffbeb; color: #f59e0b; }
        .summary-icon.danger { background: #fef2f2; color: #dc2626; }
        .summary-icon.dark { background: #f2f4f7; color: #101828; }
        .report-table thead th { white-space: nowrap; }
        .report-money { white-space: nowrap; font-weight: 600; }
    </style>',
]) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'laporan-transaksi',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Laporan Transaksi Sewa',
    'pageSubtitle' => 'Ringkasan transaksi, pembayaran, sisa tagihan, dan denda.',
    'topbarAction' => '<a href="' . site_url('admin/transaksi-sewa/laporan/pdf?' . http_build_query($filters)) . '" class="btn btn-dark" target="_blank"><i class="bi bi-file-earmark-pdf me-1"></i>Export PDF</a>',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<section class="content-area">
    <div class="panel p-4 mb-4">
        <div class="report-filter-title">
            <div class="icon-box">
                <i class="bi bi-funnel-fill"></i>
            </div>
            <div>
                <div class="fw-semibold">Filter Laporan</div>
                <div class="small text-secondary">Saring transaksi berdasarkan tanggal, pelanggan, kendaraan, dan status.</div>
            </div>
        </div>

        <form method="get" action="<?= site_url('admin/transaksi-sewa/laporan') ?>" class="row g-3 align-items-end">
            <div class="col-12 col-md-3 col-xl-2">
                <label for="tanggal_mulai" class="form-label fw-semibold">Tanggal Mulai</label>
                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="<?= esc($filters['tanggal_mulai']) ?>">
            </div>
            <div class="col-12 col-md-3 col-xl-2">
                <label for="tanggal_selesai" class="form-label fw-semibold">Tanggal Selesai</label>
                <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="<?= esc($filters['tanggal_selesai']) ?>">
            </div>
            <div class="col-12 col-md-6 col-xl-2">
                <label for="pelanggan_id" class="form-label fw-semibold">Pelanggan</label>
                <select class="form-select" id="pelanggan_id" name="pelanggan_id">
                    <option value="">Semua Pelanggan</option>
                    <?php foreach ($pelangganOptions as $pelanggan) : ?>
                        <option value="<?= esc($pelanggan['id']) ?>" <?= $filters['pelanggan_id'] === (string) $pelanggan['id'] ? 'selected' : '' ?>><?= esc($pelanggan['nama_lengkap']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-6 col-xl-2">
                <label for="kendaraan_id" class="form-label fw-semibold">Kendaraan</label>
                <select class="form-select" id="kendaraan_id" name="kendaraan_id">
                    <option value="">Semua Kendaraan</option>
                    <?php foreach ($kendaraanOptions as $kendaraan) : ?>
                        <option value="<?= esc($kendaraan['id']) ?>" <?= $filters['kendaraan_id'] === (string) $kendaraan['id'] ? 'selected' : '' ?>><?= esc($kendaraan['nama_kendaraan'] . ' - ' . $kendaraan['plat_nomor']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3 col-xl-2">
                <label for="status_transaksi" class="form-label fw-semibold">Status Transaksi</label>
                <select class="form-select" id="status_transaksi" name="status_transaksi">
                    <option value="">Semua Status</option>
                    <?php foreach ($statusTransaksiOptions as $value => $label) : ?>
                        <option value="<?= esc($value) ?>" <?= $filters['status_transaksi'] === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3 col-xl-2">
                <label for="status_pembayaran" class="form-label fw-semibold">Status Bayar</label>
                <select class="form-select" id="status_pembayaran" name="status_pembayaran">
                    <option value="">Semua Status</option>
                    <?php foreach ($statusPembayaranOptions as $value => $label) : ?>
                        <option value="<?= esc($value) ?>" <?= $filters['status_pembayaran'] === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <a href="<?= site_url('admin/transaksi-sewa/laporan') ?>" class="btn btn-light">Reset</a>
                <button type="submit" class="btn btn-dark"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl">
            <div class="panel report-summary-card">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <div class="small text-secondary">Jumlah Transaksi</div>
                        <div class="fs-5 fw-bold"><?= number_format((int) $summary['jumlah_transaksi'], 0, ',', '.') ?></div>
                    </div>
                    <div class="summary-icon dark"><i class="bi bi-receipt-cutoff"></i></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl">
            <div class="panel report-summary-card">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <div class="small text-secondary">Total Tagihan</div>
                        <div class="fs-5 fw-bold">Rp <?= number_format((int) $summary['total_tagihan'], 0, ',', '.') ?></div>
                    </div>
                    <div class="summary-icon primary"><i class="bi bi-file-earmark-text-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl">
            <div class="panel report-summary-card">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <div class="small text-secondary">Total Bayar</div>
                        <div class="fs-5 fw-bold">Rp <?= number_format((int) $summary['total_bayar'], 0, ',', '.') ?></div>
                    </div>
                    <div class="summary-icon success"><i class="bi bi-cash-coin"></i></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl">
            <div class="panel report-summary-card">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <div class="small text-secondary">Sisa Tagihan</div>
                        <div class="fs-5 fw-bold">Rp <?= number_format((int) $summary['sisa_tagihan'], 0, ',', '.') ?></div>
                    </div>
                    <div class="summary-icon warning"><i class="bi bi-hourglass-split"></i></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl">
            <div class="panel report-summary-card">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <div class="small text-secondary">Total Denda</div>
                        <div class="fs-5 fw-bold">Rp <?= number_format((int) $summary['total_denda'], 0, ',', '.') ?></div>
                    </div>
                    <div class="summary-icon danger"><i class="bi bi-exclamation-triangle-fill"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <div class="fw-semibold">Data Transaksi</div>
                <div class="small text-secondary">Daftar transaksi sesuai filter yang dipilih.</div>
            </div>
            <span class="badge text-bg-light"><?= number_format((int) $summary['jumlah_transaksi'], 0, ',', '.') ?> transaksi</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle datatable datatable-report report-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Kendaraan</th>
                        <th>Periode</th>
                        <th>Total</th>
                        <th>Bayar</th>
                        <th>Sisa</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaksi as $row) : ?>
                        <tr>
                            <td>
                                <div class="fw-semibold"><?= esc($row['kode_transaksi']) ?></div>
                                <a href="<?= site_url('admin/transaksi-sewa/detail/' . $row['id']) ?>" class="small text-decoration-none" target="_blank">Detail</a>
                            </td>
                            <td><?= esc(date('d/m/Y', strtotime($row['tanggal_transaksi']))) ?></td>
                            <td>
                                <div class="fw-semibold"><?= esc($row['nama_lengkap']) ?></div>
                                <div class="small text-secondary"><?= esc($row['kode_pelanggan']) ?></div>
                            </td>
                            <td>
                                <div><?= esc($row['nama_kendaraan']) ?></div>
                                <div class="small text-secondary"><?= esc($row['plat_nomor']) ?></div>
                            </td>
                            <td>
                                <?= esc(date('d/m/Y', strtotime($row['tanggal_sewa']))) ?> -
                                <?= esc(date('d/m/Y', strtotime($row['tanggal_kembali']))) ?>
                                <div class="small text-secondary"><?= esc((int) $row['lama_sewa']) ?> hari</div>
                            </td>
                            <td class="report-money">Rp <?= number_format((int) $row['total_tagihan'], 0, ',', '.') ?></td>
                            <td class="report-money text-success">Rp <?= number_format((int) $row['total_bayar'], 0, ',', '.') ?></td>
                            <td class="report-money <?= (int) $row['sisa_tagihan'] > 0 ? 'text-danger' : 'text-success' ?>">Rp <?= number_format((int) $row['sisa_tagihan'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($row['status_transaksi'] === 'selesai') : ?>
                                    <span class="badge text-bg-success">Selesai</span>
                                <?php elseif ($row['status_transaksi'] === 'berjalan') : ?>
                                    <span class="badge text-bg-primary">Berjalan</span>
                                <?php elseif ($row['status_transaksi'] === 'booking') : ?>
                                    <span class="badge text-bg-warning">Booking</span>
                                <?php else : ?>
                                    <span class="badge text-bg-secondary">Batal</span>
                                <?php endif; ?>

                                <?php if ($row['status_pembayaran'] === 'lunas') : ?>
                                    <span class="badge text-bg-success mt-1">Lunas</span>
                                <?php elseif ($row['status_pembayaran'] === 'belum_lunas') : ?>
                                    <span class="badge text-bg-info mt-1">Belum Lunas</span>
                                <?php else : ?>
                                    <span class="badge text-bg-secondary mt-1">Belum Bayar</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
