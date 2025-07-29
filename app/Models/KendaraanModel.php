<?php

namespace App\Models;

use CodeIgniter\Model;

class KendaraanModel extends Model
{
    protected $table            = 'kendaraan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pelanggan_id',
        'no_plat',
        'jenis_kendaraan',
        'merk',
        'model',
        'warna',
        'tahun',
        'catatan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'pelanggan_id'    => 'required|is_not_unique[pelanggan.kode_pelanggan]',
        'no_plat'         => 'required',
        'jenis_kendaraan' => 'required|in_list[motor,mobil,lainnya]',
    ];

    protected $validationMessages = [
        'pelanggan_id' => [
            'required' => 'ID pelanggan harus diisi',
            'is_not_unique' => 'Pelanggan tidak ditemukan',
        ],
        'no_plat' => [
            'required' => 'Nomor plat kendaraan harus diisi',
        ],
        'jenis_kendaraan' => [
            'required' => 'Jenis kendaraan harus diisi',
            'in_list' => 'Jenis kendaraan tidak valid',
        ],
    ];

    public function getKendaraanByPelanggan($pelangganId)
    {
        return $this->where('pelanggan_id', $pelangganId)->findAll();
    }

    public function getKendaraanByPlat($noPlat)
    {
        return $this->where('no_plat', $noPlat)->first();
    }

    public function checkKendaraanExists($pelangganId, $noPlat)
    {
        return $this->where('pelanggan_id', $pelangganId)
            ->where('no_plat', $noPlat)
            ->countAllResults() > 0;
    }

    public function getKendaraanWithPelanggan($id = null)
    {
        $builder = $this->db->table('kendaraan k');
        $builder->select('k.*, p.nama_pelanggan');
        $builder->join('pelanggan p', 'p.kode_pelanggan = k.pelanggan_id');

        if ($id !== null) {
            $builder->where('k.id', $id);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    public function searchKendaraan($keyword)
    {
        return $this->like('no_plat', $keyword)
            ->orLike('merk', $keyword)
            ->orLike('model', $keyword)
            ->orLike('warna', $keyword)
            ->findAll();
    }
}
