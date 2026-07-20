<?= view('admin/layouts/header', ['title' => 'Detail Pelanggan - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'pelanggan',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Detail Pelanggan',
    'pageSubtitle' => $pelanggan['kode_pelanggan'] . ' - ' . $pelanggan['nama_lengkap'],
    'topbarAction' => '<a href="' . site_url('admin/pelanggan') . '" class="btn btn-outline-dark"><i class="bi bi-arrow-left me-1"></i>Kembali</a>',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<section class="content-area">
    <div class="row g-4">
        <div class="col-12 col-xl-7">
            <div class="panel p-4 h-100">
                <div class="d-flex align-items-start justify-content-between gap-3 mb-4">
                    <div>
                        <div class="text-secondary small">Kode Pelanggan</div>
                        <div class="h4 fw-bold mb-0"><?= esc($pelanggan['kode_pelanggan']) ?></div>
                    </div>
                    <?php if ($pelanggan['status'] === 'aktif') : ?>
                        <span class="badge text-bg-success">Aktif</span>
                    <?php else : ?>
                        <span class="badge text-bg-secondary">Nonaktif</span>
                    <?php endif; ?>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <tbody>
                            <tr>
                                <th class="text-secondary" style="width: 210px;">Nama Lengkap</th>
                                <td class="fw-semibold"><?= esc($pelanggan['nama_lengkap']) ?></td>
                            </tr>
                            <tr>
                                <th class="text-secondary">NIK</th>
                                <td><?= esc($pelanggan['nik']) ?></td>
                            </tr>
                            <tr>
                                <th class="text-secondary">No. HP</th>
                                <td><?= esc($pelanggan['no_hp']) ?></td>
                            </tr>
                            <tr>
                                <th class="text-secondary">Tanggal Terdaftar</th>
                                <td><?= esc(date('d/m/Y', strtotime($pelanggan['tanggal_terdaftar']))) ?></td>
                            </tr>
                            <tr>
                                <th class="text-secondary">Alamat</th>
                                <td><?= nl2br(esc($pelanggan['alamat'])) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5">
            <div class="panel p-4 h-100">
                <div class="fw-semibold mb-3">Dokumen & Foto</div>
                <div class="row g-3">
                    <?php foreach (['foto' => 'Foto', 'foto_ktp' => 'Foto KTP', 'foto_sim' => 'Foto SIM'] as $field => $label) : ?>
                        <div class="col-12">
                            <div class="border rounded p-3 bg-white">
                                <div class="fw-semibold mb-2"><?= esc($label) ?></div>
                                <?php if (! empty($pelanggan[$field])) : ?>
                                    <a href="<?= base_url('uploads/pelanggan/' . $pelanggan[$field]) ?>" target="_blank" rel="noopener">
                                        <img src="<?= base_url('uploads/pelanggan/' . $pelanggan[$field]) ?>" alt="<?= esc($label . ' ' . $pelanggan['nama_lengkap']) ?>" class="w-100 rounded" style="max-height: 260px; object-fit: contain; background: #f8fafc;">
                                    </a>
                                <?php else : ?>
                                    <div class="text-secondary small">Belum ada file.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
