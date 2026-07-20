<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJenisKendaraanToMobilTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('mobil', [
            'jenis_kendaraan' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'Mobil',
                'after'      => 'kode_mobil',
            ],
        ]);

        $this->db->table('mobil')->update(['jenis_kendaraan' => 'Mobil']);
    }

    public function down()
    {
        $this->forge->dropColumn('mobil', 'jenis_kendaraan');
    }
}
