<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePembayaranDpToBelumLunas extends Migration
{
    public function up()
    {
        $this->db->table('transaksi_sewa')
            ->where('status_pembayaran', 'dp')
            ->update(['status_pembayaran' => 'belum_lunas']);
    }

    public function down()
    {
        $this->db->table('transaksi_sewa')
            ->where('status_pembayaran', 'belum_lunas')
            ->update(['status_pembayaran' => 'dp']);
    }
}
