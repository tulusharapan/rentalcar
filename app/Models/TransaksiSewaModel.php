<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiSewaModel extends Model
{
    protected $table            = 'transaksi_sewa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'kode_transaksi',
        'pelanggan_id',
        'kendaraan_id',
        'tanggal_transaksi',
        'tanggal_sewa',
        'tanggal_kembali',
        'tanggal_dikembalikan',
        'hari_terlambat',
        'harga_denda_per_hari',
        'lama_sewa',
        'harga_sewa_per_hari',
        'subtotal_sewa',
        'total_layanan',
        'denda',
        'total_tagihan',
        'catatan',
        'status_transaksi',
        'status_pembayaran',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
