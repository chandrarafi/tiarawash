<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table            = 'booking';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_booking',
        'pelanggan_id',
        'tanggal',
        'jam',
        'no_plat',
        'jenis_kendaraan',
        'merk_kendaraan',
        'layanan_id',
        'status',
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
        'tanggal'         => 'required|valid_date',
        'jam'             => 'required',
        'no_plat'         => 'required',
        'jenis_kendaraan' => 'required|in_list[motor,mobil,lainnya]',
        'layanan_id'      => 'required|numeric|is_not_unique[layanan.id]',
    ];

    protected $validationMessages = [
        'tanggal' => [
            'required' => 'Tanggal booking harus diisi',
            'valid_date' => 'Format tanggal tidak valid',
        ],
        'jam' => [
            'required' => 'Jam booking harus diisi',
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
    ];

    // Callbacks
    protected $beforeInsert = ['generateKodeBooking'];
    protected $beforeUpdate = [];
    protected $afterInsert  = [];
    protected $afterUpdate  = [];

    protected function generateKodeBooking(array $data)
    {
        if (empty($data['data']['kode_booking'])) {
            $prefix = 'BK';
            $date = date('Ymd');
            $lastBooking = $this->orderBy('id', 'DESC')->first();

            $number = 1;
            if ($lastBooking) {
                $lastKode = $lastBooking['kode_booking'];
                if (preg_match('/[A-Z]+-[0-9]+-([0-9]+)/', $lastKode, $matches)) {
                    $number = (int)$matches[1] + 1;
                }
            }

            $data['data']['kode_booking'] = $prefix . '-' . $date . '-' . sprintf('%03d', $number);
        }

        return $data;
    }

    public function getBookingWithDetails($id = null)
    {
        $builder = $this->db->table('booking b');
        $builder->select('b.*, p.nama_pelanggan, l.nama_layanan, l.harga, l.durasi_menit');
        $builder->join('pelanggan p', 'p.kode_pelanggan = b.pelanggan_id', 'left');
        $builder->join('layanan l', 'l.id = b.layanan_id');

        if ($id !== null) {
            $builder->where('b.id', $id);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    public function getBookingsByPelanggan($pelangganId)
    {
        return $this->where('pelanggan_id', $pelangganId)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('jam', 'DESC')
            ->findAll();
    }

    public function getBookingsByDate($date)
    {
        return $this->where('tanggal', $date)
            ->orderBy('jam', 'ASC')
            ->findAll();
    }

    public function checkSlotAvailability($tanggal, $jam, $jenisKendaraan)
    {
        // Mendapatkan jumlah booking pada jam yang sama
        $count = $this->where('tanggal', $tanggal)
            ->where('jam', $jam)
            ->where('jenis_kendaraan', $jenisKendaraan)
            ->where('status !=', 'batal')
            ->countAllResults();

        // Batasan jumlah slot berdasarkan jenis kendaraan
        $maxSlot = ($jenisKendaraan == 'motor') ? 3 : 2;

        return $count < $maxSlot;
    }
}
