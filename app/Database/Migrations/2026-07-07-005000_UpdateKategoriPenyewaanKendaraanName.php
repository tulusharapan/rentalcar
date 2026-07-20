<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateKategoriPenyewaanKendaraanName extends Migration
{
    public function up()
    {
        $this->db->table('kategori_keuangan')
            ->where('id', 2)
            ->update([
                'kategori'   => 'Penyewaan Kendaraan',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $this->db->table('transaksi_keuangan')
            ->where('kategori_keuangan_id', 2)
            ->update([
                'kategori'   => 'Penyewaan Kendaraan',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    public function down()
    {
        $this->db->table('kategori_keuangan')
            ->where('id', 2)
            ->update([
                'kategori'   => 'Pemasukan - Sewa Kendaraan',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $this->db->table('transaksi_keuangan')
            ->where('kategori_keuangan_id', 2)
            ->update([
                'kategori'   => 'Pemasukan - Sewa Kendaraan',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }
}
