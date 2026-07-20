<?= view('admin/layouts/header', ['title' => 'Laporan Keuangan - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'laporan-keuangan',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Laporan Keuangan',
    'pageSubtitle' => 'Pantau arus kas pemasukan, pengeluaran, dan saldo usaha rental.',
    'topbarAction' => '<a href="' . site_url('admin/transaksi-keuangan/laporan/pdf') . '?' . http_build_query($filters) . '" class="btn btn-dark" target="_blank"><i class="bi bi-file-earmark-pdf me-1"></i>Export PDF</a>',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<section class="content-area">
    <?php
        $tanggalMulai = $filters['tanggal_mulai'] !== '' ? date('d/m/Y', strtotime($filters['tanggal_mulai'])) : '-';
        $tanggalSelesai = $filters['tanggal_selesai'] !== '' ? date('d/m/Y', strtotime($filters['tanggal_selesai'])) : '-';
        $saldoClass = (int) $summary['saldo'] >= 0 ? 'text-success' : 'text-danger';
    ?>

    <div class="panel p-4 mb-4">
        <form method="get" action="<?= site_url('admin/transaksi-keuangan/laporan') ?>" class="row g-3 align-items-end">
            <div class="col-6 col-md-3 col-xl-2">
                <label for="tanggal_mulai" class="form-label fw-semibold">Tanggal Mulai</label>
                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="<?= esc($filters['tanggal_mulai']) ?>">
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <label for="tanggal_selesai" class="form-label fw-semibold">Tanggal Selesai</label>
                <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="<?= esc($filters['tanggal_selesai']) ?>">
            </div>
            <div class="col-12 col-md-3 col-xl-2">
                <label for="jenis" class="form-label fw-semibold">Jenis</label>
                <select class="form-select" id="jenis" name="jenis">
                    <option value="">Semua Jenis</option>
                    <?php foreach ($jenisOptions as $jenis) : ?>
                        <option value="<?= esc($jenis) ?>" <?= $filters['jenis'] === $jenis ? 'selected' : '' ?>><?= esc($jenis) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3 col-xl-4">
                <label for="kategori_keuangan_id" class="form-label fw-semibold">Kategori</label>
                <select class="form-select" id="kategori_keuangan_id" name="kategori_keuangan_id">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategoriOptions as $kategori) : ?>
                        <option value="<?= esc($kategori['id']) ?>" <?= $filters['kategori_keuangan_id'] === (string) $kategori['id'] ? 'selected' : '' ?>>
                            <?= esc($kategori['kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>            
            <div class="col-12 col-md-2 col-xl-2 d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-dark"><i class="bi bi-funnel"></i>Filter</button>
                <a href="<?= site_url('admin/transaksi-keuangan/laporan') ?>" class="btn btn-light">Reset</a>                
            </div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="summary-card p-3">
                <div class="small text-secondary fw-semibold">Periode</div>
                <div class="h6 fw-bold mb-0"><?= esc($tanggalMulai) ?> - <?= esc($tanggalSelesai) ?></div>
                <div class="small text-secondary mt-1"><?= esc((int) $summary['jumlah_transaksi']) ?> transaksi</div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="summary-card p-3">
                <div class="small text-secondary fw-semibold">Total Pemasukan</div>
                <div class="h5 fw-bold text-success mb-0">Rp <?= number_format((int) $summary['pemasukan'], 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="summary-card p-3">
                <div class="small text-secondary fw-semibold">Total Pengeluaran</div>
                <div class="h5 fw-bold text-danger mb-0">Rp <?= number_format((int) $summary['pengeluaran'], 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="summary-card p-3">
                <div class="small text-secondary fw-semibold">Saldo Bersih</div>
                <div class="h5 fw-bold <?= esc($saldoClass) ?> mb-0">Rp <?= number_format((int) $summary['saldo'], 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-xl-4">
            <div class="panel p-4 h-100">
                <div class="fw-bold mb-1">Ringkasan per Kategori</div>
                <div class="small text-secondary mb-3">Kategori dengan total nominal terbesar pada periode ini.</div>

                <?php if (empty($kategoriSummary)) : ?>
                    <div class="text-center text-secondary py-4">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Belum ada transaksi pada filter ini.
                    </div>
                <?php endif; ?>

                <div class="d-grid gap-2">
                    <?php foreach ($kategoriSummary as $row) : ?>
                        <div class="border rounded p-3">
                            <div class="d-flex justify-content-between gap-2">
                                <div class="min-w-0">
                                    <div class="fw-semibold text-truncate"><?= esc($row['kategori']) ?></div>
                                    <div class="small text-secondary"><?= esc($row['jenis']) ?>, <?= esc((int) $row['jumlah']) ?> transaksi</div>
                                </div>
                                <div class="text-end fw-bold <?= $row['jenis'] === 'Pemasukan' ? 'text-success' : 'text-danger' ?>">
                                    Rp <?= number_format((int) $row['total'], 0, ',', '.') ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8">
            <div class="panel p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                    <div>
                        <div class="fw-bold">Detail Transaksi Keuangan</div>
                        <div class="small text-secondary">Data berikut sudah mengikuti filter laporan.</div>
                    </div>
                    <a href="<?= site_url('admin/transaksi-keuangan/laporan/pdf') . '?' . http_build_query($filters) ?>" class="btn btn-outline-dark" target="_blank">
                        <i class="bi bi-file-earmark-pdf"></i>Export PDF
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle datatable datatable-report">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Kategori</th>
                                <th>Catatan</th>
                                <th class="text-end">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transaksi as $row) : ?>
                                <tr>
                                    <td><?= esc(date('d/m/Y', strtotime($row['tanggal']))) ?></td>
                                    <td>
                                        <?php if ($row['jenis'] === 'Pemasukan') : ?>
                                            <span class="badge text-bg-success">Pemasukan</span>
                                        <?php else : ?>
                                            <span class="badge text-bg-danger">Pengeluaran</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-semibold"><?= esc($row['kategori']) ?></td>
                                    <td><?= esc($row['catatan'] ?: '-') ?></td>
                                    <td class="text-end fw-semibold <?= $row['jenis'] === 'Pemasukan' ? 'text-success' : 'text-danger' ?>">
                                        <?= $row['jenis'] === 'Pemasukan' ? '+' : '-' ?> Rp <?= number_format((int) $row['nominal'], 0, ',', '.') ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
