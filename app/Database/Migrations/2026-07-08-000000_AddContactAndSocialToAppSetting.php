<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContactAndSocialToAppSetting extends Migration
{
    private array $fields = [
        'email' => [
            'type'       => 'VARCHAR',
            'constraint' => 150,
            'null'       => true,
            'after'      => 'nama_perusahaan',
        ],
        'no_whatsapp' => [
            'type'       => 'VARCHAR',
            'constraint' => 30,
            'null'       => true,
            'after'      => 'email',
        ],
        'link_tiktok' => [
            'type'       => 'VARCHAR',
            'constraint' => 255,
            'null'       => true,
            'after'      => 'no_whatsapp',
        ],
        'link_instagram' => [
            'type'       => 'VARCHAR',
            'constraint' => 255,
            'null'       => true,
            'after'      => 'link_tiktok',
        ],
        'link_youtube' => [
            'type'       => 'VARCHAR',
            'constraint' => 255,
            'null'       => true,
            'after'      => 'link_instagram',
        ],
        'link_facebook' => [
            'type'       => 'VARCHAR',
            'constraint' => 255,
            'null'       => true,
            'after'      => 'link_youtube',
        ],
    ];

    public function up()
    {
        $fields = [];

        foreach ($this->fields as $field => $definition) {
            if (! $this->db->fieldExists($field, 'app_setting')) {
                $fields[$field] = $definition;
            }
        }

        if ($fields !== []) {
            $this->forge->addColumn('app_setting', $fields);
        }
    }

    public function down()
    {
        $fields = [];

        foreach (array_keys($this->fields) as $field) {
            if ($this->db->fieldExists($field, 'app_setting')) {
                $fields[] = $field;
            }
        }

        if ($fields !== []) {
            $this->forge->dropColumn('app_setting', $fields);
        }
    }
}
