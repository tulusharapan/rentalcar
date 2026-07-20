<?php

namespace App\Models;

use CodeIgniter\Model;

class PelangganModel extends Model
{
    protected $table            = 'pelanggan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'kode_pelanggan',
        'nama_lengkap',
        'nik',
        'alamat',
        'no_hp',
        'tanggal_terdaftar',
        'foto_ktp',
        'foto_sim',
        'foto',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
