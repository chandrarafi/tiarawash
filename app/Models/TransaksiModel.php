<?php

namespace App\Models;

use CodeIgniter\Model;

class TransaksiModel extends Model
{
    protected $table            = 'transaksi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'no_transaksi',
        'tanggal',
        'booking_id',
        'pelanggan_id',
        'layanan_id',
        'no_plat',
        'jenis_kendaraan',
        'total_harga',
        'metode_pembayaran',
        'status_pembayaran',
        'catatan',
        'user_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'tanggal'          => 'required|valid_date',
        'no_plat'          => 'required',
        'jenis_kendaraan'  => 'required|in_list[motor,mobil,lainnya]',
        'layanan_id'       => 'required|numeric|is_not_unique[layanan.id]',
        'metode_pembayaran' => 'required|in_list[tunai,kartu_kredit,kartu_debit,e-wallet,transfer]',
    ];

    protected $validationMessages = [
        'tanggal' => [
            'required' => 'Tanggal transaksi harus diisi',
            'valid_date' => 'Format tanggal tidak valid',
        ],
        'no_plat' => [
            'required' => 'Nomor plat kendaraan harus diisi',
        ],
        'jenis_kendaraan' => [
            'required' => 'Jenis kendaraan harus diisi',
            'in_list' => 'Jenis kendaraan tidak valid',
        ],
        'layanan_id' => [
            'required' => 'Layanan harus dipilih',
            'numeric' => 'ID layanan tidak valid',
            'is_not_unique' => 'Layanan tidak ditemukan',
        ],
        'metode_pembayaran' => [
            'required' => 'Metode pembayaran harus dipilih',
            'in_list' => 'Metode pembayaran tidak valid',
        ],
    ];

    // Callbacks
    protected $beforeInsert = ['generateNoTransaksi'];
    protected $beforeUpdate = [];
    protected $afterInsert  = [];
    protected $afterUpdate  = [];

    protected function generateNoTransaksi(array $data)
    {
        if (empty($data['data']['no_transaksi'])) {
            $prefix = 'TRX';
            $date = date('Ymd');
            $lastTransaksi = $this->orderBy('id', 'DESC')->first();

            $number = 1;
            if ($lastTransaksi) {
                $lastKode = $lastTransaksi['no_transaksi'];
                if (preg_match('/[A-Z]+-[0-9]+-([0-9]+)/', $lastKode, $matches)) {
                    $number = (int)$matches[1] + 1;
                }
            }

            $data['data']['no_transaksi'] = $prefix . '-' . $date . '-' . sprintf('%03d', $number);
        }

        return $data;
    }

    public function getTransaksiWithDetails($id = null)
    {
        $builder = $this->db->table('transaksi t');
        $builder->select('t.*, p.nama_pelanggan, l.nama_layanan, u.name as nama_kasir');
        $builder->join('pelanggan p', 'p.kode_pelanggan = t.pelanggan_id', 'left');
        $builder->join('layanan l', 'l.id = t.layanan_id', 'left');
        $builder->join('users u', 'u.id = t.user_id', 'left');

        if ($id !== null) {
            $builder->where('t.id', $id);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    public function getTransaksiByPelanggan($pelangganId)
    {
        return $this->where('pelanggan_id', $pelangganId)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getTransaksiByDateRange($startDate, $endDate)
    {
        return $this->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    public function getLaporanHarian($tanggal)
    {
        $builder = $this->db->table('transaksi');
        $builder->select('COUNT(*) as jumlah_transaksi, SUM(total_harga) as total_pendapatan');
        $builder->where('tanggal', $tanggal);
        $builder->where('status_pembayaran', 'dibayar');

        return $builder->get()->getRowArray();
    }

    public function getLaporanBulanan($bulan, $tahun)
    {
        $builder = $this->db->table('transaksi');
        $builder->select('DATE(tanggal) as tanggal, COUNT(*) as jumlah_transaksi, SUM(total_harga) as total_pendapatan');
        $builder->where('MONTH(tanggal)', $bulan);
        $builder->where('YEAR(tanggal)', $tahun);
        $builder->where('status_pembayaran', 'dibayar');
        $builder->groupBy('tanggal');
        $builder->orderBy('tanggal', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function getStatistikLayanan($startDate, $endDate)
    {
        $builder = $this->db->table('transaksi t');
        $builder->select('l.nama_layanan, COUNT(*) as jumlah_transaksi, SUM(t.total_harga) as total_pendapatan');
        $builder->join('layanan l', 'l.id = t.layanan_id');
        $builder->where('t.tanggal >=', $startDate);
        $builder->where('t.tanggal <=', $endDate);
        $builder->where('t.status_pembayaran', 'dibayar');
        $builder->groupBy('t.layanan_id');
        $builder->orderBy('jumlah_transaksi', 'DESC');

        return $builder->get()->getResultArray();
    }
}
