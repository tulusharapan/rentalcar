<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedKategoriKeuanganRental extends Migration
{
    private array $categories = [
        1 => 'Lainnya',
        2 => 'Penyewaan Kendaraan',
        3 => 'Pemasukan - Denda Keterlambatan',
        4 => 'Pemasukan - Layanan Tambahan',
        5 => 'Pemasukan - Antar Jemput',
        6 => 'Pemasukan - Driver',
        7 => 'Pemasukan - Lain-lain',
        8 => 'Pengeluaran - Servis dan Maintenance',
        9 => 'Pengeluaran - Sparepart',
        10 => 'Pengeluaran - Cuci Kendaraan',
        11 => 'Pengeluaran - BBM Operasional',
        12 => 'Pengeluaran - Gaji Driver',
        13 => 'Pengeluaran - Pajak dan STNK',
        14 => 'Pengeluaran - Asuransi Kendaraan',
        15 => 'Pengeluaran - Sewa Garasi atau Kantor',
        16 => 'Pengeluaran - Listrik, Air, dan Internet',
        17 => 'Pengeluaran - Marketing dan Iklan',
        18 => 'Pengeluaran - Administrasi',
        19 => 'Pengeluaran - Lain-lain',
    ];

    public function up()
    {
        $now = date('Y-m-d H:i:s');

        foreach ($this->categories as $id => $kategori) {
            $this->db->query(
                'INSERT IGNORE INTO kategori_keuangan (id, kategori, created_at, updated_at) VALUES (?, ?, ?, ?)',
                [$id, $kategori, $now, $now]
            );
        }
    }

    public function down()
    {
        $this->db->table('kategori_keuangan')
            ->whereIn('id', array_keys($this->categories))
            ->delete();
    }
}
