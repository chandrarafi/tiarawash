<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailTransaksiModel extends Model
{
    protected $table            = 'detail_transaksi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'transaksi_id',
        'jenis_item',
        'item_id',
        'nama_item',
        'harga',
        'jumlah',
        'subtotal'
    ];


    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    protected $validationRules = [
        'transaksi_id' => 'required|numeric|is_not_unique[transaksi.id]',
        'jenis_item'   => 'required|in_list[layanan,produk]',
        'item_id'      => 'required|max_length[50]', // Changed to allow string (kode_layanan)
        'nama_item'    => 'required|max_length[100]',
        'harga'        => 'required|decimal',
        'jumlah'       => 'required|integer|greater_than[0]',
        'subtotal'     => 'required|decimal',
    ];

    protected $validationMessages = [
        'transaksi_id' => [
            'required' => 'ID transaksi harus diisi',
            'numeric' => 'ID transaksi tidak valid',
            'is_not_unique' => 'Transaksi tidak ditemukan',
        ],
        'jenis_item' => [
            'required' => 'Jenis item harus diisi',
            'in_list' => 'Jenis item tidak valid',
        ],
        'item_id' => [
            'required' => 'ID item harus diisi',
            'numeric' => 'ID item tidak valid',
        ],
        'nama_item' => [
            'required' => 'Nama item harus diisi',
        ],
        'harga' => [
            'required' => 'Harga harus diisi',
            'numeric' => 'Harga harus berupa angka',
        ],
        'jumlah' => [
            'required' => 'Jumlah harus diisi',
            'integer' => 'Jumlah harus berupa angka',
        ],
        'subtotal' => [
            'required' => 'Subtotal harus diisi',
            'numeric' => 'Subtotal harus berupa angka',
        ],
    ];

    public function getDetailByTransaksi($transaksiId)
    {
        return $this->where('transaksi_id', $transaksiId)->findAll();
    }

    public function calculateSubtotal($harga, $jumlah)
    {
        return $harga * $jumlah;
    }

    public function addDetailTransaksi($transaksiId, $jenisItem, $itemId, $namaItem, $harga, $jumlah)
    {
        $subtotal = $this->calculateSubtotal($harga, $jumlah);

        $data = [
            'transaksi_id' => $transaksiId,
            'jenis_item'   => $jenisItem,
            'item_id'      => $itemId,
            'nama_item'    => $namaItem,
            'harga'        => $harga,
            'jumlah'       => $jumlah,
            'subtotal'     => $subtotal
        ];

        return $this->insert($data);
    }
}
