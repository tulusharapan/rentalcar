<?= view('admin/layouts/header', [
    'title'     => 'Edit Pelanggan - Aplikasi',
    'extraHead' => '<link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet"><link href="https://unpkg.com/filepond-plugin-image-preview@^4/dist/filepond-plugin-image-preview.css" rel="stylesheet">',
]) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'pelanggan',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Edit Pelanggan',
    'pageSubtitle' => 'Perbarui data dan dokumen identitas pelanggan.',
    'topbarAction' => '<a href="' . site_url('admin/pelanggan') . '" class="btn btn-outline-dark"><i class="bi bi-arrow-left me-1"></i>Kembali</a>',
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
        <form action="<?= site_url('admin/pelanggan/update/' . $pelanggan['id']) ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="row g-3 mb-4">
                <?php foreach (['foto_ktp' => 'Foto KTP', 'foto_sim' => 'Foto SIM', 'foto' => 'Foto'] as $field => $label) : ?>
                    <div class="col-12 col-md-4">
                        <div class="fw-semibold mb-2"><?= esc($label) ?> Saat Ini</div>
                        <?php if (! empty($pelanggan[$field])) : ?>
                            <img src="<?= base_url('uploads/pelanggan/' . $pelanggan[$field]) ?>" alt="<?= esc($label) ?>" class="preview-photo">
                        <?php else : ?>
                            <div class="photo-placeholder preview-placeholder">-</div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="row g-4">
                <div class="col-12 col-lg-4">
                    <label for="kode_pelanggan" class="form-label fw-semibold">Kode Pelanggan</label>
                    <input type="text" class="form-control" id="kode_pelanggan" value="<?= esc($pelanggan['kode_pelanggan']) ?>" readonly>
                </div>

                <div class="col-12 col-lg-8">
                    <label for="nama_lengkap" class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap', $pelanggan['nama_lengkap']) ?>" required>
                </div>

                <div class="col-12 col-lg-4">
                    <label for="nik" class="form-label fw-semibold">NIK</label>
                    <input type="number" class="form-control" id="nik" name="nik" value="<?= old('nik', $pelanggan['nik']) ?>" required>
                </div>

                <div class="col-12 col-lg-4">
                    <label for="no_hp" class="form-label fw-semibold">No. HP</label>
                    <input type="number" class="form-control" id="no_hp" name="no_hp" value="<?= old('no_hp', $pelanggan['no_hp']) ?>" required>
                </div>

                <div class="col-12 col-lg-4">
                    <label for="tanggal_terdaftar" class="form-label fw-semibold">Tanggal Terdaftar</label>
                    <input type="date" class="form-control" id="tanggal_terdaftar" name="tanggal_terdaftar" value="<?= old('tanggal_terdaftar', $pelanggan['tanggal_terdaftar']) ?>" required>
                </div>

                <div class="col-12 col-lg-8">
                    <label for="alamat" class="form-label fw-semibold">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="4" required><?= old('alamat', $pelanggan['alamat']) ?></textarea>
                </div>

                <div class="col-12 col-lg-4">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="aktif" <?= old('status', $pelanggan['status']) === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="non-aktif" <?= old('status', $pelanggan['status']) === 'non-aktif' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>

                <div class="col-12 col-lg-4">
                    <label for="foto_ktp" class="form-label fw-semibold">Ganti Foto KTP</label>
                    <input type="file" class="filepond" id="foto_ktp" name="foto_ktp" accept="image/png,image/jpeg,image/webp">
                    <div class="form-text">Kosongkan jika tidak ingin diganti.</div>
                </div>

                <div class="col-12 col-lg-4">
                    <label for="foto_sim" class="form-label fw-semibold">Ganti Foto SIM</label>
                    <input type="file" class="filepond" id="foto_sim" name="foto_sim" accept="image/png,image/jpeg,image/webp">
                    <div class="form-text">Kosongkan jika tidak ingin diganti.</div>
                </div>

                <div class="col-12 col-lg-4">
                    <label for="foto" class="form-label fw-semibold">Ganti Foto</label>
                    <input type="file" class="filepond" id="foto" name="foto" accept="image/png,image/jpeg,image/webp">
                    <div class="form-text">Kosongkan jika tidak ingin diganti.</div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="<?= site_url('admin/pelanggan') ?>" class="btn btn-light">Batal</a>
                <button type="submit" class="btn btn-dark">Update Pelanggan</button>
            </div>
        </form>
    </div>
</section>

<script src="https://unpkg.com/filepond-plugin-file-validate-type@^1/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size@^2/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview@^4/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
<script>
    FilePond.registerPlugin(FilePondPluginFileValidateType, FilePondPluginFileValidateSize, FilePondPluginImagePreview);
    FilePond.setOptions({
        storeAsFile: true,
        allowMultiple: false,
        maxFileSize: '2MB',
        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/webp'],
        labelIdle: 'Tarik file ke sini atau <span class="filepond--label-action">pilih file</span>',
        labelFileTypeNotAllowed: 'Tipe file tidak diizinkan',
        fileValidateTypeLabelExpectedTypes: 'Gunakan gambar jpg, png, atau webp',
        labelMaxFileSizeExceeded: 'Ukuran file terlalu besar',
        labelMaxFileSize: 'Maksimal {filesize}'
    });
    FilePond.parse(document.body);
</script>

<?= view('admin/layouts/footer') ?>
