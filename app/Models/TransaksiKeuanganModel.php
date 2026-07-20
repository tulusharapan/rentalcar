<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiKeuanganModel extends Model
{
    protected $table            = 'transaksi_keuangan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'tanggal',
        'jenis',
        'kategori_keuangan_id',
        'kategori',
        'nominal',
        'catatan',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
