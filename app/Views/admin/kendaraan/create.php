<?= view('admin/layouts/header', [
    'title'     => 'Tambah Kendaraan - Aplikasi',
    'extraHead' => '<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet"><link href="https://unpkg.com/filepond-plugin-image-preview@^4/dist/filepond-plugin-image-preview.css" rel="stylesheet">',
]) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'kendaraan',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Tambah Kendaraan',
    'pageSubtitle' => 'Isi data kendaraan dan upload satu atau lebih foto kendaraan.',
    'topbarAction' => '<a href="' . site_url('admin/kendaraan') . '" class="btn btn-outline-dark"><i class="bi bi-arrow-left me-1"></i>Kembali</a>',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<section class="content-area">
    <?php $errors = session()->getFlashdata('errors'); ?>
    <?php if ($errors) : ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
                <div><?= esc($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="panel p-4">
        <form action="<?= site_url('admin/kendaraan/store') ?>" method="post" id="kendaraanForm">
            <?= csrf_field() ?>

            <div class="row g-4">
                <div class="col-12 col-lg-3">
                    <label for="kode_kendaraan" class="form-label fw-semibold">Kode Kendaraan</label>
                    <input type="text" class="form-control" id="kode_kendaraan" value="<?= esc($kodeKendaraan) ?>" readonly>
                    <div class="form-text">Kode dibuat otomatis saat data disimpan.</div>
                </div>

                <div class="col-12 col-lg-3">
                    <label for="jenis_kendaraan" class="form-label fw-semibold">Jenis Kendaraan</label>
                    <select class="form-select" id="jenis_kendaraan" name="jenis_kendaraan" required>
                        <?php foreach ($jenisKendaraanOptions as $jenisKendaraan) : ?>
                            <option value="<?= esc($jenisKendaraan) ?>" <?= old('jenis_kendaraan', 'Mobil') === $jenisKendaraan ? 'selected' : '' ?>><?= esc($jenisKendaraan) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-lg-3">
                    <label for="plat_nomor" class="form-label fw-semibold">Plat Nomor</label>
                    <input type="text" class="form-control text-uppercase" id="plat_nomor" name="plat_nomor" value="<?= old('plat_nomor') ?>" required>
                </div>

                <div class="col-12 col-lg-3">
                    <label for="merk" class="form-label fw-semibold">Merk</label>
                    <select class="form-select" id="merk" name="merk" required>
                        <option value="">Pilih merk</option>
                        <?php foreach ($merkOptions as $merk) : ?>
                            <option value="<?= esc($merk) ?>" <?= old('merk') === $merk ? 'selected' : '' ?>><?= esc($merk) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-lg-3">
                    <label for="tahun" class="form-label fw-semibold">Tahun</label>
                    <input type="number" class="form-control" id="tahun" name="tahun" value="<?= old('tahun', date('Y')) ?>" min="1980" max="<?= date('Y') + 1 ?>" required>
                </div>

                <div class="col-12 col-lg-4">
                    <label for="nama_kendaraan" class="form-label fw-semibold">Nama Kendaraan</label>
                    <input type="text" class="form-control" id="nama_kendaraan" name="nama_kendaraan" value="<?= old('nama_kendaraan') ?>" required>
                </div>

                <div class="col-12 col-lg-3">
                    <label for="warna" class="form-label fw-semibold">Warna</label>
                    <select class="form-select" id="warna" name="warna" required>
                        <option value="">Pilih warna</option>
                        <?php foreach ($warnaOptions as $warna) : ?>
                            <option value="<?= esc($warna) ?>" <?= old('warna') === $warna ? 'selected' : '' ?>><?= esc($warna) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-lg-3">
                    <label for="harga_sewa_per_hari" class="form-label fw-semibold">Harga Sewa / Hari</label>
                    <input type="text" class="form-control currency-input" id="harga_sewa_per_hari" name="harga_sewa_per_hari" value="<?= old('harga_sewa_per_hari') ?>" required>
                </div>

                <div class="col-12 col-lg-2">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <?php foreach ($statusOptions as $value => $label) : ?>
                            <option value="<?= esc($value) ?>" <?= old('status', 'ready') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label for="keterangan" class="form-label fw-semibold">Keterangan</label>
                    <textarea class="form-control" id="keterangan" name="keterangan" rows="4"><?= old('keterangan') ?></textarea>
                </div>
                
                <div class="col-12">
                    <label for="foto_kendaraan" class="form-label fw-semibold">Foto Kendaraan</label>
                    <input type="file" class="filepond" id="foto_kendaraan" name="foto_kendaraan[]" accept="image/png,image/jpeg,image/webp" multiple>
                    <div class="form-text">Bisa upload lebih dari 1 foto. Format jpg, jpeg, png, atau webp. Maksimal 2 MB per foto.</div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="<?= site_url('admin/kendaraan') ?>" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-dark" id="saveButton">Simpan Kendaraan</button>
            </div>
        </form>
    </div>
</section>

<?= view('admin/kendaraan/filepond_script') ?>
<?= view('admin/layouts/footer') ?>
