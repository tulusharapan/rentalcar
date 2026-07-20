<?php

use App\Models\SettingModel;
use App\Models\TransaksiSewaModel;

if (! function_exists('app_setting')) {
    function app_setting(): array
    {
        static $setting = null;

        if ($setting !== null) {
            return $setting;
        }

        $defaultSetting = [
            'logo'            => null,
            'favicon'         => null,
            'logo_1'          => null,
            'logo_2'          => null,
            'nama_aplikasi'   => 'Aplikasi',
            'tagline'         => 'Panel administrasi aplikasi',
            'nama_perusahaan' => 'Aplikasi',
            'email'           => '',
            'no_whatsapp'     => '',
            'link_tiktok'     => '',
            'link_instagram'  => '',
            'link_youtube'    => '',
            'link_facebook'   => '',
            'harga_denda_per_hari' => 0,
        ];

        try {
            $settingModel = new SettingModel();
            $settingData  = $settingModel->find(1);

            if (! $settingData) {
                $settingModel->insert(array_merge(
                    ['setting_id' => 1],
                    $defaultSetting
                ));

                $settingData = $settingModel->find(1);
            }

            $setting = array_merge($defaultSetting, $settingData ?? []);
        } catch (Throwable $exception) {
            $setting = $defaultSetting;
        }

        return $setting;
    }
}

if (! function_exists('rental_topbar_notifications')) {
    function rental_topbar_notifications(): array
    {
        try {
            $today = date('Y-m-d');
            $soon = date('Y-m-d', strtotime('+2 days'));
            $transaksiModel = new TransaksiSewaModel();

            $almostDue = $transaksiModel
                ->select('transaksi_sewa.kode_transaksi, transaksi_sewa.tanggal_kembali, pelanggan.nama_lengkap, kendaraan.nama_kendaraan, kendaraan.plat_nomor')
                ->join('pelanggan', 'pelanggan.id = transaksi_sewa.pelanggan_id')
                ->join('kendaraan', 'kendaraan.id = transaksi_sewa.kendaraan_id')
                ->where('transaksi_sewa.status_transaksi', 'berjalan')
                ->where('transaksi_sewa.tanggal_kembali >=', $today)
                ->where('transaksi_sewa.tanggal_kembali <=', $soon)
                ->orderBy('transaksi_sewa.tanggal_kembali', 'ASC')
                ->findAll(8);

            $overdue = $transaksiModel
                ->select('transaksi_sewa.kode_transaksi, transaksi_sewa.tanggal_kembali, pelanggan.nama_lengkap, kendaraan.nama_kendaraan, kendaraan.plat_nomor')
                ->join('pelanggan', 'pelanggan.id = transaksi_sewa.pelanggan_id')
                ->join('kendaraan', 'kendaraan.id = transaksi_sewa.kendaraan_id')
                ->where('transaksi_sewa.status_transaksi', 'berjalan')
                ->where('transaksi_sewa.tanggal_kembali <', $today)
                ->orderBy('transaksi_sewa.tanggal_kembali', 'ASC')
                ->findAll(8);

            $startingSoon = $transaksiModel
                ->select('transaksi_sewa.kode_transaksi, transaksi_sewa.tanggal_sewa, pelanggan.nama_lengkap, kendaraan.nama_kendaraan, kendaraan.plat_nomor')
                ->join('pelanggan', 'pelanggan.id = transaksi_sewa.pelanggan_id')
                ->join('kendaraan', 'kendaraan.id = transaksi_sewa.kendaraan_id')
                ->where('transaksi_sewa.status_transaksi', 'booking')
                ->where('transaksi_sewa.tanggal_sewa >=', $today)
                ->where('transaksi_sewa.tanggal_sewa <=', $soon)
                ->orderBy('transaksi_sewa.tanggal_sewa', 'ASC')
                ->findAll(8);

            return [
                'almost_due'    => $almostDue,
                'overdue'       => $overdue,
                'starting_soon' => $startingSoon,
                'total'         => count($almostDue) + count($overdue) + count($startingSoon),
            ];
        } catch (Throwable $exception) {
            return [
                'almost_due'    => [],
                'overdue'       => [],
                'starting_soon' => [],
                'total'         => 0,
            ];
        }
    }
}
