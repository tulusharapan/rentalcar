<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'app_setting';
    protected $primaryKey       = 'setting_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'logo',
        'favicon',
        'logo_1',
        'logo_2',
        'nama_aplikasi',
        'tagline',
        'nama_perusahaan',
        'email',
        'no_whatsapp',
        'link_tiktok',
        'link_instagram',
        'link_youtube',
        'link_facebook',
        'harga_denda_per_hari',
    ];
}
