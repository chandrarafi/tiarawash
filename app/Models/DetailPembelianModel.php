<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPembelianModel extends Model
{
    protected $table            = 'detail_pembelian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['no_faktur', 'perlengkapan_id', 'jumlah', 'harga_satuan', 'subtotal'];


    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    protected $validationRules = [
        'no_faktur'    => 'required|max_length[20]',
        'perlengkapan_id' => 'required|integer',
        'jumlah'          => 'required|integer|greater_than[0]',
        'harga_satuan'    => 'required|numeric|greater_than[0]',
        'subtotal'        => 'required|numeric|greater_than[0]',
    ];

    protected $validationMessages = [
        'no_faktur' => [
            'required' => 'Nomor faktur harus diisi',
            'max_length' => 'Nomor faktur maksimal 20 karakter',
        ],
        'perlengkapan_id' => [
            'required' => 'ID perlengkapan harus diisi',
            'integer'  => 'ID perlengkapan harus berupa angka',
        ],
        'jumlah' => [
            'required'       => 'Jumlah harus diisi',
            'integer'        => 'Jumlah harus berupa angka bulat',
            'greater_than'   => 'Jumlah harus lebih dari 0',
        ],
        'harga_satuan' => [
            'required'       => 'Harga satuan harus diisi',
            'numeric'        => 'Harga satuan harus berupa angka',
            'greater_than'   => 'Harga satuan harus lebih dari 0',
        ],
        'subtotal' => [
            'required'       => 'Subtotal harus diisi',
            'numeric'        => 'Subtotal harus berupa angka',
            'greater_than'   => 'Subtotal harus lebih dari 0',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Mendapatkan detail pembelian dengan informasi perlengkapan
     */
    public function getDetailWithPerlengkapan($pembelianId)
    {
        return $this->select('detail_pembelian.*, perlengkapan.nama as nama_perlengkapan, perlengkapan.kategori')
            ->join('perlengkapan', 'perlengkapan.id = detail_pembelian.perlengkapan_id')
            ->where('no_faktur', $pembelianId)
            ->findAll();
    }

    /**
     * Menghitung total pembelian
     */
    public function calculateTotal($pembelianId)
    {
        $result = $this->selectSum('subtotal')
            ->where('no_faktur', $pembelianId)
            ->get()
            ->getRowArray();

        return $result['subtotal'] ?? 0;
    }

    /**
     * Memperbarui stok perlengkapan setelah pembelian
     */
    public function updateStokPerlengkapan($detailId)
    {
        try {
            log_message('debug', 'updateStokPerlengkapan called with detailId: ' . $detailId);

            $detail = $this->find($detailId);
            if (!$detail) {
                log_message('error', 'Detail pembelian tidak ditemukan dengan ID: ' . $detailId);
                return false;
            }

            log_message('debug', 'Detail found: ' . json_encode($detail));

            $perlengkapanModel = new \App\Models\PerlengkapanModel();
            $perlengkapan = $perlengkapanModel->find($detail['perlengkapan_id']);
            if (!$perlengkapan) {
                log_message('error', 'Perlengkapan tidak ditemukan dengan ID: ' . $detail['perlengkapan_id']);
                return false;
            }

            log_message('debug', 'Perlengkapan found: ' . json_encode($perlengkapan));

            $newStok = $perlengkapan['stok'] + $detail['jumlah'];
            log_message('debug', 'Updating stok from ' . $perlengkapan['stok'] . ' to ' . $newStok);

            $result = $perlengkapanModel->update($detail['perlengkapan_id'], ['stok' => $newStok]);
            if ($result) {
                log_message('debug', 'Stok berhasil diupdate untuk perlengkapan ID: ' . $detail['perlengkapan_id']);
                return true;
            } else {
                log_message('error', 'Gagal update stok untuk perlengkapan ID: ' . $detail['perlengkapan_id']);
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in updateStokPerlengkapan: ' . $e->getMessage());
            return false;
        }
    }
}
