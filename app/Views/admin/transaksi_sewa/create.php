<?= view('admin/layouts/header', ['title' => 'Tambah Transaksi Sewa - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'transaksi-sewa',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Tambah Transaksi Sewa',
    'pageSubtitle' => 'Buat invoice sewa dan catat pembayaran pertama.',
    'topbarAction' => '<a href="' . site_url('admin/transaksi-sewa') . '" class="btn btn-outline-dark"><i class="bi bi-arrow-left me-1"></i>Kembali</a>',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<?= view('admin/transaksi_sewa/form', [
    'action'                  => site_url('admin/transaksi-sewa/store'),
    'transaksi'               => null,
    'layananTerpilih'         => [],
    'kodeTransaksi'           => $kodeTransaksi,
    'pelangganOptions'        => $pelangganOptions,
    'kendaraanOptions'            => $kendaraanOptions,
    'layananOptions'          => $layananOptions,
    'statusTransaksiOptions'  => $statusTransaksiOptions,
    'metodePembayaranOptions' => $metodePembayaranOptions,
    'hargaDendaPerHari'       => $hargaDendaPerHari,
    'showInitialPayment'      => true,
]) ?>

<?= view('admin/layouts/footer') ?>
