<?php

namespace App\Controllers;

use App\Models\AntrianModel;
use App\Models\BookingModel;
use App\Models\KaryawanModel;
use App\Models\TransaksiModel;
use App\Models\LayananModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Antrian extends BaseController
{
    protected $antrianModel;
    protected $bookingModel;
    protected $karyawanModel;
    protected $transaksiModel;
    protected $validation;

    public function __construct()
    {
        $this->antrianModel = new AntrianModel();
        $this->bookingModel = new BookingModel();
        $this->karyawanModel = new KaryawanModel();
        $this->transaksiModel = new TransaksiModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');

        $data = [
            'title' => 'Antrian Cucian',
            'tanggal' => $tanggal,
            'antrian' => $this->antrianModel->getAntrianByDate($tanggal)
        ];

        return view('admin/antrian/index', $data);
    }

    public function create()
    {
        // Untuk membuat antrian manual (walk-in customer)
        $data = [
            'title' => 'Tambah Antrian Baru',
            'booking' => $this->bookingModel->where('status', 'menunggu')
                ->where('tanggal', date('Y-m-d'))
                ->findAll(),
            'validation' => $this->validation
        ];

        return view('admin/antrian/create', $data);
    }

    public function store()
    {
        // Validasi input
        if (!$this->validate($this->antrianModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $bookingId = $this->request->getPost('booking_id');
        $tanggal = $this->request->getPost('tanggal') ?? date('Y-m-d');

        // Cek apakah booking sudah memiliki antrian
        if ($bookingId) {
            $existingAntrian = $this->antrianModel->where('booking_id', $bookingId)->first();
            if ($existingAntrian) {
                session()->setFlashdata('error', 'Booking ini sudah memiliki antrian');
                return redirect()->back()->withInput();
            }
        }

        // Simpan data
        $this->antrianModel->insert([
            'booking_id' => $bookingId,
            'tanggal' => $tanggal,
            'status' => 'menunggu'
        ]);

        session()->setFlashdata('success', 'Antrian berhasil ditambahkan');
        return redirect()->to('/admin/antrian');
    }

    public function show($id = null)
    {
        $antrian = $this->antrianModel->getAntrianWithDetails($id);

        if (!$antrian) {
            throw new PageNotFoundException('Antrian dengan ID ' . $id . ' tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Antrian',
            'antrian' => $antrian,
            'karyawan' => $this->karyawanModel->findAll()
        ];

        return view('admin/antrian/show', $data);
    }

    public function updateStatus($id = null)
    {
        $antrian = $this->antrianModel->find($id);

        if (!$antrian) {
            throw new PageNotFoundException('Antrian dengan ID ' . $id . ' tidak ditemukan');
        }

        $status = $this->request->getPost('status');
        $karyawanId = $this->request->getPost('karyawan_id');

        // Update status antrian
        $this->antrianModel->updateStatus($id, $status, $karyawanId);

        // Update status booking jika ada
        if ($antrian['booking_id']) {
            $this->bookingModel->update($antrian['booking_id'], ['status' => $status]);
        }

        // Jika status selesai, buat transaksi otomatis
        if ($status == 'selesai' && $antrian['booking_id']) {
            $booking = $this->bookingModel->find($antrian['booking_id']);
            $layananModel = new LayananModel();
            $layanan = $layananModel->find($booking['layanan_id']);

            // Buat transaksi baru
            $this->transaksiModel->insert([
                'tanggal' => date('Y-m-d'),
                'booking_id' => $booking['id'],
                'pelanggan_id' => $booking['pelanggan_id'],
                'layanan_id' => $booking['layanan_id'],
                'no_plat' => $booking['no_plat'],
                'jenis_kendaraan' => $booking['jenis_kendaraan'],
                'total_harga' => $layanan['harga'],
                'metode_pembayaran' => 'tunai',
                'status_pembayaran' => 'belum_bayar',
                'user_id' => session()->get('user_id')
            ]);
        }

        session()->setFlashdata('success', 'Status antrian berhasil diperbarui');
        return redirect()->to('/admin/antrian');
    }

    public function assignKaryawan($id = null)
    {
        $antrian = $this->antrianModel->find($id);

        if (!$antrian) {
            throw new PageNotFoundException('Antrian dengan ID ' . $id . ' tidak ditemukan');
        }

        $karyawanId = $this->request->getPost('karyawan_id');

        // Update karyawan yang ditugaskan
        $this->antrianModel->update($id, [
            'karyawan_id' => $karyawanId,
            'status' => 'diproses',
            'jam_mulai' => date('H:i:s')
        ]);

        // Update status booking jika ada
        if ($antrian['booking_id']) {
            $this->bookingModel->update($antrian['booking_id'], ['status' => 'diproses']);
        }

        session()->setFlashdata('success', 'Karyawan berhasil ditugaskan');
        return redirect()->to('/admin/antrian');
    }
}
