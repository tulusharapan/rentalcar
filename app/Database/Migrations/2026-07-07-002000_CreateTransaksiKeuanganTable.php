<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransaksiKeuanganTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'jenis' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'kategori_keuangan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'nominal' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('kategori_keuangan_id');
        $this->forge->addForeignKey('kategori_keuangan_id', 'kategori_keuangan', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('transaksi_keuangan');
    }

    public function down()
    {
        $this->forge->dropTable('transaksi_keuangan');
    }
}
