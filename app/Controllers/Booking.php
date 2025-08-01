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
            'status' => 'menunggu_konfirmasi',
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
                    'status' => 'menunggu_konfirmasi',
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

        // Get all bookings with same kode_booking (for multi-service bookings)
        $relatedBookings = [];
        if ($booking['kode_booking']) {
            $relatedBookings = $this->bookingModel->getBookingsByKodeBooking($booking['kode_booking']);
        }

        // Get antrian jika ada
        $antrian = $this->antrianModel->where('booking_id', $bookingId)->first();

        // Get transaksi jika ada (check all bookings with same kode_booking)
        $transaksi = null;
        if ($booking['kode_booking']) {
            // Find transaction for any booking with the same kode_booking
            $allRelatedBookings = $this->bookingModel->where('kode_booking', $booking['kode_booking'])->findAll();
            foreach ($allRelatedBookings as $relatedBooking) {
                $foundTransaksi = $this->transaksiModel->where('booking_id', $relatedBooking['id'])->first();
                if ($foundTransaksi) {
                    $transaksi = $foundTransaksi;
                    break; // Use the first transaction found for the booking group
                }
            }
        }

        $data = [
            'title' => 'Detail Booking',
            'subtitle' => 'Informasi lengkap booking Anda',
            'booking' => $booking,
            'relatedBookings' => $relatedBookings,
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

        // Get booking history and group by kode_booking
        $allBookings = $this->bookingModel->getBookingsByPelanggan($pelanggan['kode_pelanggan']);

        // Group bookings by kode_booking
        $groupedBookings = [];
        foreach ($allBookings as $booking) {
            $kodeBooking = $booking['kode_booking'];
            if (!isset($groupedBookings[$kodeBooking])) {
                $groupedBookings[$kodeBooking] = [
                    'main_booking' => $booking,
                    'services' => [],
                    'total_harga' => 0,
                    'service_count' => 0
                ];
            }

            $groupedBookings[$kodeBooking]['services'][] = $booking;
            $groupedBookings[$kodeBooking]['total_harga'] += (float)$booking['harga'];
            $groupedBookings[$kodeBooking]['service_count']++;
        }

        // Convert to indexed array and sort by date
        $bookings = array_values($groupedBookings);
        usort($bookings, function ($a, $b) {
            $dateTimeA = $a['main_booking']['tanggal'] . ' ' . $a['main_booking']['jam'];
            $dateTimeB = $b['main_booking']['tanggal'] . ' ' . $b['main_booking']['jam'];
            return strtotime($dateTimeB) - strtotime($dateTimeA);
        });

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
                ->where('booking.id_karyawan IS NOT NULL') // Only bookings with assigned karyawan
                ->findAll();

            // Filter out past times for today
            $currentTime = date('H:i');
            $isToday = ($tanggal === date('Y-m-d'));

            // Check if this is a simple request (like from pelanggan) - no total_durasi parameter
            if (empty($totalDurasi)) {
                // Return simple array of available slots like pelanggan expects
                $availableSlots = $this->generateSimpleAvailableSlots($existingBookings, $totalKaryawan, $isToday, $currentTime);

                // Convert to plain arrays to ensure JSON compatibility
                $existingBookingsArray = [];
                foreach ($existingBookings as $booking) {
                    // Only include bookings with valid karyawan assignment
                    if (!empty($booking['id_karyawan']) && !empty($booking['jam'])) {
                        $existingBookingsArray[] = [
                            'jam' => $booking['jam'],
                            'layanan_id' => $booking['layanan_id'],
                            'id_karyawan' => $booking['id_karyawan'],
                            'durasi' => (int)($booking['durasi'] ?? 60)
                        ];
                    }
                }

                // Debug log
                log_message('debug', "Processing date: $tanggal");
                log_message('debug', "Total karyawan: $totalKaryawan");
                log_message('debug', "Raw bookings: " . count($existingBookings));
                log_message('debug', "Valid bookings: " . count($existingBookingsArray));
                log_message('debug', "Available slots: " . count($availableSlots));



                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $availableSlots,
                    // Also include detailed data for admin karyawan count display
                    'existing_bookings' => $existingBookingsArray,
                    'total_karyawan' => (int)$totalKaryawan,
                    'is_today' => $isToday,
                    'current_time' => $isToday ? $currentTime : null
                ]);
            }

            // Return complex data for admin (when total_durasi is provided)
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

    /**
     * Generate simple available slots array (like pelanggan expects)
     */
    private function generateSimpleAvailableSlots($existingBookings, $totalKaryawan, $isToday, $currentTime)
    {
        $startHour = 8;
        $endHour = 17;
        $availableSlots = [];



        // Generate 30-minute slots
        for ($hour = $startHour; $hour < $endHour; $hour++) {
            $slots = [
                sprintf('%02d:00', $hour),
                sprintf('%02d:30', $hour)
            ];

            foreach ($slots as $slot) {
                // Skip past times for today
                if ($isToday && $currentTime && $slot <= $currentTime) {
                    continue;
                }

                // Count busy employees at this time
                $busyKaryawanSet = [];
                foreach ($existingBookings as $booking) {
                    // Skip invalid bookings
                    if (empty($booking['id_karyawan']) || empty($booking['jam'])) {
                        continue;
                    }

                    $bookingStart = $booking['jam'];
                    $bookingEnd = $this->addMinutesToTime($bookingStart, (int)($booking['durasi'] ?? 60));
                    $slotEnd = $this->addMinutesToTime($slot, 60);

                    if ($this->timesOverlap($slot, $slotEnd, $bookingStart, $bookingEnd)) {
                        $busyKaryawanSet[$booking['id_karyawan']] = true;
                    }
                }

                $availableKaryawan = $totalKaryawan - count($busyKaryawanSet);

                // Log for debugging
                log_message('debug', "Slot $slot - busy karyawan: " . count($busyKaryawanSet) . ", available: $availableKaryawan");

                if ($availableKaryawan > 0) {
                    $availableSlots[] = $slot;
                }
            }
        }
        return $availableSlots;
    }

    /**
     * Helper method to add minutes to time string
     */
    private function addMinutesToTime($timeStr, $minutes)
    {
        $time = new \DateTime($timeStr);
        $time->add(new \DateInterval('PT' . $minutes . 'M'));
        return $time->format('H:i');
    }

    /**
     * Helper method to check if times overlap
     */
    private function timesOverlap($start1, $end1, $start2, $end2)
    {
        return $start1 < $end2 && $end1 > $start2;
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

        // Get all bookings grouped by kode_booking with optional transaction data
        // Use a more compatible approach for MySQL strict mode
        $allBookings = $this->db->query("
            SELECT 
                b.kode_booking,
                MAX(CASE WHEN b.id = min_booking.min_id THEN b.status END) as booking_status,
                MIN(b.tanggal) as tanggal_booking,
                MIN(b.jam) as jam_booking,
                MAX(b.created_at) as created_at,
                MAX(CASE WHEN b.id = min_booking.min_id THEN p.nama_pelanggan END) as nama_pelanggan,
                GROUP_CONCAT(DISTINCT l.nama_layanan ORDER BY l.nama_layanan SEPARATOR ', ') as layanan_list,
                COUNT(b.id) as jumlah_layanan,
                SUM(l.harga) as total_harga_layanan,
                MAX(CASE WHEN b.id = min_booking.min_id THEN k.namakaryawan END) as namakaryawan,
                MIN(b.id) as first_booking_id,
                t.no_transaksi,
                t.status_pembayaran,
                t.bukti_pembayaran,
                t.total_harga as total_harga_transaksi,
                t.metode_pembayaran,
                t.created_at as tanggal_transaksi,
                t.id as transaksi_id
            FROM booking b
            INNER JOIN (
                SELECT kode_booking, MIN(id) as min_id
                FROM booking 
                WHERE kode_booking IS NOT NULL
                GROUP BY kode_booking
            ) min_booking ON b.kode_booking = min_booking.kode_booking
            LEFT JOIN pelanggan p ON b.pelanggan_id = p.kode_pelanggan
            LEFT JOIN layanan l ON b.layanan_id = l.kode_layanan
            LEFT JOIN karyawan k ON b.id_karyawan = k.idkaryawan
            LEFT JOIN transaksi t ON t.booking_id = min_booking.min_id
            WHERE b.kode_booking IS NOT NULL
            GROUP BY b.kode_booking, 
                     t.no_transaksi, t.status_pembayaran, t.bukti_pembayaran, 
                     t.total_harga, t.metode_pembayaran, t.created_at, t.id
            ORDER BY MAX(b.created_at) DESC
        ")->getResultArray();

        // Process the data to get proper structure
        $transactions = [];
        foreach ($allBookings as $booking) {
            $transactions[] = [
                'kode_booking' => $booking['kode_booking'],
                'booking_status' => $booking['booking_status'],
                'tanggal_booking' => $booking['tanggal_booking'],
                'jam_booking' => $booking['jam_booking'],
                'created_at' => $booking['created_at'],
                'nama_pelanggan' => $booking['nama_pelanggan'],
                'layanan_list' => $booking['layanan_list'],
                'jumlah_layanan' => $booking['jumlah_layanan'],
                'namakaryawan' => $booking['namakaryawan'],
                // Transaction data (null if no transaction)
                'no_transaksi' => $booking['no_transaksi'],
                'status_pembayaran' => $booking['status_pembayaran'] ?? 'belum_bayar',
                'bukti_pembayaran' => $booking['bukti_pembayaran'],
                'total_harga' => $booking['total_harga_transaksi'] ?? $booking['total_harga_layanan'],
                'metode_pembayaran' => $booking['metode_pembayaran'] ?? '-',
                'tanggal_transaksi' => $booking['tanggal_transaksi'] ?? $booking['created_at'],
                'transaksi_id' => $booking['transaksi_id']
            ];
        }

        // Get statistics
        $stats = [
            'total_bookings' => $this->bookingModel->countAll(),
            'pending_bookings' => $this->bookingModel->where('status', 'menunggu_konfirmasi')->countAllResults(),
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
            'layanan_ids' => 'required',
            'id_karyawan' => 'permit_empty',
            'catatan' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $layananIds = $this->request->getPost('layanan_ids');
        if (empty($layananIds) || !is_array($layananIds)) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal satu layanan');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $kodeBooking = $this->bookingModel->generateNewKodeBooking();
            $layananModel = new \App\Models\LayananModel();

            // Calculate total duration for all services
            $totalDurasi = 0;
            $validServices = [];
            foreach ($layananIds as $layananId) {
                $layanan = $this->layananModel->find($layananId);
                if ($layanan) {
                    $validServices[] = $layanan;
                    $totalDurasi += (int)$layanan['durasi_menit'];
                }
            }

            if (empty($validServices)) {
                throw new \Exception('Tidak ada layanan valid yang dipilih');
            }

            // Get ONE karyawan available for the entire booking duration
            $sharedKaryawan = $this->bookingModel->getRandomAvailableKaryawan(
                $this->request->getPost('tanggal'),
                $this->request->getPost('jam'),
                $totalDurasi
            );

            if (!$sharedKaryawan) {
                throw new \Exception('Tidak ada karyawan yang tersedia untuk menangani semua layanan pada waktu tersebut');
            }

            $idKaryawan = $sharedKaryawan['idkaryawan'];

            $totalHarga = 0;
            $firstBookingId = null;

            // Create booking for each valid service
            foreach ($validServices as $layanan) {
                $layananId = $layanan['kode_layanan'];

                $bookingData = [
                    'kode_booking' => $kodeBooking,
                    'pelanggan_id' => $this->request->getPost('pelanggan_id'),
                    'layanan_id' => $layananId,
                    'tanggal' => $this->request->getPost('tanggal'),
                    'jam' => $this->request->getPost('jam'),
                    'no_plat' => strtoupper($this->request->getPost('no_plat')),
                    'jenis_kendaraan' => $this->request->getPost('jenis_kendaraan'),
                    'merk_kendaraan' => $this->request->getPost('merk_kendaraan'),
                    'id_karyawan' => $idKaryawan,
                    'status' => 'dikonfirmasi', // Admin bookings are auto-confirmed
                    'catatan' => $this->request->getPost('catatan')
                ];

                $bookingId = $this->bookingModel->insert($bookingData);

                if (!$bookingId) {
                    throw new \Exception('Gagal menyimpan booking untuk layanan: ' . $layanan['nama_layanan']);
                }

                if ($firstBookingId === null) {
                    $firstBookingId = $bookingId;
                }

                $totalHarga += $layanan['harga'];
            }

            // Create transaction
            $transaksiModel = new \App\Models\TransaksiModel();

            // Generate no_transaksi
            $noTransaksi = 'TRX-' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999));

            $transaksiData = [
                'no_transaksi' => $noTransaksi,
                'tanggal' => date('Y-m-d'),
                'booking_id' => $firstBookingId,
                'pelanggan_id' => $this->request->getPost('pelanggan_id'),
                'layanan_id' => $layananIds[0], // Use first service
                'no_plat' => $this->request->getPost('no_plat'),
                'jenis_kendaraan' => $this->request->getPost('jenis_kendaraan'),
                'total_harga' => $totalHarga,
                'metode_pembayaran' => 'tunai', // Default for admin bookings
                'status_pembayaran' => 'dibayar', // Admin bookings are paid
                'catatan' => $this->request->getPost('catatan'),
                'user_id' => session()->get('user_id')
            ];

            $transaksiId = $transaksiModel->insert($transaksiData);

            if (!$transaksiId) {
                throw new \Exception('Gagal menyimpan transaksi');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            // Check if AJAX request
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Booking berhasil ditambahkan dengan ' . count($layananIds) . ' layanan'
                ]);
            }

            return redirect()->to('admin/booking')->with('success', 'Booking berhasil ditambahkan dengan ' . count($layananIds) . ' layanan');
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Admin booking creation failed: ' . $e->getMessage());

            // Check if AJAX request
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menambahkan booking: ' . $e->getMessage()
                ]);
            }

            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan booking: ' . $e->getMessage());
        }
    }

    /**
     * Show booking detail with payment info by booking ID
     */
    public function show($bookingId)
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get main booking with details
        $booking = $this->bookingModel->getBookingWithDetails($bookingId);

        if (!$booking) {
            return redirect()->to('admin/booking')->with('error', 'Booking tidak ditemukan');
        }

        // Get all bookings with same kode_booking (multi-service)
        $relatedBookings = [];
        if ($booking['kode_booking']) {
            $relatedBookings = $this->bookingModel->getBookingsByKodeBooking($booking['kode_booking']);
        }

        // Get antrian if exists
        $antrian = $this->antrianModel->where('booking_id', $bookingId)->first();

        // Get transaksi if exists (check all bookings with same kode_booking)
        $transaksi = null;
        if ($booking['kode_booking']) {
            // Find transaction for any booking with the same kode_booking
            $allRelatedBookings = $this->bookingModel->where('kode_booking', $booking['kode_booking'])->findAll();
            foreach ($allRelatedBookings as $relatedBooking) {
                $foundTransaksi = $this->transaksiModel->where('booking_id', $relatedBooking['id'])->first();
                if ($foundTransaksi) {
                    $transaksi = $foundTransaksi;
                    break; // Use the first transaction found for the booking group
                }
            }
        }

        // Add compatibility fields for admin view
        $booking['tanggal_booking'] = $booking['tanggal'];
        $booking['jam_booking'] = $booking['jam'];
        $booking['booking_status'] = $booking['status'];

        $data = [
            'title' => 'Detail Booking',
            'subtitle' => 'Informasi lengkap booking dan pembayaran',
            'booking' => $booking,
            'relatedBookings' => $relatedBookings,
            'antrian' => $antrian,
            'transaksi' => $transaksi
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

        // Get all bookings with the same kode_booking (for multi-service)
        $allBookings = $this->bookingModel->where('kode_booking', $booking['kode_booking'])->findAll();

        // Get services for all bookings with the same kode_booking
        $bookingServices = [];
        foreach ($allBookings as $b) {
            $layanan = $this->layananModel->where('kode_layanan', $b['layanan_id'])->first();
            if ($layanan) {
                $bookingServices[] = $layanan;
            }
        }

        // Get related data for form
        $pelanggan = $this->pelangganModel->findAll();
        $layanan = $this->layananModel->findAll();
        $karyawan = $this->karyawanModel->findAll();

        $data = [
            'title' => 'Edit Booking',
            'subtitle' => 'Ubah data booking',
            'booking' => $booking,
            'booking_services' => $bookingServices,
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
            'id_karyawan' => 'permit_empty',
            'status' => 'required|in_list[menunggu_konfirmasi,dikonfirmasi,selesai,dibatalkan]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get selected services
        $layananIds = $this->request->getPost('layanan_ids');
        if (empty($layananIds)) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal satu layanan');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Delete existing bookings with the same kode_booking
            $this->bookingModel->where('kode_booking', $booking['kode_booking'])->delete();

            // Get total duration for all selected services
            $totalDurasi = 0;
            $validServices = [];
            foreach ($layananIds as $layananId) {
                $layanan = $this->layananModel->where('kode_layanan', $layananId)->first();
                if ($layanan) {
                    $validServices[] = $layanan;
                    $totalDurasi += (int)$layanan['durasi_menit'];
                }
            }

            if (empty($validServices)) {
                throw new \Exception('Tidak ada layanan valid yang dipilih');
            }

            // Get or assign karyawan
            $idKaryawan = $this->request->getPost('id_karyawan');
            if (empty($idKaryawan)) {
                $tanggal = $this->request->getPost('tanggal');
                $jam = $this->request->getPost('jam');
                $availableKaryawan = $this->bookingModel->getRandomAvailableKaryawan($tanggal, $jam, $totalDurasi);
                $idKaryawan = $availableKaryawan ? $availableKaryawan['idkaryawan'] : null;
            }

            // Calculate start time in minutes
            list($hours, $minutes) = explode(':', $this->request->getPost('jam'));
            $startTimeMinutes = ($hours * 60) + $minutes;

            // Create new bookings for each selected service
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

                $bookingData = [
                    'kode_booking' => $booking['kode_booking'], // Keep the same kode_booking
                    'pelanggan_id' => $this->request->getPost('pelanggan_id'),
                    'tanggal' => $this->request->getPost('tanggal'),
                    'jam' => $serviceJam,
                    'no_plat' => strtoupper($this->request->getPost('no_plat')),
                    'jenis_kendaraan' => $this->request->getPost('jenis_kendaraan'),
                    'merk_kendaraan' => $this->request->getPost('merk_kendaraan'),
                    'layanan_id' => $service['kode_layanan'],
                    'id_karyawan' => $idKaryawan,
                    'status' => $this->request->getPost('status'),
                    'catatan' => $this->request->getPost('catatan'),
                    'payment_expires_at' => $booking['payment_expires_at'], // Keep original expiry
                    'user_id' => $booking['user_id'] // Keep original user
                ];

                if (!$this->bookingModel->insert($bookingData)) {
                    throw new \Exception('Gagal menyimpan booking untuk layanan: ' . $service['nama_layanan']);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal memperbarui booking');
            }

            // Check if AJAX request
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Booking berhasil diperbarui'
                ]);
            }

            return redirect()->to('admin/booking')->with('success', 'Booking berhasil diperbarui');
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Update booking error: ' . $e->getMessage());

            // Check if AJAX request
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui booking: ' . $e->getMessage()
                ]);
            }

            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui booking: ' . $e->getMessage());
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
     * Confirm booking by kode_booking (for admin)
     */
    public function confirmBookingByCode($kodeBooking)
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akses ditolak'
            ]);
        }

        // Get all bookings with this kode_booking
        $bookings = $this->bookingModel->where('kode_booking', $kodeBooking)->findAll();

        if (empty($bookings)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking tidak ditemukan'
            ]);
        }

        // Check if booking is still pending
        $firstBooking = $bookings[0];
        if ($firstBooking['status'] !== 'menunggu_konfirmasi') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking sudah dikonfirmasi atau dibatalkan sebelumnya'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update all bookings with same kode_booking to confirmed
            $this->bookingModel->where('kode_booking', $kodeBooking)
                ->set(['status' => 'dikonfirmasi'])
                ->update();

            // Create antrian for the first booking (representing the whole booking session)
            $antrianData = [
                'booking_id' => $firstBooking['id'],
                'tanggal' => $firstBooking['tanggal'],
                'status' => 'menunggu'
            ];

            if ($this->antrianModel->insert($antrianData)) {
                $antrianId = $this->antrianModel->getInsertID();
                $antrian = $this->antrianModel->find($antrianId);
                log_message('info', 'Antrian created for booking ' . $kodeBooking . ': ' . $antrian['nomor_antrian']);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            log_message('info', "Admin confirmed booking: {$kodeBooking}");

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
     * Reject booking by kode_booking (for admin)
     */
    public function rejectBookingByCode($kodeBooking)
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akses ditolak'
            ]);
        }

        // Get all bookings with this kode_booking
        $bookings = $this->bookingModel->where('kode_booking', $kodeBooking)->findAll();

        if (empty($bookings)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking tidak ditemukan'
            ]);
        }

        // Check if booking is still pending
        $firstBooking = $bookings[0];
        if ($firstBooking['status'] !== 'menunggu_konfirmasi') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking sudah diproses sebelumnya'
            ]);
        }

        $alasan = $this->request->getPost('alasan') ?? 'Booking ditolak oleh admin';

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update all bookings with same kode_booking to rejected
            $this->bookingModel->where('kode_booking', $kodeBooking)
                ->set([
                    'status' => 'dibatalkan',
                    'catatan' => $alasan
                ])
                ->update();

            // Also update related transaction if exists
            $transaksiModel = new \App\Models\TransaksiModel();
            $transaksi = $transaksiModel->where('booking_id', $firstBooking['id'])->first();

            if ($transaksi) {
                $transaksiModel->update($transaksi['id'], [
                    'status_pembayaran' => 'batal',
                    'catatan' => $alasan
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            log_message('info', "Admin rejected booking: {$kodeBooking}. Reason: {$alasan}");

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Booking berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Booking rejection failed: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menolak booking: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get transaction by kode_booking (for receipt access from history)
     */
    public function getTransaction($kodeBooking)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }

        // Get any booking with this kode_booking to find related transaction
        $booking = $this->bookingModel->where('kode_booking', $kodeBooking)->first();

        if (!$booking) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking tidak ditemukan'
            ]);
        }

        // Find transaction for this booking
        $transaksi = $this->transaksiModel->where('booking_id', $booking['id'])->first();

        if ($transaksi) {
            return $this->response->setJSON([
                'status' => 'success',
                'no_transaksi' => $transaksi['no_transaksi'],
                'booking_id' => $booking['id']
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan',
                'booking_id' => $booking['id']
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

    /**
     * Laporan Booking Pertanggan
     */
    public function laporan()
    {
        // Check admin/pimpinan permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get filter parameters
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $tanggal_filter = $this->request->getGet('tanggal');

        // Build query for booking data with proper GROUP BY compliance
        $builder = $this->db->table('booking b');
        $builder->select('
            b.kode_booking,
            MIN(b.pelanggan_id) as pelanggan_id,
            MIN(p.nama_pelanggan) as nama_pelanggan,
            MIN(b.tanggal) as tanggal,
            MIN(b.jam) as jam,
            MIN(b.jenis_kendaraan) as jenis_kendaraan,
            MIN(b.merk_kendaraan) as merk_kendaraan,
            MIN(b.no_plat) as no_plat,
            GROUP_CONCAT(DISTINCT l.nama_layanan ORDER BY l.nama_layanan SEPARATOR ", ") as layanan,
            MIN(b.status) as status,
            MIN(k.namakaryawan) as namakaryawan,
            MIN(b.id) as first_booking_id
        ');
        $builder->join('pelanggan p', 'p.kode_pelanggan = b.pelanggan_id', 'left');
        $builder->join('layanan l', 'l.kode_layanan = b.layanan_id', 'left');
        $builder->join('karyawan k', 'k.idkaryawan = b.id_karyawan', 'left');
        $builder->where('b.kode_booking IS NOT NULL');

        // Apply date filters
        if ($tanggal_filter) {
            $builder->where('b.tanggal', $tanggal_filter);
        } else {
            $builder->where('MONTH(b.tanggal)', $bulan);
            $builder->where('YEAR(b.tanggal)', $tahun);
        }

        $builder->groupBy('b.kode_booking');
        $builder->orderBy('MIN(b.tanggal)', 'ASC');
        $builder->orderBy('MIN(b.jam)', 'ASC');

        $bookings = $builder->get()->getResultArray();

        // Prepare data for view
        $data = [
            'title' => 'Laporan Booking Pertanggan',
            'subtitle' => 'Laporan booking pelanggan untuk admin dan pimpinan',
            'active' => 'laporan-booking',
            'bookings' => $bookings,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'tanggal_filter' => $tanggal_filter,
            'total_booking' => count($bookings),
            'nama_bulan' => [
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember'
            ]
        ];

        return view('admin/booking/laporan', $data);
    }

    /**
     * Export Laporan Booking ke PDF
     */
    public function exportPDF()
    {
        // Check admin/pimpinan permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get filter parameters
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $tanggal_filter = $this->request->getGet('tanggal');

        // Build query for booking data with proper GROUP BY compliance
        $builder = $this->db->table('booking b');
        $builder->select('
            b.kode_booking,
            MIN(b.pelanggan_id) as pelanggan_id,
            MIN(p.nama_pelanggan) as nama_pelanggan,
            MIN(b.tanggal) as tanggal,
            MIN(b.jam) as jam,
            MIN(b.jenis_kendaraan) as jenis_kendaraan,
            MIN(b.merk_kendaraan) as merk_kendaraan,
            MIN(b.no_plat) as no_plat,
            GROUP_CONCAT(DISTINCT l.nama_layanan ORDER BY l.nama_layanan SEPARATOR ", ") as layanan,
            MIN(b.status) as status,
            MIN(k.namakaryawan) as namakaryawan,
            MIN(b.id) as first_booking_id
        ');
        $builder->join('pelanggan p', 'p.kode_pelanggan = b.pelanggan_id', 'left');
        $builder->join('layanan l', 'l.kode_layanan = b.layanan_id', 'left');
        $builder->join('karyawan k', 'k.idkaryawan = b.id_karyawan', 'left');
        $builder->where('b.kode_booking IS NOT NULL');

        // Apply date filters
        if ($tanggal_filter) {
            $builder->where('b.tanggal', $tanggal_filter);
        } else {
            $builder->where('MONTH(b.tanggal)', $bulan);
            $builder->where('YEAR(b.tanggal)', $tahun);
        }

        $builder->groupBy('b.kode_booking');
        $builder->orderBy('MIN(b.tanggal)', 'ASC');
        $builder->orderBy('MIN(b.jam)', 'ASC');

        $bookings = $builder->get()->getResultArray();

        // Prepare data for PDF
        $data = [
            'bookings' => $bookings,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'tanggal_filter' => $tanggal_filter,
            'total_booking' => count($bookings),
            'nama_bulan' => [
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember'
            ]
        ];

        // Generate PDF
        require_once ROOTPATH . 'vendor/autoload.php';

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml(view('admin/booking/laporan_pdf', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Set filename
        if ($tanggal_filter) {
            $filename = 'Laporan_Booking_' . date('d-m-Y', strtotime($tanggal_filter)) . '.pdf';
        } else {
            $filename = 'Laporan_Booking_' . $data['nama_bulan'][$bulan] . '_' . $tahun . '.pdf';
        }

        // Output PDF
        $dompdf->stream($filename, array('Attachment' => false));
    }

    /**
     * Laporan Booking Perbulan (Data Booking PerBulan)
     */
    public function laporanPerbulan()
    {
        // Check admin/pimpinan permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get filter parameters
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Build query untuk laporan perbulan
        $builder = $this->db->table('booking b');
        $builder->select('
            b.kode_booking,
            MIN(b.pelanggan_id) as idpelanggan,
            MIN(p.nama_pelanggan) as nama_pelanggan,
            MIN(b.tanggal) as tanggal,
            MIN(b.jam) as jam,
            MIN(b.no_plat) as noplat,
            MIN(b.jenis_kendaraan) as jenis_kendaraan,
            MIN(b.merk_kendaraan) as merk_kendaraan,
            GROUP_CONCAT(DISTINCT l.nama_layanan ORDER BY l.nama_layanan SEPARATOR ", ") as layanan,
            MIN(b.status) as status
        ');
        $builder->join('pelanggan p', 'p.kode_pelanggan = b.pelanggan_id', 'left');
        $builder->join('layanan l', 'l.kode_layanan = b.layanan_id', 'left');
        $builder->where('b.kode_booking IS NOT NULL');
        $builder->where('MONTH(b.tanggal)', $bulan);
        $builder->where('YEAR(b.tanggal)', $tahun);
        $builder->groupBy('b.kode_booking');
        $builder->orderBy('MIN(b.tanggal)', 'ASC');
        $builder->orderBy('MIN(b.jam)', 'ASC');

        $bookings = $builder->get()->getResultArray();

        // Prepare data for view
        $data = [
            'title' => 'Laporan Data Booking PerBulan',
            'subtitle' => 'Laporan booking pelanggan perbulan untuk admin dan pimpinan',
            'active' => 'laporan-booking-perbulan',
            'bookings' => $bookings,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'total_booking' => count($bookings),
            'nama_bulan' => [
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember'
            ]
        ];

        return view('admin/booking/laporan_perbulan', $data);
    }

    /**
     * Export Laporan Perbulan ke PDF
     */
    public function exportPerbulanPDF()
    {
        // Check admin/pimpinan permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get filter parameters
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Build query untuk laporan perbulan
        $builder = $this->db->table('booking b');
        $builder->select('
            b.kode_booking,
            MIN(b.pelanggan_id) as idpelanggan,
            MIN(p.nama_pelanggan) as nama_pelanggan,
            MIN(b.tanggal) as tanggal,
            MIN(b.jam) as jam,
            MIN(b.no_plat) as noplat,
            MIN(b.jenis_kendaraan) as jenis_kendaraan,
            MIN(b.merk_kendaraan) as merk_kendaraan,
            GROUP_CONCAT(DISTINCT l.nama_layanan ORDER BY l.nama_layanan SEPARATOR ", ") as layanan,
            MIN(b.status) as status
        ');
        $builder->join('pelanggan p', 'p.kode_pelanggan = b.pelanggan_id', 'left');
        $builder->join('layanan l', 'l.kode_layanan = b.layanan_id', 'left');
        $builder->where('b.kode_booking IS NOT NULL');
        $builder->where('MONTH(b.tanggal)', $bulan);
        $builder->where('YEAR(b.tanggal)', $tahun);
        $builder->groupBy('b.kode_booking');
        $builder->orderBy('MIN(b.tanggal)', 'ASC');
        $builder->orderBy('MIN(b.jam)', 'ASC');

        $bookings = $builder->get()->getResultArray();

        // Prepare data for PDF
        $data = [
            'bookings' => $bookings,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'total_booking' => count($bookings),
            'nama_bulan' => [
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember'
            ]
        ];

        // Generate PDF
        require_once ROOTPATH . 'vendor/autoload.php';

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml(view('admin/booking/laporan_perbulan_pdf', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Set filename
        $filename = 'Laporan_Booking_PerBulan_' . $data['nama_bulan'][$bulan] . '_' . $tahun . '.pdf';

        // Output PDF
        $dompdf->stream($filename, array('Attachment' => false));
    }
}
