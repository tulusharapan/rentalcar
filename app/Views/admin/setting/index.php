<?= view('admin/layouts/header', ['title' => 'Setting Aplikasi - Aplikasi']) ?>
<?php $canManageSetting = in_array(($userRole ?? ''), ['admin', 'administrator'], true); ?>
<?php
$favicon = $setting['favicon'] ?? null;
$logo1   = $setting['logo_1'] ?? ($setting['logo'] ?? null);
$logo2   = $setting['logo_2'] ?? null;
?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'setting',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Setting Aplikasi',
    'pageSubtitle' => 'Perbarui identitas aplikasi, kontak, sosial media, logo, dan pengaturan operasional.',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<section class="content-area">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-8">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <?php if (! $canManageSetting) : ?>
                <div class="alert alert-info">Staff dapat melihat setting aplikasi, tetapi hanya administrator yang dapat memperbaruinya.</div>
            <?php endif; ?>

            <?php $errors = session()->getFlashdata('errors'); ?>
            <?php if ($errors) : ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error) : ?>
                        <div><?= esc($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="panel p-4">
                <form action="<?= site_url('admin/setting/update') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <div class="fw-semibold mb-2">Favicon Saat Ini</div>
                            <?php if (! empty($favicon)) : ?>
                                <img src="<?= base_url('uploads/settings/' . $favicon) ?>" alt="Favicon <?= esc($setting['nama_aplikasi']) ?>" class="preview-photo setting-logo-preview">
                            <?php else : ?>
                                <div class="photo-placeholder preview-placeholder">
                                    <?= esc(strtoupper(substr((string) $setting['nama_aplikasi'], 0, 1))) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="fw-semibold mb-2">Logo 1 Saat Ini</div>
                            <?php if (! empty($logo1)) : ?>
                                <img src="<?= base_url('uploads/settings/' . $logo1) ?>" alt="Logo dashboard <?= esc($setting['nama_aplikasi']) ?>" class="preview-photo setting-logo-preview">
                            <?php else : ?>
                                <div class="photo-placeholder preview-placeholder">
                                    <?= esc(strtoupper(substr((string) $setting['nama_aplikasi'], 0, 1))) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="fw-semibold mb-2">Logo 2 Saat Ini</div>
                            <?php if (! empty($logo2)) : ?>
                                <img src="<?= base_url('uploads/settings/' . $logo2) ?>" alt="Logo login <?= esc($setting['nama_aplikasi']) ?>" class="preview-photo setting-logo-preview">
                            <?php else : ?>
                                <div class="photo-placeholder preview-placeholder">
                                    <?= esc(strtoupper(substr((string) $setting['nama_aplikasi'], 0, 1))) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-12 col-lg-4">
                            <label for="favicon" class="form-label fw-semibold">Upload Favicon</label>
                            <input type="file" class="form-control" id="favicon" name="favicon" accept=".ico,.jpg,.jpeg,.png,.webp,image/*" <?= $canManageSetting ? '' : 'disabled' ?>>
                            <div class="form-text">Dipakai untuk icon tab browser. Kosongkan jika tidak diganti. Format ico, jpg, jpeg, png, atau webp. Maksimal 2 MB.</div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <label for="logo_1" class="form-label fw-semibold">Upload Logo 1</label>
                            <input type="file" class="form-control" id="logo_1" name="logo_1" accept=".jpg,.jpeg,.png,.webp,image/*" <?= $canManageSetting ? '' : 'disabled' ?>>
                            <div class="form-text">Dipakai untuk logo di dashboard admin. Kosongkan jika tidak diganti. Maksimal 2 MB.</div>
                        </div>

                        <div class="col-12 col-lg-4">
                            <label for="logo_2" class="form-label fw-semibold">Upload Logo 2</label>
                            <input type="file" class="form-control" id="logo_2" name="logo_2" accept=".jpg,.jpeg,.png,.webp,image/*" <?= $canManageSetting ? '' : 'disabled' ?>>
                            <div class="form-text">Dipakai untuk logo di halaman login. Kosongkan jika tidak diganti. Maksimal 2 MB.</div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <label for="nama_aplikasi" class="form-label fw-semibold">Nama Aplikasi</label>
                            <input type="text" class="form-control" id="nama_aplikasi" name="nama_aplikasi" value="<?= old('nama_aplikasi', $setting['nama_aplikasi']) ?>" <?= $canManageSetting ? 'required' : 'readonly' ?>>
                        </div>

                        <div class="col-12 col-lg-6">
                            <label for="nama_perusahaan" class="form-label fw-semibold">Nama Perusahaan</label>
                            <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" value="<?= old('nama_perusahaan', $setting['nama_perusahaan']) ?>" <?= $canManageSetting ? '' : 'readonly' ?>>
                        </div>

                        <div class="col-12">
                            <label for="tagline" class="form-label fw-semibold">Tagline</label>
                            <input type="text" class="form-control" id="tagline" name="tagline" value="<?= old('tagline', $setting['tagline']) ?>" placeholder="Contoh: Panel administrasi aplikasi" <?= $canManageSetting ? '' : 'readonly' ?>>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <div class="fw-bold">Kontak Admin</div>
                            <div class="small text-secondary">Informasi kontak ini bisa digunakan untuk kebutuhan invoice, halaman publik, atau komunikasi pelanggan.</div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $setting['email'] ?? '') ?>" placeholder="contoh: admin@rentalbox.test" <?= $canManageSetting ? '' : 'readonly' ?>>
                        </div>

                        <div class="col-12 col-lg-6">
                            <label for="no_whatsapp" class="form-label fw-semibold">Nomor WhatsApp</label>
                            <input type="text" class="form-control" id="no_whatsapp" name="no_whatsapp" value="<?= old('no_whatsapp', $setting['no_whatsapp'] ?? '') ?>" placeholder="contoh: 6281234567890" <?= $canManageSetting ? '' : 'readonly' ?>>
                            <div class="form-text">Gunakan format internasional tanpa tanda plus agar mudah dipakai untuk link WhatsApp.</div>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <div class="fw-bold">Link Sosial Media</div>
                            <div class="small text-secondary">Isi URL lengkap, misalnya https://instagram.com/username.</div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <label for="link_tiktok" class="form-label fw-semibold">TikTok</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-tiktok"></i></span>
                                <input type="url" class="form-control" id="link_tiktok" name="link_tiktok" value="<?= old('link_tiktok', $setting['link_tiktok'] ?? '') ?>" placeholder="https://www.tiktok.com/@username" <?= $canManageSetting ? '' : 'readonly' ?>>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <label for="link_instagram" class="form-label fw-semibold">Instagram</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-instagram"></i></span>
                                <input type="url" class="form-control" id="link_instagram" name="link_instagram" value="<?= old('link_instagram', $setting['link_instagram'] ?? '') ?>" placeholder="https://www.instagram.com/username" <?= $canManageSetting ? '' : 'readonly' ?>>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <label for="link_youtube" class="form-label fw-semibold">YouTube</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-youtube"></i></span>
                                <input type="url" class="form-control" id="link_youtube" name="link_youtube" value="<?= old('link_youtube', $setting['link_youtube'] ?? '') ?>" placeholder="https://www.youtube.com/@channel" <?= $canManageSetting ? '' : 'readonly' ?>>
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <label for="link_facebook" class="form-label fw-semibold">Facebook</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-facebook"></i></span>
                                <input type="url" class="form-control" id="link_facebook" name="link_facebook" value="<?= old('link_facebook', $setting['link_facebook'] ?? '') ?>" placeholder="https://www.facebook.com/username" <?= $canManageSetting ? '' : 'readonly' ?>>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <div class="fw-bold">Operasional Rental</div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <label for="harga_denda_per_hari" class="form-label fw-semibold">Harga Denda / Hari</label>
                            <input type="text" class="form-control currency-input" id="harga_denda_per_hari" name="harga_denda_per_hari" value="<?= old('harga_denda_per_hari', $setting['harga_denda_per_hari'] ?? 0) ?>" <?= $canManageSetting ? 'required' : 'readonly' ?>>
                            <div class="form-text">Dipakai untuk denda keterlambatan pengembalian kendaraan.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-light">Batal</a>
                        <?php if ($canManageSetting) : ?>
                            <button type="submit" class="btn btn-dark">
                                <i class="bi bi-check2-circle me-1"></i>Simpan Setting
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?= view('admin/layouts/footer') ?>
