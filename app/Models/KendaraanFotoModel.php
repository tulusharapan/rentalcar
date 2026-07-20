<?php

namespace App\Models;

use CodeIgniter\Model;

class KendaraanFotoModel extends Model
{
    protected $table            = 'kendaraan_foto';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'kendaraan_id',
        'file_name',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
