<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('cek-ketersediaan', 'Home::cekKetersediaan');
$routes->get('kendaraan/(:num)', 'Home::detail/$1');
$routes->get('mobil/(:num)', 'Home::detail/$1');
$routes->get('login', 'AuthController::index');
$routes->post('login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');

$routes->get('admin', static fn () => redirect()->to('/admin/dashboard'));
$routes->get('admin/dashboard', 'AdminController::dashboard');

$routes->get('admin/pelanggan', 'PelangganController::index');
$routes->get('admin/pelanggan/create', 'PelangganController::create');
$routes->post('admin/pelanggan/store', 'PelangganController::store');
$routes->get('admin/pelanggan/detail/(:num)', 'PelangganController::detail/$1');
$routes->get('admin/pelanggan/edit/(:num)', 'PelangganController::edit/$1');
$routes->post('admin/pelanggan/update/(:num)', 'PelangganController::update/$1');
$routes->post('admin/pelanggan/delete/(:num)', 'PelangganController::delete/$1');

$routes->get('admin/kendaraan', 'KendaraanController::index');
$routes->get('admin/kendaraan/monitor', 'KendaraanController::monitor');
$routes->get('admin/kendaraan/create', 'KendaraanController::create');
$routes->post('admin/kendaraan/store', 'KendaraanController::store');
$routes->get('admin/kendaraan/detail/(:num)', 'KendaraanController::detail/$1');
$routes->get('admin/kendaraan/edit/(:num)', 'KendaraanController::edit/$1');
$routes->post('admin/kendaraan/update/(:num)', 'KendaraanController::update/$1');
$routes->post('admin/kendaraan/delete/(:num)', 'KendaraanController::delete/$1');
$routes->post('admin/kendaraan/upload-photo', 'KendaraanController::uploadPhoto');
$routes->delete('admin/kendaraan/revert-photo', 'KendaraanController::revertPhoto');

$routes->get('admin/layanan-tambahan', 'LayananTambahanController::index');
$routes->get('admin/layanan-tambahan/create', 'LayananTambahanController::create');
$routes->post('admin/layanan-tambahan/store', 'LayananTambahanController::store');
$routes->get('admin/layanan-tambahan/edit/(:num)', 'LayananTambahanController::edit/$1');
$routes->post('admin/layanan-tambahan/update/(:num)', 'LayananTambahanController::update/$1');
$routes->post('admin/layanan-tambahan/delete/(:num)', 'LayananTambahanController::delete/$1');

$routes->get('admin/kategori-keuangan', 'KategoriKeuanganController::index');
$routes->get('admin/kategori-keuangan/create', 'KategoriKeuanganController::create');
$routes->post('admin/kategori-keuangan/store', 'KategoriKeuanganController::store');
$routes->get('admin/kategori-keuangan/edit/(:num)', 'KategoriKeuanganController::edit/$1');
$routes->post('admin/kategori-keuangan/update/(:num)', 'KategoriKeuanganController::update/$1');
$routes->post('admin/kategori-keuangan/delete/(:num)', 'KategoriKeuanganController::delete/$1');

$routes->get('admin/transaksi-keuangan', 'TransaksiKeuanganController::index');
$routes->get('admin/transaksi-keuangan/laporan', 'TransaksiKeuanganController::laporan');
$routes->get('admin/transaksi-keuangan/laporan/pdf', 'TransaksiKeuanganController::laporanPdf');
$routes->get('admin/transaksi-keuangan/create', 'TransaksiKeuanganController::create');
$routes->post('admin/transaksi-keuangan/store', 'TransaksiKeuanganController::store');
$routes->get('admin/transaksi-keuangan/edit/(:num)', 'TransaksiKeuanganController::edit/$1');
$routes->post('admin/transaksi-keuangan/update/(:num)', 'TransaksiKeuanganController::update/$1');
$routes->post('admin/transaksi-keuangan/delete/(:num)', 'TransaksiKeuanganController::delete/$1');

$routes->get('admin/transaksi-sewa', 'TransaksiSewaController::index');
$routes->get('admin/transaksi-sewa/laporan', 'TransaksiSewaController::laporan');
$routes->get('admin/transaksi-sewa/laporan/pdf', 'TransaksiSewaController::laporanPdf');
$routes->get('admin/transaksi-sewa/create', 'TransaksiSewaController::create');
$routes->post('admin/transaksi-sewa/store', 'TransaksiSewaController::store');
$routes->get('admin/transaksi-sewa/invoice/(:num)', 'TransaksiSewaController::invoicePdf/$1');
$routes->get('admin/transaksi-sewa/surat-jalan/(:num)', 'TransaksiSewaController::suratJalanPdf/$1');
$routes->get('admin/transaksi-sewa/detail/(:num)', 'TransaksiSewaController::detail/$1');
$routes->get('admin/transaksi-sewa/edit/(:num)', 'TransaksiSewaController::edit/$1');
$routes->post('admin/transaksi-sewa/update/(:num)', 'TransaksiSewaController::update/$1');
$routes->post('admin/transaksi-sewa/delete/(:num)', 'TransaksiSewaController::delete/$1');
$routes->get('admin/transaksi-sewa/available-kendaraan', 'TransaksiSewaController::availableKendaraanJson');
$routes->get('admin/transaksi-sewa/pembayaran/kuitansi/(:num)/(:num)', 'TransaksiSewaController::kuitansiPdf/$1/$2');
$routes->get('admin/transaksi-sewa/pembayaran/(:num)', 'TransaksiSewaController::pembayaran/$1');
$routes->post('admin/transaksi-sewa/pembayaran/store/(:num)', 'TransaksiSewaController::storePembayaran/$1');
$routes->post('admin/transaksi-sewa/pembayaran/delete/(:num)/(:num)', 'TransaksiSewaController::deletePembayaran/$1/$2');

$routes->get('admin/users', 'UserController::index');
$routes->get('admin/users/create', 'UserController::create');
$routes->post('admin/users/store', 'UserController::store');
$routes->get('admin/users/edit/(:num)', 'UserController::edit/$1');
$routes->post('admin/users/update/(:num)', 'UserController::update/$1');
$routes->post('admin/users/delete/(:num)', 'UserController::delete/$1');

$routes->get('admin/profile', 'UserController::profile');
$routes->post('admin/profile/update', 'UserController::updateProfile');
$routes->get('admin/change-password', 'UserController::changePassword');
$routes->post('admin/change-password/update', 'UserController::updatePassword');

$routes->get('admin/backup', 'BackupController::index');
$routes->get('admin/backup/download', 'BackupController::download');

$routes->get('admin/setting', 'SettingController::index');
$routes->post('admin/setting/update', 'SettingController::update');
