<?= view('admin/layouts/header', ['title' => 'Edit Transaksi Sewa - Aplikasi']) ?>
<?= view('admin/layouts/sidebar', [
    'activeMenu' => 'transaksi-sewa',
    'userName'   => $userName,
    'userEmail'  => $userEmail,
]) ?>
<?= view('admin/layouts/topbar', [
    'pageTitle'    => 'Edit Transaksi Sewa',
    'pageSubtitle' => $transaksi['kode_transaksi'],
    'topbarAction' => '<a href="' . site_url('admin/transaksi-sewa') . '" class="btn btn-outline-dark"><i class="bi bi-arrow-left me-1"></i>Kembali</a>',
    'userName'     => $userName,
    'userRole'     => $userRole,
]) ?>

<?= view('admin/transaksi_sewa/form', [
    'action'                  => site_url('admin/transaksi-sewa/update/' . $transaksi['id']),
    'transaksi'               => $transaksi,
    'layananTerpilih'         => $layananTerpilih,
    'kodeTransaksi'           => $transaksi['kode_transaksi'],
    'pelangganOptions'        => $pelangganOptions,
    'kendaraanOptions'            => $kendaraanOptions,
    'layananOptions'          => $layananOptions,
    'statusTransaksiOptions'  => $statusTransaksiOptions,
    'metodePembayaranOptions' => $metodePembayaranOptions,
    'hargaDendaPerHari'       => $hargaDendaPerHari,
    'showInitialPayment'      => false,
]) ?>

<?= view('admin/layouts/footer') ?>
