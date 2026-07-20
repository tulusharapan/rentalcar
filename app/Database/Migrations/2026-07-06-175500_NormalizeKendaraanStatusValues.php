<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NormalizeKendaraanStatusValues extends Migration
{
    public function up()
    {
        $this->db->table('kendaraan')->whereIn('status', ['Tersedia', 'Disewa'])->update(['status' => 'ready']);
        $this->db->table('kendaraan')->where('status', 'Maintenance')->update(['status' => 'maintenance']);
    }

    public function down()
    {
        $this->db->table('kendaraan')->where('status', 'ready')->update(['status' => 'Tersedia']);
        $this->db->table('kendaraan')->where('status', 'maintenance')->update(['status' => 'Maintenance']);
    }
}
