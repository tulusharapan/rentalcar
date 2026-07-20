<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePelangganTable extends Migration
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
            'kode_pelanggan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'nama_lengkap' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'nik' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'alamat' => [
                'type' => 'TEXT',
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],
            'tanggal_terdaftar' => [
                'type' => 'DATE',
            ],
            'foto_ktp' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'foto_sim' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'aktif',
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
        $this->forge->addUniqueKey('kode_pelanggan');
        $this->forge->addUniqueKey('nik');
        $this->forge->createTable('pelanggan');
    }

    public function down()
    {
        $this->forge->dropTable('pelanggan');
    }
}
