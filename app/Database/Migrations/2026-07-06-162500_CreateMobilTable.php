<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMobilTable extends Migration
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
            'kode_mobil' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'plat_nomor' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'merk' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
            ],
            'nama_mobil' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'tahun' => [
                'type'       => 'YEAR',
                'constraint' => 4,
            ],
            'warna' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'harga_sewa_per_hari' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'Tersedia',
            ],
            'keterangan' => [
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
        $this->forge->addUniqueKey('kode_mobil');
        $this->forge->addUniqueKey('plat_nomor');
        $this->forge->createTable('mobil');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'mobil_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'file_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addKey('mobil_id');
        $this->forge->addForeignKey('mobil_id', 'mobil', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('mobil_foto');
    }

    public function down()
    {
        $this->forge->dropTable('mobil_foto');
        $this->forge->dropTable('mobil');
    }
}
