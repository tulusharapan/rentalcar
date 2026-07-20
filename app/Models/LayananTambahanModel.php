<?php

namespace App\Models;

use CodeIgniter\Model;

class LayananTambahanModel extends Model
{
    protected $table            = 'layanan_tambahan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'nama_layanan',
        'harga_layanan',
        'icon',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
