<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'user';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'name',
        'email',
        'photo',
        'password',
        'role',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
