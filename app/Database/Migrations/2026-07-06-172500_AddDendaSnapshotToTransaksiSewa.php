<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDendaSnapshotToTransaksiSewa extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaksi_sewa', [
            'hari_terlambat' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'tanggal_dikembalikan',
            ],
            'harga_denda_per_hari' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'hari_terlambat',
            ],
        ]);

        $this->db->query("
            UPDATE transaksi_sewa
            SET hari_terlambat = DATEDIFF(tanggal_dikembalikan, tanggal_kembali)
            WHERE tanggal_dikembalikan IS NOT NULL
                AND tanggal_dikembalikan > tanggal_kembali
        ");

        $this->db->query("
            UPDATE transaksi_sewa
            SET harga_denda_per_hari = FLOOR(denda / hari_terlambat)
            WHERE hari_terlambat > 0
                AND denda > 0
        ");
    }

    public function down()
    {
        $this->forge->dropColumn('transaksi_sewa', ['hari_terlambat', 'harga_denda_per_hari']);
    }
}
