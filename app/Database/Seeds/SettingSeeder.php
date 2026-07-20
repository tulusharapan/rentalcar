<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run()
    {
        $settingTable = $this->db->table('app_setting');
        $setting      = $settingTable->where('setting_id', 1)->get()->getRowArray();

        if ($setting) {
            return;
        }

        $settingTable->insert([
            'setting_id'      => 1,
            'logo'            => null,
            'favicon'         => null,
            'logo_1'          => null,
            'logo_2'          => null,
            'nama_aplikasi'   => 'Aplikasi',
            'tagline'         => 'Panel administrasi aplikasi',
            'nama_perusahaan' => 'Aplikasi',
        ]);
    }
}
