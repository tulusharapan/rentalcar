<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAppSettingTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'setting_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'logo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'favicon' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'logo_1' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'logo_2' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'nama_aplikasi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'tagline' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'nama_perusahaan' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('setting_id', true);
        $this->forge->createTable('app_setting');
    }

    public function down()
    {
        $this->forge->dropTable('app_setting');
    }
}
