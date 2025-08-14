<?php

namespace App\Models;

use CodeIgniter\Model;

class LayananModel extends Model
{
    protected $table            = 'layanan';
    protected $primaryKey       = 'kode_layanan';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_layanan',
        'nama_layanan',
        'jenis_kendaraan',
        'harga',
        'durasi_menit',
        'deskripsi',
        'foto',
        'status'
    ];


    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    protected $validationRules = [
        'kode_layanan'    => 'required|is_unique[layanan.kode_layanan]',
        'nama_layanan'    => 'required|max_length[255]',
        'jenis_kendaraan' => 'required|in_list[motor,mobil,lainnya]',
        'harga'           => 'required|numeric|greater_than[0]',
        'durasi_menit'    => 'required|integer|greater_than[0]',
        'status'          => 'required|in_list[aktif,nonaktif]',
        'deskripsi'       => 'permit_empty|max_length[1000]',
        'foto'            => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [
        'kode_layanan' => [
            'required' => 'Kode layanan harus diisi',
            'is_unique' => 'Kode layanan sudah digunakan',
        ],
        'nama_layanan' => [
            'required' => 'Nama layanan harus diisi',
            'max_length' => 'Nama layanan maksimal 255 karakter',
        ],
        'jenis_kendaraan' => [
            'required' => 'Jenis kendaraan harus diisi',
            'in_list' => 'Jenis kendaraan tidak valid',
        ],
        'harga' => [
            'required' => 'Harga harus diisi',
            'numeric' => 'Harga harus berupa angka',
            'greater_than' => 'Harga harus lebih dari 0',
        ],
        'durasi_menit' => [
            'required' => 'Durasi harus diisi',
            'integer' => 'Durasi harus berupa angka',
            'greater_than' => 'Durasi harus lebih dari 0',
        ],
        'status' => [
            'required' => 'Status harus diisi',
            'in_list' => 'Status tidak valid',
        ],
        'deskripsi' => [
            'max_length' => 'Deskripsi maksimal 1000 karakter',
        ],
        'foto' => [
            'uploaded' => 'File foto harus diupload',
            'max_size' => 'Ukuran foto maksimal 2MB',
            'is_image' => 'File harus berupa gambar',
            'mime_in' => 'Format foto harus JPG, JPEG, atau PNG',
        ],
    ];


    protected $beforeInsert = [];
    protected $beforeUpdate = [];
    protected $afterInsert  = [];
    protected $afterUpdate  = [];

    public function getValidationRulesForEdit($excludeKode = null)
    {
        $rules = $this->validationRules;


        if ($excludeKode !== null) {
            $rules['kode_layanan'] = 'required|is_unique[layanan.kode_layanan,kode_layanan,' . $excludeKode . ']';
        }

        return $rules;
    }

    protected function generateKodeLayanan(array $data)
    {
        if (empty($data['data']['kode_layanan'])) {
            $prefix = 'LYN';
            $date = date('Ymd');
            $lastLayanan = $this->orderBy('kode_layanan', 'DESC')->first();

            $number = 1;
            if ($lastLayanan) {
                $lastKode = $lastLayanan['kode_layanan'];
                if (preg_match('/[A-Z]+-[0-9]+-([0-9]+)/', $lastKode, $matches)) {
                    $number = (int)$matches[1] + 1;
                }
            }

            $data['data']['kode_layanan'] = $prefix . '-' . $date . '-' . sprintf('%03d', $number);
        }

        return $data;
    }

    public function getLayananByJenisKendaraan($jenisKendaraan)
    {
        return $this->where('jenis_kendaraan', $jenisKendaraan)
            ->where('status', 'aktif')
            ->findAll();
    }
}
