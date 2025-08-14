<?php

namespace App\Models;

use CodeIgniter\Model;

class PerlengkapanModel extends Model
{
    protected $table            = 'perlengkapan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama', 'kategori', 'stok', 'harga', 'deskripsi'];


    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    protected $validationRules = [
        'nama'     => 'required|min_length[3]|max_length[100]',
        'kategori' => 'required|in_list[alat,bahan]',
        'stok'     => 'required|numeric|integer',
        'harga'    => 'required|numeric',
    ];
    protected $validationMessages = [
        'nama' => [
            'required'   => 'Nama perlengkapan harus diisi',
            'min_length' => 'Nama perlengkapan minimal 3 karakter',
            'max_length' => 'Nama perlengkapan maksimal 100 karakter',
        ],
        'kategori' => [
            'required' => 'Kategori harus diisi',
            'in_list'  => 'Kategori harus alat atau bahan',
        ],
        'stok' => [
            'required' => 'Stok harus diisi',
            'numeric'  => 'Stok harus berupa angka',
            'integer'  => 'Stok harus berupa bilangan bulat',
        ],
        'harga' => [
            'required' => 'Harga harus diisi',
            'numeric'  => 'Harga harus berupa angka',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
