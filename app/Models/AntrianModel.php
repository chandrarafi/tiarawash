<?php

namespace App\Models;

use CodeIgniter\Model;

class AntrianModel extends Model
{
    protected $table            = 'antrian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_antrian',
        'booking_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'status',
        'karyawan_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'tanggal' => 'required|valid_date',
    ];

    protected $validationMessages = [
        'tanggal' => [
            'required' => 'Tanggal antrian harus diisi',
            'valid_date' => 'Format tanggal tidak valid',
        ],
    ];

    // Callbacks
    protected $beforeInsert = ['generateNomorAntrian'];
    protected $beforeUpdate = [];
    protected $afterInsert  = [];
    protected $afterUpdate  = [];

    protected function generateNomorAntrian(array $data)
    {
        if (empty($data['data']['nomor_antrian'])) {
            $tanggal = isset($data['data']['tanggal']) ? $data['data']['tanggal'] : date('Y-m-d');
            $formattedDate = date('Ymd', strtotime($tanggal));

            // Hitung jumlah antrian pada tanggal yang sama
            $count = $this->where('tanggal', $tanggal)->countAllResults();
            $number = $count + 1;

            $data['data']['nomor_antrian'] = 'A' . $formattedDate . sprintf('%03d', $number);
        }

        return $data;
    }

    public function getAntrianWithDetails($id = null)
    {
        $builder = $this->db->table('antrian a');
        $builder->select('a.*, b.kode_booking, b.pelanggan_id, b.no_plat, b.jenis_kendaraan, 
                          b.merk_kendaraan, p.nama_pelanggan, l.nama_layanan, l.harga, 
                          l.durasi_menit, k.namakaryawan');
        $builder->join('booking b', 'b.id = a.booking_id', 'left');
        $builder->join('pelanggan p', 'p.kode_pelanggan = b.pelanggan_id', 'left');
        $builder->join('layanan l', 'l.kode_layanan = b.layanan_id', 'left');
        $builder->join('karyawan k', 'k.idkaryawan = a.karyawan_id', 'left');

        if ($id !== null) {
            $builder->where('a.id', $id);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    public function getAntrianByDate($date)
    {
        $builder = $this->db->table('antrian a');
        $builder->select('a.*, b.kode_booking, b.pelanggan_id, b.no_plat, b.jenis_kendaraan, 
                          b.merk_kendaraan, p.nama_pelanggan, l.nama_layanan, k.namakaryawan');
        $builder->join('booking b', 'b.id = a.booking_id', 'left');
        $builder->join('pelanggan p', 'p.kode_pelanggan = b.pelanggan_id', 'left');
        $builder->join('layanan l', 'l.kode_layanan = b.layanan_id', 'left');
        $builder->join('karyawan k', 'k.idkaryawan = a.karyawan_id', 'left');
        $builder->where('a.tanggal', $date);
        $builder->orderBy('a.status', 'ASC');
        $builder->orderBy('a.nomor_antrian', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function getAntrianByKaryawan($karyawanId, $status = null)
    {
        $this->where('karyawan_id', $karyawanId);

        if ($status !== null) {
            $this->where('status', $status);
        }

        return $this->findAll();
    }

    public function updateStatus($id, $status, $karyawanId = null)
    {
        $data = ['status' => $status];

        if ($status == 'diproses' && $karyawanId) {
            $data['karyawan_id'] = $karyawanId;
            $data['jam_mulai'] = date('H:i:s');
        }

        if ($status == 'selesai') {
            $data['jam_selesai'] = date('H:i:s');
        }

        return $this->update($id, $data);
    }
}
