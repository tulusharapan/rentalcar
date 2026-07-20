<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateMobilStatusToReadyMaintenance extends Migration
{
    public function up()
    {
        $this->db->table('mobil')->where('status', 'Tersedia')->update(['status' => 'ready']);
        $this->db->table('mobil')->where('status', 'Disewa')->update(['status' => 'ready']);
        $this->db->table('mobil')->where('status', 'Maintenance')->update(['status' => 'maintenance']);

        $this->forge->modifyColumn('mobil', [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'ready',
            ],
        ]);
    }

    public function down()
    {
        $this->db->table('mobil')->where('status', 'ready')->update(['status' => 'Tersedia']);
        $this->db->table('mobil')->where('status', 'maintenance')->update(['status' => 'Maintenance']);

        $this->forge->modifyColumn('mobil', [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'default'    => 'Tersedia',
            ],
        ]);
    }
}
