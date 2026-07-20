<?php

namespace App\Models;

use CodeIgniter\Model;

class PembayaranSewaModel extends Model
{
    protected $table            = 'pembayaran_sewa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'transaksi_sewa_id',
        'tanggal_pembayaran',
        'jumlah_bayar',
        'metode_pembayaran',
        'catatan',
        'transaksi_keuangan_id',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
