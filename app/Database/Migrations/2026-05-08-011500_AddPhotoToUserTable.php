<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhotoToUserTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('user', [
            'photo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'email',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('user', 'photo');
    }
}
