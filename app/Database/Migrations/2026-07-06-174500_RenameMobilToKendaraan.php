<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameMobilToKendaraan extends Migration
{
    public function up()
    {
        $this->dropForeignKeys('mobil_foto');
        $this->dropForeignKeys('transaksi_sewa', 'mobil');

        $this->db->query('RENAME TABLE mobil TO kendaraan');
        $this->db->query('RENAME TABLE mobil_foto TO kendaraan_foto');
        $this->db->query('ALTER TABLE kendaraan CHANGE kode_mobil kode_kendaraan VARCHAR(20) NOT NULL');
        $this->db->query('ALTER TABLE kendaraan CHANGE nama_mobil nama_kendaraan VARCHAR(150) NOT NULL');
        $this->db->query('ALTER TABLE kendaraan_foto CHANGE mobil_id kendaraan_id INT UNSIGNED NOT NULL');
        $this->db->query('ALTER TABLE transaksi_sewa CHANGE mobil_id kendaraan_id INT UNSIGNED NOT NULL');

        $this->db->query('ALTER TABLE kendaraan_foto ADD CONSTRAINT kendaraan_foto_kendaraan_id_foreign FOREIGN KEY (kendaraan_id) REFERENCES kendaraan(id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE transaksi_sewa ADD CONSTRAINT transaksi_sewa_kendaraan_id_foreign FOREIGN KEY (kendaraan_id) REFERENCES kendaraan(id) ON DELETE RESTRICT ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->dropForeignKeys('kendaraan_foto');
        $this->dropForeignKeys('transaksi_sewa', 'kendaraan');

        $this->db->query('ALTER TABLE transaksi_sewa CHANGE kendaraan_id mobil_id INT UNSIGNED NOT NULL');
        $this->db->query('ALTER TABLE kendaraan_foto CHANGE kendaraan_id mobil_id INT UNSIGNED NOT NULL');
        $this->db->query('ALTER TABLE kendaraan CHANGE nama_kendaraan nama_mobil VARCHAR(150) NOT NULL');
        $this->db->query('ALTER TABLE kendaraan CHANGE kode_kendaraan kode_mobil VARCHAR(20) NOT NULL');
        $this->db->query('RENAME TABLE kendaraan_foto TO mobil_foto');
        $this->db->query('RENAME TABLE kendaraan TO mobil');

        $this->db->query('ALTER TABLE mobil_foto ADD CONSTRAINT mobil_foto_mobil_id_foreign FOREIGN KEY (mobil_id) REFERENCES mobil(id) ON DELETE CASCADE ON UPDATE CASCADE');
        $this->db->query('ALTER TABLE transaksi_sewa ADD CONSTRAINT transaksi_sewa_mobil_id_foreign FOREIGN KEY (mobil_id) REFERENCES mobil(id) ON DELETE RESTRICT ON UPDATE CASCADE');
    }

    private function dropForeignKeys(string $table, ?string $referencedTable = null): void
    {
        $database = $this->db->database;
        $builder = $this->db->table('information_schema.KEY_COLUMN_USAGE')
            ->select('CONSTRAINT_NAME')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_NAME', $table)
            ->where('REFERENCED_TABLE_NAME IS NOT NULL', null, false);

        if ($referencedTable !== null) {
            $builder->where('REFERENCED_TABLE_NAME', $referencedTable);
        }

        $constraints = $builder->distinct()->get()->getResultArray();

        foreach ($constraints as $constraint) {
            $constraintName = $constraint['CONSTRAINT_NAME'] ?? '';

            if ($constraintName !== '') {
                $this->db->query('ALTER TABLE ' . $this->db->protectIdentifiers($table) . ' DROP FOREIGN KEY ' . $this->db->protectIdentifiers($constraintName));
            }
        }
    }
}
