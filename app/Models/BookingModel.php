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

        'merk_kendaraan',
        'layanan_id',
        'status',
        'payment_expires_at',
        'catatan',
        'user_id',
        'id_karyawan'
    ];


    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    protected $validationRules      = [
        'kode_booking'    => 'permit_empty|max_length[20]',
        'pelanggan_id'    => 'required|max_length[10]',
        'tanggal'         => 'required|valid_date',
        'jam'             => 'required',
        'no_plat'         => 'required|max_length[20]',

        'merk_kendaraan'  => 'permit_empty|max_length[50]',
        'layanan_id'      => 'required|is_not_unique[layanan.kode_layanan]',
        'catatan'         => 'permit_empty',
        'status'          => 'permit_empty|in_list[menunggu_konfirmasi,dikonfirmasi,selesai,dibatalkan,batal]',
        'id_karyawan'     => 'permit_empty|max_length[20]|is_not_unique[karyawan.idkaryawan]'
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

        'layanan_id' => [
            'required' => 'Layanan harus dipilih',
            'numeric' => 'ID layanan tidak valid',
            'is_not_unique' => 'Layanan tidak ditemukan',
        ],
    ];


    protected $beforeInsert = ['generateKodeBooking'];
    protected $beforeUpdate = [];
    protected $afterInsert  = [];
    protected $afterUpdate  = [];

    protected function generateKodeBooking(array $data)
    {

        if (empty($data['data']['kode_booking'])) {
            $data['data']['kode_booking'] = $this->generateNewKodeBooking();
        }

        return $data;
    }

    public function generateNewKodeBooking()
    {
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

        return $prefix . '-' . $date . '-' . sprintf('%03d', $number);
    }

    public function getBookingWithDetails($id = null)
    {
        $builder = $this->db->table('booking b');
        $builder->select('b.*, p.nama_pelanggan, l.nama_layanan, l.harga, l.durasi_menit, l.jenis_kendaraan');
        $builder->join('pelanggan p', 'p.kode_pelanggan = b.pelanggan_id', 'left');
        $builder->join('layanan l', 'l.kode_layanan = b.layanan_id', 'left');

        if ($id !== null) {
            $builder->where('b.id', $id);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    public function getBookingsByPelanggan($pelangganId)
    {
        $builder = $this->db->table('booking b');
        $builder->select('b.*, p.nama_pelanggan, l.nama_layanan, l.harga, l.durasi_menit, l.jenis_kendaraan');
        $builder->join('pelanggan p', 'p.kode_pelanggan = b.pelanggan_id', 'left');
        $builder->join('layanan l', 'l.kode_layanan = b.layanan_id', 'left');
        $builder->where('b.pelanggan_id', $pelangganId);
        $builder->orderBy('b.tanggal', 'DESC');
        $builder->orderBy('b.jam', 'DESC');

        return $builder->get()->getResultArray();
    }

    public function getBookingsByDate($date)
    {
        return $this->where('tanggal', $date)
            ->orderBy('jam', 'ASC')
            ->findAll();
    }

    public function getBookingsByKodeBooking($kodeBooking)
    {
        $builder = $this->db->table('booking b');
        $builder->select('b.*, p.nama_pelanggan, l.nama_layanan, l.harga, l.durasi_menit, l.jenis_kendaraan');
        $builder->join('pelanggan p', 'p.kode_pelanggan = b.pelanggan_id', 'left');
        $builder->join('layanan l', 'l.kode_layanan = b.layanan_id', 'left');
        $builder->where('b.kode_booking', $kodeBooking);
        $builder->orderBy('b.jam', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Check if a time slot is available for booking
     */
    public function checkSlotAvailability($tanggal, $jam, $jenisKendaraan = null)
    {

        $builder = $this->db->table('booking b')
            ->join('layanan l', 'l.kode_layanan = b.layanan_id', 'left')
            ->where('b.tanggal', $tanggal)
            ->where('b.jam', $jam)
            ->where('b.status !=', 'dibatalkan');


        if ($jenisKendaraan) {
            $builder->where('l.jenis_kendaraan', $jenisKendaraan);
        }

        $existingBooking = $builder->get()->getRowArray();


        return $existingBooking === null;
    }

    /**
     * Get available karyawan for specific date and time
     */
    public function getAvailableKaryawan($tanggal, $jam, $durasi = 60)
    {

        list($hours, $minutes) = explode(':', $jam);
        $startTimeMinutes = ($hours * 60) + $minutes;
        $endTimeMinutes = $startTimeMinutes + $durasi;
        $endTime = sprintf('%02d:%02d', floor($endTimeMinutes / 60), $endTimeMinutes % 60);


        $karyawanModel = new \App\Models\KaryawanModel();
        $allKaryawan = $karyawanModel->findAll();


        $busyKaryawan = $this->db->table('booking b')
            ->select('b.id_karyawan')
            ->join('layanan l', 'l.kode_layanan = b.layanan_id', 'left')
            ->where('b.tanggal', $tanggal)
            ->where('b.status !=', 'batal')
            ->where('b.id_karyawan IS NOT NULL')
            ->groupStart()
            ->where("b.jam < '$endTime'")
            ->where("ADDTIME(b.jam, SEC_TO_TIME(COALESCE(l.durasi_menit, 60) * 60)) > '$jam'")
            ->groupEnd()
            ->get()
            ->getResultArray();

        $busyKaryawanIds = array_column($busyKaryawan, 'id_karyawan');


        $availableKaryawan = array_filter($allKaryawan, function ($karyawan) use ($busyKaryawanIds) {
            return !in_array($karyawan['idkaryawan'], $busyKaryawanIds);
        });

        return array_values($availableKaryawan);
    }

    /**
     * Get random available karyawan
     */
    public function getRandomAvailableKaryawan($tanggal, $jam, $durasi = 60)
    {
        $availableKaryawan = $this->getAvailableKaryawan($tanggal, $jam, $durasi);

        if (empty($availableKaryawan)) {
            return null;
        }


        $randomIndex = array_rand($availableKaryawan);
        return $availableKaryawan[$randomIndex];
    }

    /**
     * Check slot availability based on karyawan availability
     */
    public function checkSlotAvailabilityWithKaryawan($tanggal, $jam, $durasi = 60)
    {
        $availableKaryawan = $this->getAvailableKaryawan($tanggal, $jam, $durasi);
        return count($availableKaryawan) > 0;
    }

    /**
     * Auto-cancel expired bookings
     */
    public function cancelExpiredBookings()
    {
        $expiredBookings = $this->where('status', 'menunggu_konfirmasi')
            ->where('payment_expires_at <', date('Y-m-d H:i:s'))
            ->where('payment_expires_at IS NOT NULL')
            ->findAll();

        $canceledCount = 0;
        foreach ($expiredBookings as $booking) {
            if ($this->update($booking['id'], ['status' => 'batal'])) {
                $canceledCount++;
                log_message('info', "Auto-canceled expired booking: {$booking['kode_booking']}");
            }
        }

        return $canceledCount;
    }

    /**
     * Get booking payment status and time remaining
     */
    public function getPaymentInfo($kodeBooking)
    {
        $bookings = $this->where('kode_booking', $kodeBooking)->findAll();

        if (empty($bookings)) {
            return null;
        }

        $firstBooking = $bookings[0];
        $now = new \DateTime();
        $expiresAt = new \DateTime($firstBooking['payment_expires_at']);

        $timeRemaining = $expiresAt->getTimestamp() - $now->getTimestamp();
        $isExpired = $timeRemaining <= 0;

        return [
            'status' => $firstBooking['status'],
            'expires_at' => $firstBooking['payment_expires_at'],
            'time_remaining' => max(0, $timeRemaining),
            'is_expired' => $isExpired,
            'total_services' => count($bookings)
        ];
    }
}
