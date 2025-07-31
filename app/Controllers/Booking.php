<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\LayananModel;
use App\Models\PelangganModel;
use App\Models\AntrianModel;
use App\Models\TransaksiModel;
use CodeIgniter\API\ResponseTrait;

class Booking extends BaseController
{
    use ResponseTrait;

    protected $bookingModel;
    protected $layananModel;
    protected $pelangganModel;
    protected $antrianModel;
    protected $transaksiModel;
    protected $karyawanModel;
    protected $db;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->layananModel = new LayananModel();
        $this->pelangganModel = new PelangganModel();
        $this->antrianModel = new AntrianModel();
        $this->transaksiModel = new TransaksiModel();
        $this->karyawanModel = new \App\Models\KaryawanModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Halaman form booking untuk pelanggan
     */
    public function create()
    {
        // Pastikan user sudah login dan adalah pelanggan
        if (!session()->get('logged_in') || session()->get('role') !== 'pelanggan') {
            return redirect()->to('auth')->with('error', 'Silakan login sebagai pelanggan terlebih dahulu.');
        }

        // Get pelanggan data
        $userId = session()->get('user_id');
        $pelanggan = $this->pelangganModel->where('user_id', $userId)->first();

        if (!$pelanggan) {
            return redirect()->to('pelanggan/dashboard')->with('error', 'Data pelanggan tidak ditemukan.');
        }

        // Get active services
        $layananList = $this->layananModel->where('status', 'aktif')->findAll();

        $data = [
            'title' => 'Booking Layanan',
            'subtitle' => 'Buat booking layanan cuci kendaraan',
            'pelanggan' => $pelanggan,
            'layanan_list' => $layananList
        ];

        return view('pelanggan/booking/create', $data);
    }

    /**
     * Proses simpan booking
     */
    public function store()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Validasi user login
        if (!session()->get('logged_in') || session()->get('role') !== 'pelanggan') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ]);
        }

        // Get pelanggan data
        $userId = session()->get('user_id');
        $pelanggan = $this->pelangganModel->where('user_id', $userId)->first();

        if (!$pelanggan) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data pelanggan tidak ditemukan'
            ]);
        }

        // Validasi input
        $rules = [
            'layanan_id' => 'required',
            'tanggal' => 'required|valid_date',
            'jam' => 'required',
            'no_plat' => 'required|max_length[20]',
            'jenis_kendaraan' => 'required|in_list[motor,mobil,lainnya]',
            'merk_kendaraan' => 'permit_empty|max_length[50]',
            'catatan' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak valid',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Cek ketersediaan slot
        $tanggal = $this->request->getPost('tanggal');
        $jam = $this->request->getPost('jam');
        $jenisKendaraan = $this->request->getPost('jenis_kendaraan');

        if (!$this->bookingModel->checkSlotAvailability($tanggal, $jam, $jenisKendaraan)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Maaf, slot pada tanggal dan jam tersebut sudah penuh. Silakan pilih waktu lain.'
            ]);
        }

        // Get layanan data
        $layananId = $this->request->getPost('layanan_id');
        $layanan = $this->layananModel->find($layananId);

        if (!$layanan) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Layanan tidak ditemukan'
            ]);
        }

        // Prepare booking data
        $bookingData = [
            'pelanggan_id' => $pelanggan['kode_pelanggan'],
            'tanggal' => $tanggal,
            'jam' => $jam,
            'no_plat' => strtoupper($this->request->getPost('no_plat')),
            'jenis_kendaraan' => $jenisKendaraan,
            'merk_kendaraan' => $this->request->getPost('merk_kendaraan'),
            'layanan_id' => $layananId,
            'status' => 'pending',
            'catatan' => $this->request->getPost('catatan'),
            'user_id' => $userId
        ];

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insert booking
            if ($this->bookingModel->insert($bookingData)) {
                $bookingId = $this->bookingModel->getInsertID();

                // Get the booking with generated kode_booking
                $booking = $this->bookingModel->find($bookingId);

                log_message('info', 'Booking created successfully: ' . json_encode($booking));

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('Database transaction failed');
                }

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Booking berhasil dibuat! Kode booking Anda: ' . $booking['kode_booking'],
                    'data' => [
                        'booking_id' => $bookingId,
                        'kode_booking' => $booking['kode_booking'],
                        'redirect' => site_url('pelanggan/booking/detail/' . $bookingId)
                    ]
                ]);
            } else {
                throw new \Exception('Gagal menyimpan booking: ' . json_encode($this->bookingModel->errors()));
            }
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Booking creation failed: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal membuat booking: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Proses simpan booking dari form public (untuk guest dan pelanggan)
     */
    public function storePublic()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        // Validation rules
        $rules = [
            'selected_services' => 'required',
            'total_durasi' => 'required|numeric',
            'total_harga' => 'required|numeric',
            'tanggal' => 'required|valid_date',
            'jam' => 'required',
            'no_plat' => 'required|max_length[20]',
            'jenis_kendaraan' => 'required|in_list[motor,mobil,lainnya]',
            'merk_kendaraan' => 'permit_empty|max_length[50]',
            'catatan' => 'permit_empty'
        ];

        // Jika user belum login, validasi data pelanggan
        $isLoggedIn = session()->get('logged_in') && session()->get('role') === 'pelanggan';
        if (!$isLoggedIn) {
            $rules['nama_pelanggan'] = 'required|max_length[100]';
            $rules['no_hp'] = 'required|max_length[15]';
            $rules['email'] = 'permit_empty|valid_email';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak valid',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Parse selected services
        $selectedServicesJson = $this->request->getPost('selected_services');
        $selectedServices = json_decode($selectedServicesJson, true);

        if (!$selectedServices || !is_array($selectedServices) || empty($selectedServices)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Layanan harus dipilih'
            ]);
        }

        // Validate selected services exist
        $validServices = [];
        foreach ($selectedServices as $kodeLayanan) {
            // Ensure kodeLayanan is a string
            if (!is_string($kodeLayanan) || empty($kodeLayanan)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Format kode layanan tidak valid'
                ]);
            }

            $service = $this->layananModel->find($kodeLayanan);
            if (!$service || $service['status'] !== 'aktif') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => "Layanan {$kodeLayanan} tidak valid atau tidak aktif"
                ]);
            }
            $validServices[] = $service;
        }

        // Get atau create pelanggan data
        $pelangganId = null;
        $userId = null;

        if ($isLoggedIn) {
            // User sudah login sebagai pelanggan
            $userId = session()->get('user_id');
            $pelanggan = $this->pelangganModel->where('user_id', $userId)->first();

            if (!$pelanggan) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data pelanggan tidak ditemukan'
                ]);
            }
            $pelangganId = $pelanggan['kode_pelanggan'];
        } else {
            // Guest booking - create temporary pelanggan record
            $pelangganData = [
                'nama_pelanggan' => $this->request->getPost('nama_pelanggan'),
                'no_hp' => $this->request->getPost('no_hp'),
                'alamat' => 'Guest booking - ' . date('Y-m-d H:i:s')
            ];

            // Check if pelanggan already exists by phone
            $existingPelanggan = $this->pelangganModel->where('no_hp', $pelangganData['no_hp'])->first();
            if ($existingPelanggan) {
                $pelangganId = $existingPelanggan['kode_pelanggan'];
            } else {
                // Create new pelanggan for guest
                if ($this->pelangganModel->insert($pelangganData)) {
                    $insertId = $this->pelangganModel->getInsertID();
                    $newPelanggan = $this->pelangganModel->find($insertId);
                    $pelangganId = $newPelanggan['kode_pelanggan'];
                } else {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Gagal menyimpan data pelanggan'
                    ]);
                }
            }
        }

        // Check slot availability for the total duration
        $tanggal = $this->request->getPost('tanggal');
        $jam = $this->request->getPost('jam');
        $totalDurasi = (int) $this->request->getPost('total_durasi');
        $jenisKendaraan = $this->request->getPost('jenis_kendaraan');

        // Convert jam to minutes for calculation
        list($hours, $minutes) = explode(':', $jam);
        $startTimeMinutes = ($hours * 60) + $minutes;
        $endTimeMinutes = $startTimeMinutes + $totalDurasi;

        // Check if there are available karyawan for this time slot
        if (!$this->bookingModel->checkSlotAvailabilityWithKaryawan($tanggal, $jam, $totalDurasi)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Tidak ada karyawan yang tersedia pada slot waktu tersebut. Silakan pilih waktu lain.'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Generate single kode_booking for all services
            $prefix = 'BK';
            $date = date('Ymd');
            $lastBooking = $this->bookingModel->orderBy('id', 'DESC')->first();

            $number = 1;
            if ($lastBooking) {
                $lastKode = $lastBooking['kode_booking'];
                if (preg_match('/[A-Z]+-[0-9]+-([0-9]+)/', $lastKode, $matches)) {
                    $number = (int)$matches[1] + 1;
                }
            }

            $sharedKodeBooking = $prefix . '-' . $date . '-' . sprintf('%03d', $number);

            // Get ONE karyawan available for the entire booking duration
            $sharedKaryawan = $this->bookingModel->getRandomAvailableKaryawan(
                $tanggal,
                $jam,
                $totalDurasi
            );

            if (!$sharedKaryawan) {
                throw new \Exception('Tidak ada karyawan yang tersedia untuk menangani semua layanan pada waktu tersebut');
            }

            log_message('info', "Assigned shared karyawan {$sharedKaryawan['namakaryawan']} (ID: {$sharedKaryawan['idkaryawan']}) for entire booking duration ({$totalDurasi} minutes)");

            // Create booking for each selected service
            $bookingIds = [];
            $totalHarga = 0;

            foreach ($validServices as $index => $service) {
                // Calculate jam for each service (sequential)
                $serviceStartMinutes = $startTimeMinutes;
                if ($index > 0) {
                    // Add duration of all previous services
                    for ($i = 0; $i < $index; $i++) {
                        $serviceStartMinutes += (int)$validServices[$i]['durasi_menit'];
                    }
                }

                $serviceJam = sprintf(
                    '%02d:%02d',
                    floor($serviceStartMinutes / 60),
                    $serviceStartMinutes % 60
                );

                // Set payment timeout (30 minutes from now)
                $paymentExpires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

                $bookingData = [
                    'kode_booking' => $sharedKodeBooking, // Use shared kode_booking
                    'pelanggan_id' => $pelangganId,
                    'tanggal' => $tanggal,
                    'jam' => $serviceJam,
                    'no_plat' => strtoupper($this->request->getPost('no_plat')),
                    'jenis_kendaraan' => $jenisKendaraan,
                    'merk_kendaraan' => $this->request->getPost('merk_kendaraan'),
                    'layanan_id' => $service['kode_layanan'],
                    'status' => 'menunggu',
                    'payment_expires_at' => $paymentExpires,
                    'catatan' => $this->request->getPost('catatan'),
                    'user_id' => $userId,
                    'id_karyawan' => $sharedKaryawan['idkaryawan'] // Use SAME karyawan for all services
                ];

                log_message('info', "Using shared karyawan {$sharedKaryawan['namakaryawan']} (ID: {$sharedKaryawan['idkaryawan']}) for service {$service['nama_layanan']} at {$serviceJam}");
                log_message('info', "Booking data to be inserted: " . json_encode($bookingData));

                if ($this->bookingModel->insert($bookingData)) {
                    $bookingIds[] = $this->bookingModel->getInsertID();
                    $totalHarga += (float)$service['harga'];
                    log_message('info', "Successfully inserted booking for service: {$service['nama_layanan']}");
                } else {
                    $errors = $this->bookingModel->errors();
                    log_message('error', "Booking validation errors: " . json_encode($errors));
                    log_message('error', "Booking data that failed: " . json_encode($bookingData));
                    log_message('error', "Karyawan data: " . json_encode($sharedKaryawan));
                    throw new \Exception('Gagal menyimpan booking untuk layanan: ' . $service['nama_layanan'] . '. Errors: ' . json_encode($errors));
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            $endTime = sprintf(
                '%02d:%02d',
                floor($endTimeMinutes / 60),
                $endTimeMinutes % 60
            );

            $successMessage = "Booking berhasil dibuat! Kode booking: {$sharedKodeBooking}";
            $successMessage .= "\nTotal " . count($validServices) . " layanan dari {$jam} hingga {$endTime}";
            $successMessage .= "\nTotal biaya: Rp " . number_format($totalHarga, 0, ',', '.');

            if (!$isLoggedIn) {
                $successMessage .= "\nSimpan kode ini untuk melacak status booking Anda.";
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $successMessage,
                'data' => [
                    'booking_ids' => $bookingIds,
                    'kode_booking' => $sharedKodeBooking,
                    'total_services' => count($validServices),
                    'total_durasi' => $totalDurasi,
                    'total_harga' => $totalHarga,
                    'waktu_mulai' => $jam,
                    'waktu_selesai' => $endTime,
                    'pelanggan_name' => $isLoggedIn ? null : $this->request->getPost('nama_pelanggan'),
                    'is_guest' => !$isLoggedIn,
                    'payment_url' => site_url('payment/' . $sharedKodeBooking)
                ]
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Multiple services booking creation failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal membuat booking: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Detail booking
     */
    public function detail($bookingId)
    {
        // Pastikan user sudah login
        if (!session()->get('logged_in')) {
            return redirect()->to('auth');
        }

        $booking = $this->bookingModel->getBookingWithDetails($bookingId);

        if (!$booking) {
            return redirect()->to('pelanggan/dashboard')->with('error', 'Booking tidak ditemukan.');
        }

        // Cek apakah user berhak akses booking ini
        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        if ($userRole === 'pelanggan') {
            $pelanggan = $this->pelangganModel->where('user_id', $userId)->first();
            if (!$pelanggan || $booking['pelanggan_id'] !== $pelanggan['kode_pelanggan']) {
                return redirect()->to('pelanggan/dashboard')->with('error', 'Anda tidak memiliki akses ke booking ini.');
            }
        } elseif (!in_array($userRole, ['admin', 'pimpinan'])) {
            return redirect()->to('auth');
        }

        // Get antrian jika ada
        $antrian = $this->antrianModel->where('booking_id', $bookingId)->first();

        // Get transaksi jika ada
        $transaksi = $this->transaksiModel->where('booking_id', $bookingId)->first();

        $data = [
            'title' => 'Detail Booking',
            'subtitle' => 'Informasi lengkap booking Anda',
            'booking' => $booking,
            'antrian' => $antrian,
            'transaksi' => $transaksi
        ];

        return view('pelanggan/booking/detail', $data);
    }

    /**
     * Riwayat booking pelanggan
     */
    public function history()
    {
        // Pastikan user sudah login sebagai pelanggan
        if (!session()->get('logged_in') || session()->get('role') !== 'pelanggan') {
            return redirect()->to('auth');
        }

        $userId = session()->get('user_id');
        $pelanggan = $this->pelangganModel->where('user_id', $userId)->first();

        if (!$pelanggan) {
            return redirect()->to('pelanggan/dashboard')->with('error', 'Data pelanggan tidak ditemukan.');
        }

        // Get booking history
        $bookings = $this->bookingModel->getBookingsByPelanggan($pelanggan['kode_pelanggan']);

        $data = [
            'title' => 'Riwayat Booking',
            'subtitle' => 'Semua booking layanan cuci kendaraan Anda',
            'bookings' => $bookings,
            'pelanggan' => $pelanggan
        ];

        return view('pelanggan/booking/history', $data);
    }

    /**
     * Konfirmasi booking (untuk admin)
     */
    public function confirm($bookingId)
    {
        // Pastikan user adalah admin/pimpinan
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ]);
        }

        $booking = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking tidak ditemukan'
            ]);
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update status booking
            $this->bookingModel->update($bookingId, ['status' => 'dikonfirmasi']);

            // Buat antrian otomatis
            $antrianData = [
                'booking_id' => $bookingId,
                'tanggal' => $booking['tanggal'],
                'status' => 'menunggu'
            ];

            if ($this->antrianModel->insert($antrianData)) {
                $antrianId = $this->antrianModel->getInsertID();
                $antrian = $this->antrianModel->find($antrianId);

                log_message('info', 'Antrian created for booking ' . $bookingId . ': ' . $antrian['nomor_antrian']);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Booking berhasil dikonfirmasi dan antrian telah dibuat'
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Booking confirmation failed: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengkonfirmasi booking: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Proses pembayaran dari booking
     */
    public function processPayment($bookingId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Pastikan user sudah login
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ]);
        }

        $booking = $this->bookingModel->getBookingWithDetails($bookingId);
        if (!$booking) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking tidak ditemukan'
            ]);
        }

        // Validasi akses
        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        if ($userRole === 'pelanggan') {
            $pelanggan = $this->pelangganModel->where('user_id', $userId)->first();
            if (!$pelanggan || $booking['pelanggan_id'] !== $pelanggan['kode_pelanggan']) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke booking ini'
                ]);
            }
        }

        // Validasi input
        $rules = [
            'metode_pembayaran' => 'required|in_list[tunai,kartu_kredit,kartu_debit,e-wallet,transfer]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Metode pembayaran harus dipilih',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create transaksi
            $transaksiData = [
                'tanggal' => date('Y-m-d'),
                'booking_id' => $bookingId,
                'pelanggan_id' => $booking['pelanggan_id'],
                'layanan_id' => $booking['layanan_id'],
                'no_plat' => $booking['no_plat'],
                'jenis_kendaraan' => $booking['jenis_kendaraan'],
                'total_harga' => $booking['harga'],
                'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
                'status_pembayaran' => 'dibayar',
                'catatan' => 'Pembayaran untuk booking ' . $booking['kode_booking'],
                'user_id' => $userId
            ];

            if ($this->transaksiModel->insert($transaksiData)) {
                $transaksiId = $this->transaksiModel->getInsertID();
                $transaksi = $this->transaksiModel->find($transaksiId);

                // Update booking status
                $this->bookingModel->update($bookingId, ['status' => 'selesai']);

                log_message('info', 'Payment processed for booking ' . $bookingId . ': ' . $transaksi['no_transaksi']);

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('Database transaction failed');
                }

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Pembayaran berhasil diproses! No. Transaksi: ' . $transaksi['no_transaksi'],
                    'data' => [
                        'transaksi_id' => $transaksiId,
                        'no_transaksi' => $transaksi['no_transaksi']
                    ]
                ]);
            } else {
                throw new \Exception('Gagal menyimpan transaksi: ' . json_encode($this->transaksiModel->errors()));
            }
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Payment processing failed: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Cancel booking
     */
    public function cancel($bookingId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Pastikan user sudah login
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ]);
        }

        $booking = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking tidak ditemukan'
            ]);
        }

        // Validasi akses
        $userRole = session()->get('role');
        $userId = session()->get('user_id');

        if ($userRole === 'pelanggan') {
            $pelanggan = $this->pelangganModel->where('user_id', $userId)->first();
            if (!$pelanggan || $booking['pelanggan_id'] !== $pelanggan['kode_pelanggan']) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke booking ini'
                ]);
            }
        }

        // Cek apakah booking masih bisa dibatalkan
        if (in_array($booking['status'], ['selesai', 'dibatalkan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking dengan status ' . $booking['status'] . ' tidak dapat dibatalkan'
            ]);
        }

        try {
            // Update status booking
            if ($this->bookingModel->update($bookingId, ['status' => 'dibatalkan'])) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Booking berhasil dibatalkan'
                ]);
            } else {
                throw new \Exception('Gagal membatalkan booking');
            }
        } catch (\Exception $e) {
            log_message('error', 'Booking cancellation failed: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal membatalkan booking: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get available time slots for specific date and service
     */
    public function getAvailableSlots()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        $tanggal = $this->request->getPost('tanggal');
        $totalDurasi = $this->request->getPost('total_durasi');
        $jenisKendaraan = $this->request->getPost('jenis_kendaraan');

        if (!$tanggal) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tanggal harus dipilih']);
        }

        try {
            // Get total number of karyawan
            $karyawanModel = new \App\Models\KaryawanModel();
            $totalKaryawan = $karyawanModel->countAll();

            // Get existing bookings for the date with karyawan info
            $existingBookings = $this->bookingModel
                ->select('booking.jam, booking.layanan_id, booking.id_karyawan')
                ->join('layanan l', 'l.kode_layanan = booking.layanan_id', 'left')
                ->select('l.durasi_menit as durasi')
                ->where('booking.tanggal', $tanggal)
                ->where('booking.status !=', 'dibatalkan')
                ->findAll();

            // Filter out past times for today
            $currentTime = date('H:i');
            $isToday = ($tanggal === date('Y-m-d'));

            $response = [
                'status' => 'success',
                'existing_bookings' => $existingBookings,
                'total_durasi' => $totalDurasi,
                'total_karyawan' => $totalKaryawan,
                'is_today' => $isToday,
                'current_time' => $isToday ? $currentTime : null
            ];

            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', 'Error in getAvailableSlots: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil slot waktu'
            ]);
        }
    }

    // ================== ADMIN BOOKING METHODS ==================

    /**
     * Admin booking index with payment confirmation integration
     */
    public function index()
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get unique transactions with booking summary
        $transactions = $this->db->query("
            SELECT 
                t.no_transaksi,
                t.status_pembayaran,
                t.bukti_pembayaran,
                t.total_harga,
                t.metode_pembayaran,
                t.created_at as tanggal_transaksi,
                t.id as transaksi_id,
                MIN(b.tanggal) as tanggal_booking,
                MIN(b.jam) as jam_booking,
                b.kode_booking,
                p.nama_pelanggan,
                GROUP_CONCAT(DISTINCT l.nama_layanan SEPARATOR ', ') as layanan_list,
                COUNT(DISTINCT b.id) as jumlah_layanan,
                k.namakaryawan
            FROM transaksi t
            LEFT JOIN booking b ON t.booking_id = b.id
            LEFT JOIN pelanggan p ON b.pelanggan_id = p.kode_pelanggan
            LEFT JOIN layanan l ON b.layanan_id = l.kode_layanan
            LEFT JOIN karyawan k ON b.id_karyawan = k.idkaryawan
            WHERE t.no_transaksi IS NOT NULL
            GROUP BY t.no_transaksi
            ORDER BY t.created_at DESC
        ")->getResultArray();

        // Get statistics
        $stats = [
            'total_bookings' => $this->bookingModel->countAll(),
            'pending_bookings' => $this->bookingModel->where('status', 'menunggu')->countAllResults(),
            'confirmed_bookings' => $this->bookingModel->where('status', 'dikonfirmasi')->countAllResults(),
            'completed_bookings' => $this->bookingModel->where('status', 'selesai')->countAllResults(),
            'pending_payments' => $this->db->table('transaksi')->where('status_pembayaran', 'belum_bayar')->where('bukti_pembayaran IS NOT NULL')->countAllResults()
        ];

        $data = [
            'title' => 'Data Booking & Transaksi',
            'subtitle' => 'Kelola booking pelanggan dan konfirmasi pembayaran',
            'transactions' => $transactions,
            'stats' => $stats
        ];

        return view('admin/booking/index', $data);
    }

    /**
     * Admin create booking form
     */
    public function adminCreate()
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get related data for form
        $pelanggan = $this->pelangganModel->findAll();
        $layanan = $this->layananModel->where('status', 'aktif')->findAll();
        $karyawan = $this->karyawanModel->findAll();

        $data = [
            'title' => 'Tambah Booking Baru',
            'subtitle' => 'Buat booking baru untuk pelanggan',
            'pelanggan' => $pelanggan,
            'layanan' => $layanan,
            'karyawan' => $karyawan
        ];

        return view('admin/booking/create', $data);
    }

    /**
     * Admin store booking
     */
    public function adminStore()
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        $rules = [
            'pelanggan_id' => 'required',
            'tanggal' => 'required|valid_date',
            'jam' => 'required',
            'no_plat' => 'required',
            'jenis_kendaraan' => 'required|in_list[motor,mobil,lainnya]',
            'layanan_id' => 'required',
            'id_karyawan' => 'permit_empty',
            'catatan' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'pelanggan_id' => $this->request->getPost('pelanggan_id'),
            'tanggal' => $this->request->getPost('tanggal'),
            'jam' => $this->request->getPost('jam'),
            'no_plat' => strtoupper($this->request->getPost('no_plat')),
            'jenis_kendaraan' => $this->request->getPost('jenis_kendaraan'),
            'merk_kendaraan' => $this->request->getPost('merk_kendaraan'),
            'layanan_id' => $this->request->getPost('layanan_id'),
            'id_karyawan' => $this->request->getPost('id_karyawan'),
            'status' => 'dikonfirmasi', // Admin booking auto confirmed
            'catatan' => $this->request->getPost('catatan')
        ];

        if ($this->bookingModel->insert($data)) {
            return redirect()->to('admin/booking')->with('success', 'Booking berhasil ditambahkan');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan booking');
        }
    }

    /**
     * Show booking detail with payment info by transaction ID
     */
    public function show($transaksiId)
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        $transaksiModel = new \App\Models\TransaksiModel();

        // Get transaction with booking details
        $booking = $transaksiModel
            ->select('transaksi.*, booking.kode_booking, booking.tanggal, booking.jam, booking.no_plat, booking.jenis_kendaraan, booking.merk_kendaraan, booking.status as booking_status, booking.catatan')
            ->select('pelanggan.nama_pelanggan, pelanggan.no_hp, pelanggan.alamat')
            ->select('layanan.nama_layanan, layanan.harga, layanan.durasi_menit')
            ->select('karyawan.namakaryawan')
            ->join('booking', 'booking.id = transaksi.booking_id', 'left')
            ->join('pelanggan', 'pelanggan.kode_pelanggan = booking.pelanggan_id', 'left')
            ->join('layanan', 'layanan.kode_layanan = booking.layanan_id', 'left')
            ->join('karyawan', 'karyawan.idkaryawan = booking.id_karyawan', 'left')
            ->find($transaksiId);

        if (!$booking) {
            return redirect()->to('admin/booking')->with('error', 'Transaksi tidak ditemukan');
        }

        // Get all bookings with same kode_booking (multi-service)
        $relatedBookings = [];
        if ($booking['kode_booking']) {
            $relatedBookings = $this->bookingModel
                ->select('booking.*, layanan.nama_layanan, layanan.harga, layanan.durasi_menit')
                ->join('layanan', 'layanan.kode_layanan = booking.layanan_id', 'left')
                ->where('booking.kode_booking', $booking['kode_booking'])
                ->orderBy('booking.jam', 'ASC')
                ->findAll();
        }

        // Add additional fields for compatibility
        $booking['tanggal_booking'] = $booking['tanggal'];
        $booking['jam_booking'] = $booking['jam'];
        $booking['transaksi_id'] = $booking['id'];

        $data = [
            'title' => 'Detail Transaksi',
            'subtitle' => 'Informasi lengkap booking dan pembayaran',
            'booking' => $booking,
            'relatedBookings' => $relatedBookings
        ];

        return view('admin/booking/show', $data);
    }

    /**
     * Edit booking
     */
    public function edit($bookingId)
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        $booking = $this->bookingModel->find($bookingId);

        if (!$booking) {
            return redirect()->to('admin/booking')->with('error', 'Booking tidak ditemukan');
        }

        // Get related data for form
        $pelanggan = $this->pelangganModel->findAll();
        $layanan = $this->layananModel->findAll();
        $karyawan = $this->karyawanModel->findAll();

        $data = [
            'title' => 'Edit Booking',
            'subtitle' => 'Ubah data booking',
            'booking' => $booking,
            'pelanggan' => $pelanggan,
            'layanan' => $layanan,
            'karyawan' => $karyawan
        ];

        return view('admin/booking/edit', $data);
    }

    /**
     * Update booking
     */
    public function update($bookingId)
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        $booking = $this->bookingModel->find($bookingId);

        if (!$booking) {
            return redirect()->to('admin/booking')->with('error', 'Booking tidak ditemukan');
        }

        $rules = [
            'pelanggan_id' => 'required',
            'tanggal' => 'required|valid_date',
            'jam' => 'required',
            'no_plat' => 'required',
            'jenis_kendaraan' => 'required|in_list[motor,mobil,lainnya]',
            'layanan_id' => 'required',
            'id_karyawan' => 'permit_empty',
            'status' => 'required|in_list[menunggu,dikonfirmasi,diproses,selesai,batal]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'pelanggan_id' => $this->request->getPost('pelanggan_id'),
            'tanggal' => $this->request->getPost('tanggal'),
            'jam' => $this->request->getPost('jam'),
            'no_plat' => strtoupper($this->request->getPost('no_plat')),
            'jenis_kendaraan' => $this->request->getPost('jenis_kendaraan'),
            'merk_kendaraan' => $this->request->getPost('merk_kendaraan'),
            'layanan_id' => $this->request->getPost('layanan_id'),
            'id_karyawan' => $this->request->getPost('id_karyawan'),
            'status' => $this->request->getPost('status'),
            'catatan' => $this->request->getPost('catatan')
        ];

        if ($this->bookingModel->update($bookingId, $data)) {
            return redirect()->to('admin/booking')->with('success', 'Booking berhasil diperbarui');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui booking');
        }
    }

    /**
     * Delete booking
     */
    public function delete($bookingId)
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akses ditolak'
            ]);
        }

        $booking = $this->bookingModel->find($bookingId);

        if (!$booking) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking tidak ditemukan'
            ]);
        }

        if ($this->bookingModel->delete($bookingId)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Booking berhasil dihapus'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus booking'
            ]);
        }
    }

    /**
     * Approve payment (integrated into booking)
     */
    public function approvePayment($transaksiId)
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akses ditolak'
            ]);
        }

        $transaksiModel = new \App\Models\TransaksiModel();
        $transaksi = $transaksiModel->find($transaksiId);

        if (!$transaksi) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan'
            ]);
        }

        if ($transaksi['status_pembayaran'] !== 'belum_bayar') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaksi sudah diproses sebelumnya'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update payment status to approved
            $transaksiModel->update($transaksiId, [
                'status_pembayaran' => 'dibayar',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Update related booking status to confirmed
            if ($transaksi['booking_id']) {
                $booking = $this->bookingModel->find($transaksi['booking_id']);
                if ($booking && is_array($booking)) {
                    // Update all bookings with same kode_booking
                    $this->bookingModel->where('kode_booking', $booking['kode_booking'])
                        ->set(['status' => 'dikonfirmasi'])
                        ->update();

                    log_message('info', "Admin approved payment {$transaksi['no_transaksi']}, booking {$booking['kode_booking']} confirmed");
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pembayaran berhasil dikonfirmasi'
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Payment approval failed: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengkonfirmasi pembayaran: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reject payment (integrated into booking)
     */
    public function rejectPayment($transaksiId)
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akses ditolak'
            ]);
        }

        $transaksiModel = new \App\Models\TransaksiModel();
        $transaksi = $transaksiModel->find($transaksiId);

        if (!$transaksi) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan'
            ]);
        }

        if ($transaksi['status_pembayaran'] !== 'belum_bayar') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaksi sudah diproses sebelumnya'
            ]);
        }

        $alasan = $this->request->getPost('alasan') ?? 'Bukti pembayaran tidak valid';

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update payment status to rejected
            $transaksiModel->update($transaksiId, [
                'status_pembayaran' => 'batal',
                'catatan' => $alasan,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Update related booking status to cancelled
            if ($transaksi['booking_id']) {
                $booking = $this->bookingModel->find($transaksi['booking_id']);
                if ($booking && is_array($booking)) {
                    // Update all bookings with same kode_booking
                    $this->bookingModel->where('kode_booking', $booking['kode_booking'])
                        ->set(['status' => 'batal'])
                        ->update();

                    log_message('info', "Admin rejected payment {$transaksi['no_transaksi']}, booking {$booking['kode_booking']} cancelled. Reason: {$alasan}");
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pembayaran berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Payment rejection failed: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menolak pembayaran: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete transaction and related bookings
     */
    public function deleteTransaction($transaksiId)
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akses ditolak'
            ]);
        }

        $transaksiModel = new \App\Models\TransaksiModel();
        $transaksi = $transaksiModel->find($transaksiId);

        if (!$transaksi) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Get related booking
            $booking = $this->bookingModel->find($transaksi['booking_id']);

            if ($booking && is_array($booking)) {
                // Delete all bookings with same kode_booking
                $this->bookingModel->where('kode_booking', $booking['kode_booking'])->delete();
                log_message('info', "Deleted bookings with kode_booking: {$booking['kode_booking']}");
            }

            // Delete transaction
            $transaksiModel->delete($transaksiId);
            log_message('info', "Deleted transaction: {$transaksi['no_transaksi']}");

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Transaksi dan booking terkait berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Transaction deletion failed: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
            ]);
        }
    }
}
