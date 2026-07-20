<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiSewaLayananModel extends Model
{
    protected $table            = 'transaksi_sewa_layanan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'transaksi_sewa_id',
        'layanan_tambahan_id',
        'nama_layanan',
        'harga_layanan',
        'qty',
        'total_harga',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
