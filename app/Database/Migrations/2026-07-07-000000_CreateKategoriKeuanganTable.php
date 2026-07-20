<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKategoriKeuanganTable extends Migration
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
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
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
        $this->forge->addUniqueKey('kategori');
        $this->forge->createTable('kategori_keuangan');
    }

    public function down()
    {
        $this->forge->dropTable('kategori_keuangan');
    }
}
