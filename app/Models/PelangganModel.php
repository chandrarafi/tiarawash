<?php

namespace App\Models;

use CodeIgniter\Model;

class PelangganModel extends Model
{
    protected $table            = 'pelanggan';
    protected $primaryKey       = 'kode_pelanggan';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kode_pelanggan', 'user_id', 'nama_pelanggan', 'no_hp', 'alamat'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'kode_pelanggan'  => 'required',
        'user_id'         => 'permit_empty|numeric|integer',
        'nama_pelanggan'  => 'required|max_length[100]',
        'no_hp'           => 'required|max_length[15]',
        'alamat'          => 'required',
    ];

    protected $validationMessages   = [
        'kode_pelanggan' => [
            'required' => 'Kode Pelanggan harus diisi',
        ],
        'user_id' => [
            'numeric' => 'User ID harus berupa angka',
            'integer' => 'User ID harus berupa bilangan bulat',
        ],
        'nama_pelanggan' => [
            'required' => 'Nama Pelanggan harus diisi',
            'max_length' => 'Nama Pelanggan maksimal 100 karakter',
        ],
        'no_hp' => [
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
     * Generate kode pelanggan baru dengan format PEL-XXXXX
     * 
     * @return string
     */
    public function generateKode()
    {
        $prefix = 'PEL-';
        $lastKode = $this->orderBy('kode_pelanggan', 'DESC')->first();

        if (!$lastKode) {
            return $prefix . '00001';
        }

        $lastNumber = substr($lastKode['kode_pelanggan'], 4);
        $nextNumber = intval($lastNumber) + 1;

        return $prefix . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Dapatkan data pelanggan dengan data user
     * 
     * @param string $kode_pelanggan
     * @return array|null
     */
    public function getPelangganWithUser($kode_pelanggan)
    {
        // Cek dulu apakah pelanggan ada
        $pelanggan = $this->find($kode_pelanggan);
        if (!$pelanggan) {
            log_message('error', 'Pelanggan tidak ditemukan dengan kode: ' . $kode_pelanggan);
            return null;
        }

        $builder = $this->db->table('pelanggan');
        $builder->select('pelanggan.*, users.username, users.email, users.name, users.role');
        $builder->join('users', 'users.id = pelanggan.user_id', 'left');
        $builder->where('pelanggan.kode_pelanggan', $kode_pelanggan);

        $result = $builder->get()->getRowArray();

        // Jika hasil query kosong, kembalikan data pelanggan saja
        if (!$result) {
            log_message('debug', 'Join query tidak mengembalikan hasil, mengembalikan data pelanggan saja');
            return $pelanggan;
        }

        return $result;
    }

    /**
     * Dapatkan pelanggan berdasarkan user_id
     * 
     * @param int $user_id
     * @return array
     */
    public function getPelangganByUserId($user_id)
    {
        return $this->where('user_id', $user_id)->first();
    }
}
