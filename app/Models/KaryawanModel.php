<?php

namespace App\Models;

use CodeIgniter\Model;

class KaryawanModel extends Model
{
    protected $table            = 'karyawan';
    protected $primaryKey       = 'idkaryawan';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['idkaryawan', 'namakaryawan', 'nohp', 'alamat'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'idkaryawan'    => 'required',
        'namakaryawan'  => 'required|max_length[100]',
        'nohp'          => 'required|max_length[15]',
        'alamat'        => 'required',
    ];
    protected $validationMessages   = [
        'idkaryawan' => [
            'required' => 'ID Karyawan harus diisi',
        ],
        'namakaryawan' => [
            'required' => 'Nama Karyawan harus diisi',
            'max_length' => 'Nama Karyawan maksimal 100 karakter',
        ],
        'nohp' => [
            'required' => 'Nomor HP harus diisi',
            'max_length' => 'Nomor HP maksimal 15 karakter',
        ],
        'alamat' => [
            'required' => 'Alamat harus diisi',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Generate ID karyawan baru dengan format KRY-XXXXX
     * 
     * @return string
     */
    public function generateId()
    {
        $prefix = 'KRY-';
        $lastId = $this->orderBy('idkaryawan', 'DESC')->first();

        if (!$lastId) {
            return $prefix . '00001';
        }

        $lastNumber = substr($lastId['idkaryawan'], 4);
        $nextNumber = intval($lastNumber) + 1;

        return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
