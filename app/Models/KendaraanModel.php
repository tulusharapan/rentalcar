<?php

namespace App\Models;

use CodeIgniter\Model;

class KendaraanModel extends Model
{
    protected $table            = 'kendaraan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'kode_kendaraan',
        'jenis_kendaraan',
        'plat_nomor',
        'merk',
        'nama_kendaraan',
        'tahun',
        'warna',
        'harga_sewa_per_hari',
        'status',
        'keterangan',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
