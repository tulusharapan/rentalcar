<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateKategoriKeuanganDefaultToLainnya extends Migration
{
    public function up()
    {
        $this->db->table('kategori_keuangan')
            ->where('id', 1)
            ->update([
                'kategori'   => 'Lainnya',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $this->db->table('transaksi_keuangan')
            ->where('kategori_keuangan_id', 1)
            ->update([
                'kategori'   => 'Lainnya',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    public function down()
    {
        $this->db->table('kategori_keuangan')
            ->where('id', 1)
            ->update([
                'kategori'   => 'Default',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $this->db->table('transaksi_keuangan')
            ->where('kategori_keuangan_id', 1)
            ->update([
                'kategori'   => 'Default',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }
}
