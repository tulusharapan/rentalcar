<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDendaSettingAndTransaksiSewa extends Migration
{
    public function up()
    {
        $this->forge->addColumn('app_setting', [
            'harga_denda_per_hari' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'nama_perusahaan',
            ],
        ]);

        $this->forge->addColumn('transaksi_sewa', [
            'tanggal_dikembalikan' => [
                'type'  => 'DATE',
                'null'  => true,
                'after' => 'tanggal_kembali',
            ],
            'denda' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'total_layanan',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaksi_sewa', ['tanggal_dikembalikan', 'denda']);
        $this->forge->dropColumn('app_setting', 'harga_denda_per_hari');
    }
}
