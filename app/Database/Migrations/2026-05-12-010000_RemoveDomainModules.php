<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveDomainModules extends Migration
{
    private array $tables = [
        'transaksi_laundry_detail',
        'transaksi_laundry',
        'transfer',
        'transaksi',
        'layanan_laundry',
        'cabang_laundry',
        'pelanggan',
        'rekening',
        'kategori',
    ];

    public function up()
    {
        if ($this->db->fieldExists('cabang_id', 'user')) {
            $this->forge->dropColumn('user', 'cabang_id');
        }

        foreach ($this->tables as $table) {
            if ($this->db->tableExists($table)) {
                $this->forge->dropTable($table, true);
            }
        }
    }

    public function down()
    {
        if (! $this->db->fieldExists('cabang_id', 'user')) {
            $this->forge->addColumn('user', [
                'cabang_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'role',
                ],
            ]);
        }
    }
}
