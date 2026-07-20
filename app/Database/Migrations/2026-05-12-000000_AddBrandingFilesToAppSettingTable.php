<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBrandingFilesToAppSettingTable extends Migration
{
    public function up()
    {
        $fields = [];

        if (! $this->db->fieldExists('favicon', 'app_setting')) {
            $fields['favicon'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'logo',
            ];
        }

        if (! $this->db->fieldExists('logo_1', 'app_setting')) {
            $fields['logo_1'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'favicon',
            ];
        }

        if (! $this->db->fieldExists('logo_2', 'app_setting')) {
            $fields['logo_2'] = [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'logo_1',
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('app_setting', $fields);
        }
    }

    public function down()
    {
        $fields = [];

        foreach (['favicon', 'logo_1', 'logo_2'] as $field) {
            if ($this->db->fieldExists($field, 'app_setting')) {
                $fields[] = $field;
            }
        }

        if ($fields !== []) {
            $this->forge->dropColumn('app_setting', $fields);
        }
    }
}
