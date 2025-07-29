<?php

namespace App\Controllers;

use App\Models\BookingModel;
use App\Models\LayananModel;
use App\Models\PelangganModel;
use App\Models\KendaraanModel;
use App\Models\AntrianModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Booking extends BaseController
{
    protected $bookingModel;
    protected $layananModel;
    protected $pelangganModel;
    protected $kendaraanModel;
    protected $antrianModel;
    protected $validation;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->layananModel = new LayananModel();
        $this->pelangganModel = new PelangganModel();
        $this->kendaraanModel = new KendaraanModel();
        $this->antrianModel = new AntrianModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $data = [
            'title' => 'Daftar Booking',
            'booking' => $this->bookingModel->getBookingWithDetails()
        ];

        return view('admin/booking/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Booking Baru',
            'pelanggan' => $this->pelangganModel->findAll(),
            'layanan' => $this->layananModel->where('status', 'aktif')->findAll(),
            'validation' => $this->validation
        ];

        return view('admin/booking/create', $data);
    }

    public function store()
    {
        // Validasi input
        if (!$this->validate($this->bookingModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Cek ketersediaan slot
        $tanggal = $this->request->getPost('tanggal');
        $jam = $this->request->getPost('jam');
        $jenisKendaraan = $this->request->getPost('jenis_kendaraan');

        if (!$this->bookingModel->checkSlotAvailability($tanggal, $jam, $jenisKendaraan)) {
            session()->setFlashdata('error', 'Slot untuk waktu tersebut sudah penuh. Silakan pilih waktu lain.');
            return redirect()->back()->withInput();
        }

        // Simpan data
        $userId = session()->get('user_id');
        $bookingId = $this->bookingModel->insert([
            'pelanggan_id' => $this->request->getPost('pelanggan_id'),
            'tanggal' => $tanggal,
            'jam' => $jam,
            'no_plat' => $this->request->getPost('no_plat'),
            'jenis_kendaraan' => $jenisKendaraan,
            'merk_kendaraan' => $this->request->getPost('merk_kendaraan'),
            'layanan_id' => $this->request->getPost('layanan_id'),
            'status' => 'menunggu',
            'catatan' => $this->request->getPost('catatan'),
            'user_id' => $userId
        ]);

        // Buat antrian otomatis jika booking untuk hari ini
        $today = date('Y-m-d');
        if ($tanggal == $today) {
            $this->antrianModel->insert([
                'booking_id' => $bookingId,
                'tanggal' => $tanggal,
                'status' => 'menunggu'
            ]);
        }

        session()->setFlashdata('success', 'Booking berhasil ditambahkan');
        return redirect()->to('/admin/booking');
    }

    public function show($id = null)
    {
        $booking = $this->bookingModel->getBookingWithDetails($id);

        if (!$booking) {
            throw new PageNotFoundException('Booking dengan ID ' . $id . ' tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Booking',
            'booking' => $booking
        ];

        return view('admin/booking/show', $data);
    }

    public function edit($id = null)
    {
        $booking = $this->bookingModel->find($id);

        if (!$booking) {
            throw new PageNotFoundException('Booking dengan ID ' . $id . ' tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Booking',
            'booking' => $booking,
            'pelanggan' => $this->pelangganModel->findAll(),
            'layanan' => $this->layananModel->where('status', 'aktif')->findAll(),
            'validation' => $this->validation
        ];

        return view('admin/booking/edit', $data);
    }

    public function update($id = null)
    {
        // Validasi input
        if (!$this->validate($this->bookingModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Update data
        $this->bookingModel->update($id, [
            'pelanggan_id' => $this->request->getPost('pelanggan_id'),
            'tanggal' => $this->request->getPost('tanggal'),
            'jam' => $this->request->getPost('jam'),
            'no_plat' => $this->request->getPost('no_plat'),
            'jenis_kendaraan' => $this->request->getPost('jenis_kendaraan'),
            'merk_kendaraan' => $this->request->getPost('merk_kendaraan'),
            'layanan_id' => $this->request->getPost('layanan_id'),
            'status' => $this->request->getPost('status'),
            'catatan' => $this->request->getPost('catatan')
        ]);

        session()->setFlashdata('success', 'Booking berhasil diperbarui');
        return redirect()->to('/admin/booking');
    }

    public function cancel($id = null)
    {
        $booking = $this->bookingModel->find($id);

        if (!$booking) {
            throw new PageNotFoundException('Booking dengan ID ' . $id . ' tidak ditemukan');
        }

        // Update status booking menjadi batal
        $this->bookingModel->update($id, ['status' => 'batal']);

        // Update status antrian jika ada
        $antrian = $this->antrianModel->where('booking_id', $id)->first();
        if ($antrian) {
            $this->antrianModel->update($antrian['id'], ['status' => 'batal']);
        }

        session()->setFlashdata('success', 'Booking berhasil dibatalkan');
        return redirect()->to('/admin/booking');
    }

    // Untuk pelanggan
    public function createBooking()
    {
        $userId = session()->get('user_id');
        $pelanggan = $this->pelangganModel->where('user_id', $userId)->first();

        if (!$pelanggan) {
            session()->setFlashdata('error', 'Data pelanggan tidak ditemukan');
            return redirect()->to('/pelanggan/profile');
        }

        $kendaraan = $this->kendaraanModel->getKendaraanByPelanggan($pelanggan['kode_pelanggan']);

        $data = [
            'title' => 'Buat Booking Baru',
            'pelanggan' => $pelanggan,
            'kendaraan' => $kendaraan,
            'layanan' => $this->layananModel->where('status', 'aktif')->findAll(),
            'validation' => $this->validation
        ];

        return view('pelanggan/booking/create', $data);
    }

    public function storeBooking()
    {
        // Validasi input
        if (!$this->validate($this->bookingModel->getValidationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userId = session()->get('user_id');
        $pelanggan = $this->pelangganModel->where('user_id', $userId)->first();

        if (!$pelanggan) {
            session()->setFlashdata('error', 'Data pelanggan tidak ditemukan');
            return redirect()->to('/pelanggan/profile');
        }

        // Cek ketersediaan slot
        $tanggal = $this->request->getPost('tanggal');
        $jam = $this->request->getPost('jam');
        $jenisKendaraan = $this->request->getPost('jenis_kendaraan');

        if (!$this->bookingModel->checkSlotAvailability($tanggal, $jam, $jenisKendaraan)) {
            session()->setFlashdata('error', 'Slot untuk waktu tersebut sudah penuh. Silakan pilih waktu lain.');
            return redirect()->back()->withInput();
        }

        // Simpan kendaraan baru jika belum ada
        $noPlat = $this->request->getPost('no_plat');
        $kendaraan = $this->kendaraanModel->where('pelanggan_id', $pelanggan['kode_pelanggan'])
            ->where('no_plat', $noPlat)
            ->first();

        if (!$kendaraan && $this->request->getPost('save_kendaraan') == '1') {
            $this->kendaraanModel->insert([
                'pelanggan_id' => $pelanggan['kode_pelanggan'],
                'no_plat' => $noPlat,
                'jenis_kendaraan' => $jenisKendaraan,
                'merk' => $this->request->getPost('merk'),
                'model' => $this->request->getPost('model'),
                'warna' => $this->request->getPost('warna'),
                'tahun' => $this->request->getPost('tahun'),
                'catatan' => $this->request->getPost('catatan_kendaraan')
            ]);
        }

        // Simpan data booking
        $bookingId = $this->bookingModel->insert([
            'pelanggan_id' => $pelanggan['kode_pelanggan'],
            'tanggal' => $tanggal,
            'jam' => $jam,
            'no_plat' => $noPlat,
            'jenis_kendaraan' => $jenisKendaraan,
            'merk_kendaraan' => $this->request->getPost('merk'),
            'layanan_id' => $this->request->getPost('layanan_id'),
            'status' => 'menunggu',
            'catatan' => $this->request->getPost('catatan'),
            'user_id' => $userId
        ]);

        // Buat antrian otomatis jika booking untuk hari ini
        $today = date('Y-m-d');
        if ($tanggal == $today) {
            $this->antrianModel->insert([
                'booking_id' => $bookingId,
                'tanggal' => $tanggal,
                'status' => 'menunggu'
            ]);
        }

        session()->setFlashdata('success', 'Booking berhasil dibuat. Kode booking Anda: ' . $this->bookingModel->find($bookingId)['kode_booking']);
        return redirect()->to('/pelanggan/booking/history');
    }

    public function history()
    {
        $userId = session()->get('user_id');
        $pelanggan = $this->pelangganModel->where('user_id', $userId)->first();

        if (!$pelanggan) {
            session()->setFlashdata('error', 'Data pelanggan tidak ditemukan');
            return redirect()->to('/pelanggan/profile');
        }

        $bookings = $this->bookingModel->getBookingsByPelanggan($pelanggan['kode_pelanggan']);

        $data = [
            'title' => 'Riwayat Booking',
            'bookings' => $bookings
        ];

        return view('pelanggan/booking/history', $data);
    }

    public function detail($id = null)
    {
        $userId = session()->get('user_id');
        $pelanggan = $this->pelangganModel->where('user_id', $userId)->first();

        if (!$pelanggan) {
            session()->setFlashdata('error', 'Data pelanggan tidak ditemukan');
            return redirect()->to('/pelanggan/profile');
        }

        $booking = $this->bookingModel->getBookingWithDetails($id);

        if (!$booking || $booking['pelanggan_id'] != $pelanggan['kode_pelanggan']) {
            throw new PageNotFoundException('Booking tidak ditemukan');
        }

        $data = [
            'title' => 'Detail Booking',
            'booking' => $booking
        ];

        return view('pelanggan/booking/detail', $data);
    }
}
