<?php

namespace App\Models;

use CodeIgniter\Model;

class PembelianModel extends Model
{
    protected $table            = 'pembelian';
    protected $primaryKey       = 'no_faktur';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['no_faktur', 'tanggal', 'supplier', 'total_harga', 'keterangan', 'user_id'];


    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    protected $validationRules = [
        'no_faktur'    => 'required|min_length[3]|max_length[20]|is_unique[pembelian.no_faktur]',
        'tanggal'      => 'required|valid_date',
        'supplier'     => 'required|min_length[3]|max_length[100]',
        'total_harga'  => 'permit_empty|numeric',
    ];

    protected $validationMessages = [
        'no_faktur' => [
            'required'    => 'Nomor faktur harus diisi',
            'min_length'  => 'Nomor faktur minimal 3 karakter',
            'max_length'  => 'Nomor faktur maksimal 20 karakter',
            'is_unique'   => 'Nomor faktur sudah ada dalam database',
        ],
        'tanggal' => [
            'required'    => 'Tanggal harus diisi',
            'valid_date'  => 'Format tanggal tidak valid',
        ],
        'supplier' => [
            'required'    => 'Nama supplier harus diisi',
            'min_length'  => 'Nama supplier minimal 3 karakter',
            'max_length'  => 'Nama supplier maksimal 100 karakter',
        ],
        'total_harga' => [
            'required'    => 'Total harga harus diisi',
            'numeric'     => 'Total harga harus berupa angka',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;


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
     * Generate nomor faktur otomatis
     * Format: PBL-YYYYMMDD-XXXX (XXXX adalah nomor urut)
     */
    public function generateNoFaktur()
    {
        $date = date('Ymd');
        $prefix = "PBL-{$date}-";

        $lastPembelian = $this->select('no_faktur')
            ->like('no_faktur', $prefix, 'after')
            ->orderBy('no_faktur', 'DESC')
            ->first();

        if ($lastPembelian) {
            $lastNumber = substr($lastPembelian['no_faktur'], -4);
            $nextNumber = str_pad((int)$lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }

    /**
     * Get validation rules for edit (exclude unique check for current record)
     */
    public function getValidationRulesForEdit($currentNoFaktur = null)
    {
        $rules = $this->validationRules;
        if ($currentNoFaktur !== null) {
            $rules['no_faktur'] = 'required|min_length[3]|max_length[20]|is_unique[pembelian.no_faktur,no_faktur,' . $currentNoFaktur . ']';
        }
        return $rules;
    }

    /**
     * Mendapatkan data pembelian dengan detail supplier dan user
     */
    public function getPembelianWithDetails($noFaktur = null)
    {
        $builder = $this->db->table('pembelian p')
            ->select('p.*, u.name as user_name')
            ->join('users u', 'u.id = p.user_id', 'left');

        if ($noFaktur !== null) {
            return $builder->where('p.no_faktur', $noFaktur)->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Mendapatkan laporan pembelian berdasarkan periode
     */
    public function getLaporanPembelian($startDate, $endDate)
    {
        return $this->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->orderBy('tanggal', 'DESC')
            ->findAll();
    }
}
