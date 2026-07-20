<?= view('admin/layouts/header', ['title' => 'Transaksi Sewa - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'transaksi-sewa',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Transaksi Sewa',
    'pageSubtitle' => 'Kelola invoice sewa kendaraan dan status pembayarannya.',
    'topbarAction' => '<a href="' . site_url('admin/transaksi-sewa/create') . '" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i>Tambah Transaksi</a>',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<section class="content-area">
    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?php
        $summary = $summary ?? ['jumlah' => count($transaksi), 'total_tagihan' => 0, 'total_bayar' => 0, 'sisa_tagihan' => 0, 'telat' => 0];
        $filters = $filters ?? [];
    ?>

    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="summary-card p-3">
                <div class="text-secondary small fw-semibold">Transaksi</div>
                <div class="h4 fw-bold mb-0"><?= esc((int) $summary['jumlah']) ?></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="summary-card p-3">
                <div class="text-secondary small fw-semibold">Total Tagihan</div>
                <div class="h5 fw-bold mb-0">Rp <?= number_format((int) $summary['total_tagihan'], 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="summary-card p-3">
                <div class="text-secondary small fw-semibold">Terbayar</div>
                <div class="h5 fw-bold mb-0 text-success">Rp <?= number_format((int) $summary['total_bayar'], 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="summary-card p-3">
                <div class="d-flex justify-content-between gap-2">
                    <div>
                        <div class="text-secondary small fw-semibold">Sisa Tagihan</div>
                        <div class="h5 fw-bold mb-0 text-danger">Rp <?= number_format((int) $summary['sisa_tagihan'], 0, ',', '.') ?></div>
                    </div>
                    <?php if ((int) ($summary['telat'] ?? 0) > 0) : ?>
                        <span class="badge text-bg-danger align-self-start"><?= esc((int) $summary['telat']) ?> telat</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="panel p-4 mb-4">
        <form method="get" action="<?= site_url('admin/transaksi-sewa') ?>" class="row g-3 align-items-end">
            <div class="col-12 col-xl-4">
                <label for="q" class="form-label fw-semibold">Cari</label>
                <input type="text" class="form-control" id="q" name="q" value="<?= esc($filters['q'] ?? '') ?>" placeholder="Invoice, pelanggan, HP, plat, kendaraan">
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <label for="tanggal_mulai" class="form-label fw-semibold">Transaksi Dari</label>
                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="<?= esc($filters['tanggal_mulai'] ?? '') ?>">
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <label for="tanggal_selesai" class="form-label fw-semibold">Transaksi Sampai</label>
                <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="<?= esc($filters['tanggal_selesai'] ?? '') ?>">
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <label for="periode_mulai" class="form-label fw-semibold">Sewa Dari</label>
                <input type="date" class="form-control" id="periode_mulai" name="periode_mulai" value="<?= esc($filters['periode_mulai'] ?? '') ?>">
            </div>
            <div class="col-6 col-md-3 col-xl-2">
                <label for="periode_selesai" class="form-label fw-semibold">Sewa Sampai</label>
                <input type="date" class="form-control" id="periode_selesai" name="periode_selesai" value="<?= esc($filters['periode_selesai'] ?? '') ?>">
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <label for="pelanggan_id" class="form-label fw-semibold">Pelanggan</label>
                <select class="form-select" id="pelanggan_id" name="pelanggan_id">
                    <option value="">Semua Pelanggan</option>
                    <?php foreach ($pelangganOptions as $pelanggan) : ?>
                        <option value="<?= esc($pelanggan['id']) ?>" <?= ($filters['pelanggan_id'] ?? '') === (string) $pelanggan['id'] ? 'selected' : '' ?>>
                            <?= esc($pelanggan['nama_lengkap'] . ' - ' . $pelanggan['kode_pelanggan']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <label for="kendaraan_id" class="form-label fw-semibold">Kendaraan</label>
                <select class="form-select" id="kendaraan_id" name="kendaraan_id">
                    <option value="">Semua Kendaraan</option>
                    <?php foreach ($kendaraanOptions as $kendaraan) : ?>
                        <option value="<?= esc($kendaraan['id']) ?>" <?= ($filters['kendaraan_id'] ?? '') === (string) $kendaraan['id'] ? 'selected' : '' ?>>
                            <?= esc(($kendaraan['jenis_kendaraan'] ?? 'Mobil') . ' - ' . $kendaraan['plat_nomor'] . ' - ' . $kendaraan['nama_kendaraan']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-4 col-xl-2">
                <label for="status_transaksi" class="form-label fw-semibold">Status Transaksi</label>
                <select class="form-select" id="status_transaksi" name="status_transaksi">
                    <option value="">Semua Status</option>
                    <?php foreach ($statusTransaksiOptions as $value => $label) : ?>
                        <option value="<?= esc($value) ?>" <?= ($filters['status_transaksi'] ?? '') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-4 col-xl-2">
                <label for="status_pembayaran" class="form-label fw-semibold">Status Pembayaran</label>
                <select class="form-select" id="status_pembayaran" name="status_pembayaran">
                    <option value="">Semua Pembayaran</option>
                    <?php foreach ($statusPembayaranOptions as $value => $label) : ?>
                        <option value="<?= esc($value) ?>" <?= ($filters['status_pembayaran'] ?? '') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-4 col-xl-2">
                <label for="kondisi" class="form-label fw-semibold">Kondisi</label>
                <select class="form-select" id="kondisi" name="kondisi">
                    <option value="">Semua Kondisi</option>
                    <?php foreach ($kondisiOptions as $value => $label) : ?>
                        <option value="<?= esc($value) ?>" <?= ($filters['kondisi'] ?? '') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-xl-auto d-flex gap-2">
                <button type="submit" class="btn btn-dark"><i class="bi bi-funnel"></i>Filter</button>
                <a href="<?= site_url('admin/transaksi-sewa') ?>" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>

    <div class="panel p-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-2 mb-3">
            <div>
                <div class="fw-bold">Daftar Transaksi Sewa</div>
                <div class="small text-secondary">Menampilkan <?= esc((int) $summary['jumlah']) ?> transaksi sesuai filter.</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Pelanggan</th>
                        <th>Kendaraan</th>
                        <th>Periode</th>
                        <th>Total</th>
                        <th>Terbayar</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaksi as $row) : ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($row['kode_transaksi']) ?></td>
                            <td>
                                <div class="fw-semibold"><?= esc($row['nama_lengkap']) ?></div>
                                <div class="small text-secondary"><?= esc($row['kode_pelanggan']) ?></div>
                            </td>
                            <td>
                                <div><?= esc($row['nama_kendaraan']) ?></div>
                                <div class="small text-secondary"><?= esc(($row['jenis_kendaraan'] ?? 'Mobil') . ' - ' . $row['plat_nomor']) ?></div>
                            </td>
                            <td>
                                Mulai : <span class="badge bg-primary"><?= esc(date('d/m/Y', strtotime($row['tanggal_sewa']))) ?></span><br>  
                                Sampai : <span class="badge bg-danger"><?= esc(date('d/m/Y', strtotime($row['tanggal_kembali']))) ?></span>
                                <div class="small text-secondary"><?= esc($row['lama_sewa']) ?> hari</div>
                                <?php if ($row['status_transaksi'] === 'selesai' && ! empty($row['tanggal_dikembalikan'])) : ?>
                                    <div class="small text-success">
                                        Dikembalikan: <?= esc(date('d/m/Y', strtotime($row['tanggal_dikembalikan']))) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                 <?= number_format((int) $row['total_tagihan'], 0, ',', '.') ?>
                                <?php if ((int) ($row['denda'] ?? 0) > 0) : ?>
                                    <div class="small text-danger">
                                        Denda <?= esc((int) ($row['hari_terlambat'] ?? 0)) ?> hari:
                                         <?= number_format((int) $row['denda'], 0, ',', '.') ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                 <?= number_format((int) $row['total_bayar'], 0, ',', '.') ?>
                                <div class="small text-secondary">Sisa  <?= number_format((int) $row['sisa_tagihan'], 0, ',', '.') ?></div>
                            </td>
                            <td class="text-center">
                                <?php if ($row['status_pembayaran'] === 'lunas') : ?>
                                    <span class="badge text-bg-success">Lunas</span>
                                <?php elseif ($row['status_pembayaran'] === 'belum_lunas') : ?>
                                    <span class="badge text-bg-primary">Belum Lunas</span>
                                <?php else : ?>
                                    <span class="badge text-bg-secondary">Belum Bayar</span>
                                <?php endif; ?>
                                <br><br>
                                <div class="mt-1">
                                    <span class="badge <?php if($row['status_transaksi'] === 'selesai') echo 'bg-success'; else echo 'bg-warning text-dark'; ?>"><?= esc(ucfirst($row['status_transaksi'])) ?></span>
                                    <?php if ((int) ($row['hari_telat_berjalan'] ?? 0) > 0) : ?>
                                        <span class="badge text-bg-danger">Telat <?= esc((int) $row['hari_telat_berjalan']) ?> hari</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                <div class="d-grid gap-1 text-start">

                                    <a href="<?= site_url('admin/transaksi-sewa/invoice/' . $row['id']) ?>"
                                        class="btn btn-sm btn-dark"
                                        target="_blank">
                                        <i class="bi bi-file-earmark-pdf"></i> Invoice
                                    </a>

                                    <a href="<?= site_url('admin/transaksi-sewa/surat-jalan/' . $row['id']) ?>"
                                        class="btn btn-sm btn-secondary"
                                        target="_blank">
                                        <i class="bi bi-truck"></i> Surat Jalan
                                    </a>

                                    <a href="<?= site_url('admin/transaksi-sewa/pembayaran/' . $row['id']) ?>"
                                        class="btn btn-sm btn-success">
                                        <i class="bi bi-cash-coin"></i> Pembayaran
                                    </a>

                                    <a href="<?= site_url('admin/transaksi-sewa/detail/' . $row['id']) ?>"
                                        class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>

                                    <a href="<?= site_url('admin/transaksi-sewa/edit/' . $row['id']) ?>"
                                        class="btn btn-sm btn-primary">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>

                                    <form action="<?= site_url('admin/transaksi-sewa/delete/' . $row['id']) ?>"
                                        method="post"
                                        onsubmit="return confirm('Hapus transaksi sewa ini?')">

                                        <?= csrf_field() ?>

                                        <button type="submit" class="btn btn-sm btn-danger w-100">
                                            <i class="bi bi-trash3"></i> Hapus
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
