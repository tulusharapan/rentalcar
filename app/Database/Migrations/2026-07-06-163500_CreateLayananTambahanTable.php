<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLayananTambahanTable extends Migration
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
            'nama_layanan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'harga_layanan' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'default'    => 'bi-stars',
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
        $this->forge->addUniqueKey('nama_layanan');
        $this->forge->createTable('layanan_tambahan');
    }

    public function down()
    {
        $this->forge->dropTable('layanan_tambahan');
    }
}
