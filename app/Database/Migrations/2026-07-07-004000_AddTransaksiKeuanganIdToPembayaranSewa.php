<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTransaksiKeuanganIdToPembayaranSewa extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pembayaran_sewa', [
            'transaksi_keuangan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'catatan',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('pembayaran_sewa', 'transaksi_keuangan_id');
    }
}
