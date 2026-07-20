<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriKeuanganModel extends Model
{
    protected $table            = 'kategori_keuangan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'kategori',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
