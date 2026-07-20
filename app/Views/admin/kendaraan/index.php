<?= view('admin/layouts/header', ['title' => 'Data Kendaraan - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'kendaraan',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Data Kendaraan',
    'pageSubtitle' => 'Kelola armada kendaraan rental.',
    'topbarAction' => '<a href="' . site_url('admin/kendaraan/create') . '" class="btn btn-dark"><i class="bi bi-plus-lg me-1"></i>Tambah Kendaraan</a>',
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

    <div class="panel p-4">
        <form method="get" action="<?= site_url('admin/kendaraan') ?>" class="row g-3 mb-4">
            <div class="col-12 col-md-6 col-xl">
                <label for="filter_jenis" class="form-label fw-semibold">Jenis</label>
                <select class="form-select" id="filter_jenis" name="jenis_kendaraan">
                    <option value="">Semua Jenis</option>
                    <?php foreach ($jenisKendaraanOptions as $jenis) : ?>
                        <option value="<?= esc($jenis) ?>" <?= ($filters['jenis_kendaraan'] ?? '') === $jenis ? 'selected' : '' ?>><?= esc($jenis) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-6 col-xl">
                <label for="filter_merk" class="form-label fw-semibold">Merk</label>
                <select class="form-select" id="filter_merk" name="merk">
                    <option value="">Semua Merk</option>
                    <?php foreach ($merkOptions as $merk) : ?>
                        <option value="<?= esc($merk) ?>" <?= ($filters['merk'] ?? '') === $merk ? 'selected' : '' ?>><?= esc($merk) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-6 col-xl">
                <label for="filter_tahun" class="form-label fw-semibold">Tahun</label>
                <input type="number" class="form-control" id="filter_tahun" name="tahun" value="<?= esc($filters['tahun'] ?? '') ?>" placeholder="Semua">
            </div>
            <div class="col-12 col-md-6 col-xl">
                <label for="filter_warna" class="form-label fw-semibold">Warna</label>
                <select class="form-select" id="filter_warna" name="warna">
                    <option value="">Semua Warna</option>
                    <?php foreach ($warnaOptions as $warna) : ?>
                        <option value="<?= esc($warna) ?>" <?= ($filters['warna'] ?? '') === $warna ? 'selected' : '' ?>><?= esc($warna) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-6 col-xl">
                <label for="filter_status" class="form-label fw-semibold">Status</label>
                <select class="form-select" id="filter_status" name="status">
                    <option value="">Semua Status</option>
                    <?php foreach ($statusOptions as $value => $label) : ?>
                        <option value="<?= esc($value) ?>" <?= ($filters['status'] ?? '') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-6 col-xl-auto d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-dark"><i class="bi bi-funnel me-1"></i>Filter</button>
                <a href="<?= site_url('admin/kendaraan') ?>" class="btn btn-light">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle datatable">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Kode</th>
                        <th>Jenis</th>
                        <th>Plat Nomor</th>
                        <th>Merk</th>
                        <th>Nama Kendaraan</th>
                        <th>Tahun</th>
                        <th>Harga/Hari</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kendaraan as $row) : ?>
                        <tr>
                            <td>
                                <?php if (! empty($row['foto_utama'])) : ?>
                                    <img src="<?= base_url('uploads/kendaraan/' . $row['foto_utama']) ?>" alt="Foto <?= esc($row['nama_kendaraan']) ?>" class="user-photo">
                                <?php else : ?>
                                    <div class="photo-placeholder"><i class="bi bi-car-front-fill"></i></div>
                                <?php endif; ?>
                            </td>
                            <td class="fw-semibold"><?= esc($row['kode_kendaraan']) ?></td>
                            <td><?= esc($row['jenis_kendaraan'] ?? 'Mobil') ?></td>
                            <td><?= esc($row['plat_nomor']) ?></td>
                            <td><?= esc($row['merk']) ?></td>
                            <td><?= esc($row['nama_kendaraan']) ?></td>
                            <td><?= esc($row['tahun']) ?></td>
                            <td>Rp <?= number_format((int) $row['harga_sewa_per_hari'], 0, ',', '.') ?></td>
                            <td>
                                <?php if ($row['status'] === 'ready') : ?>
                                    <span class="badge text-bg-success">Ready</span>
                                <?php elseif ($row['status'] === 'maintenance') : ?>
                                    <span class="badge text-bg-warning">Maintenance</span>
                                <?php else : ?>
                                    <span class="badge text-bg-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="<?= site_url('admin/kendaraan/detail/' . $row['id']) ?>" class="btn btn-sm btn-info btn-icon" title="Detail kendaraan" aria-label="Detail kendaraan" target="_blank">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?= site_url('admin/kendaraan/edit/' . $row['id']) ?>" class="btn btn-sm btn-primary btn-icon" title="Edit kendaraan" aria-label="Edit kendaraan">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <?php if (! empty($row['has_transaksi'])) : ?>
                                    <button type="button" class="btn btn-sm btn-secondary btn-icon" title="Tidak bisa dihapus karena sudah memiliki transaksi. Ubah status menjadi Nonaktif bila tidak dipakai lagi." aria-label="Kendaraan terkunci" disabled>
                                        <i class="bi bi-lock-fill"></i>
                                    </button>
                                <?php else : ?>
                                    <form action="<?= site_url('admin/kendaraan/delete/' . $row['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus data kendaraan ini?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-danger btn-icon" title="Hapus kendaraan" aria-label="Hapus kendaraan">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
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
