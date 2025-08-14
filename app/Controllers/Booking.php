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

    public function create()
    {

        if (!session()->get('logged_in') || session()->get('role') !== 'pelanggan') {
            return redirect()->to('auth')->with('error', 'Silakan login sebagai pelanggan terlebih dahulu.');
        }


        $userId = session()->get('user_id');
        $pelanggan = $this->pelangganModel->where('user_id', $userId)->first();

        if (!$pelanggan) {
            return redirect()->to('pelanggan/dashboard')->with('error', 'Data pelanggan tidak ditemukan.');
        }


        $layananList = $this->layananModel->where('status', 'aktif')->findAll();

        $data = [
            'title' => 'Booking Layanan',
            'subtitle' => 'Buat booking layanan cuci kendaraan',
            'pelanggan' => $pelanggan,
            'layanan_list' => $layananList
        ];

        return view('pelanggan/booking/create', $data);
    }

    public function store()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }


        if (!session()->get('logged_in') || session()->get('role') !== 'pelanggan') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ]);
        }


        $userId = session()->get('user_id');
        $pelanggan = $this->pelangganModel->where('user_id', $userId)->first();

        if (!$pelanggan) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data pelanggan tidak ditemukan'
            ]);
        }


        $layananId = $this->request->getPost('layanan_id');
        $layanan = $this->layananModel->find($layananId);
        if (!$layanan) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Layanan tidak ditemukan'
            ]);
        }

        $jenisKendaraan = $layanan['jenis_kendaraan'];


        $rules = [
            'layanan_id' => 'required',
            'tanggal' => 'required|valid_date',
            'jam' => 'required',
            'catatan' => 'permit_empty'
        ];


        if ($jenisKendaraan === 'motor') {
            $rules['no_plat_motor'] = 'required|max_length[20]';
            $rules['merk_motor'] = 'permit_empty|max_length[50]';
        } elseif ($jenisKendaraan === 'mobil') {
            $rules['no_plat_mobil'] = 'required|max_length[20]';
            $rules['merk_mobil'] = 'permit_empty|max_length[50]';
        } elseif ($jenisKendaraan === 'lainnya') {
            $rules['no_plat_lainnya'] = 'required|max_length[20]';
            $rules['merk_lainnya'] = 'permit_empty|max_length[50]';
        }


        $namaLayanan = strtolower($layanan['nama_layanan']);
        $isComboPackage = strpos($namaLayanan, 'combo') !== false ||
            strpos($namaLayanan, 'paket') !== false ||
            strpos($namaLayanan, 'motor + mobil') !== false ||
            strpos($namaLayanan, 'motor & mobil') !== false ||
            strpos($namaLayanan, 'motor dan mobil') !== false ||
            strpos($namaLayanan, 'all in one') !== false ||
            strpos($namaLayanan, 'lengkap') !== false;

        if ($isComboPackage) {

            $rules['no_plat_motor'] = 'required|max_length[20]';
            $rules['merk_motor'] = 'permit_empty|max_length[50]';
            $rules['no_plat_mobil'] = 'required|max_length[20]';
            $rules['merk_mobil'] = 'permit_empty|max_length[50]';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak valid',
                'errors' => $this->validator->getErrors()
            ]);
        }


        $tanggal = $this->request->getPost('tanggal');
        $jam = $this->request->getPost('jam');

        if (!$this->bookingModel->checkSlotAvailability($tanggal, $jam, $jenisKendaraan)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Maaf, slot pada tanggal dan jam tersebut sudah penuh. Silakan pilih waktu lain.'
            ]);
        }


        $vehicleData = $this->prepareVehicleData($jenisKendaraan, $layanan['nama_layanan']);

        if (!$vehicleData) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data kendaraan tidak lengkap'
            ]);
        }


        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $bookingIds = [];


            foreach ($vehicleData as $vehicle) {
                $bookingData = [
                    'pelanggan_id' => $pelanggan['kode_pelanggan'],
                    'tanggal' => $tanggal,
                    'jam' => $jam,
                    'no_plat' => strtoupper($vehicle['no_plat']),
                    'merk_kendaraan' => $vehicle['merk_kendaraan'],
                    'layanan_id' => $layananId,
                    'status' => 'menunggu_konfirmasi',
                    'catatan' => $this->request->getPost('catatan'),
                    'user_id' => $userId
                ];

                if ($this->bookingModel->insert($bookingData)) {
                    $bookingIds[] = $this->bookingModel->getInsertID();
                } else {
                    throw new \Exception('Gagal menyimpan booking untuk kendaraan: ' . $vehicle['no_plat']);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }


            $firstBooking = $this->bookingModel->find($bookingIds[0]);

            $message = count($bookingIds) > 1
                ? 'Booking berhasil dibuat untuk ' . count($bookingIds) . ' kendaraan! Kode booking utama: ' . $firstBooking['kode_booking']
                : 'Booking berhasil dibuat! Kode booking Anda: ' . $firstBooking['kode_booking'];

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $message,
                'data' => [
                    'booking_ids' => $bookingIds,
                    'kode_booking' => $firstBooking['kode_booking'],
                    'redirect' => site_url('pelanggan/booking/detail/' . $bookingIds[0])
                ]
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Booking creation failed: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal membuat booking: ' . $e->getMessage()
            ]);
        }
    }

    private function prepareVehicleData($jenisKendaraan, $namaLayanan = '')
    {
        $vehicleData = [];


        $isComboPackage = false;
        if (!empty($namaLayanan)) {
            $namaLayananLower = strtolower($namaLayanan);
            $isComboPackage = strpos($namaLayananLower, 'combo') !== false ||
                strpos($namaLayananLower, 'paket') !== false ||
                strpos($namaLayananLower, 'motor + mobil') !== false ||
                strpos($namaLayananLower, 'motor & mobil') !== false ||
                strpos($namaLayananLower, 'motor dan mobil') !== false ||
                strpos($namaLayananLower, 'all in one') !== false ||
                strpos($namaLayananLower, 'lengkap') !== false;
        }

        if ($isComboPackage) {

            $noPlatMotor = $this->request->getPost('no_plat_motor');
            $merkMotor = $this->request->getPost('merk_motor');
            $noPlatMobil = $this->request->getPost('no_plat_mobil');
            $merkMobil = $this->request->getPost('merk_mobil');

            if (!empty($noPlatMotor)) {
                $vehicleData[] = [
                    'no_plat' => $noPlatMotor,
                    'merk_kendaraan' => $merkMotor ?: ''
                ];
            }

            if (!empty($noPlatMobil)) {
                $vehicleData[] = [
                    'no_plat' => $noPlatMobil,
                    'merk_kendaraan' => $merkMobil ?: ''
                ];
            }
        } elseif ($jenisKendaraan === 'motor') {
            $noPlatMotor = $this->request->getPost('no_plat_motor');
            $merkMotor = $this->request->getPost('merk_motor');

            if (!empty($noPlatMotor)) {
                $vehicleData[] = [
                    'no_plat' => $noPlatMotor,
                    'merk_kendaraan' => $merkMotor ?: ''
                ];
            }
        } elseif ($jenisKendaraan === 'mobil') {
            $noPlatMobil = $this->request->getPost('no_plat_mobil');
            $merkMobil = $this->request->getPost('merk_mobil');

            if (!empty($noPlatMobil)) {
                $vehicleData[] = [
                    'no_plat' => $noPlatMobil,
                    'merk_kendaraan' => $merkMobil ?: ''
                ];
            }
        } elseif ($jenisKendaraan === 'lainnya') {
            $noPlatLainnya = $this->request->getPost('no_plat_lainnya');
            $merkLainnya = $this->request->getPost('merk_lainnya');

            if (!empty($noPlatLainnya)) {
                $vehicleData[] = [
                    'no_plat' => $noPlatLainnya,
                    'merk_kendaraan' => $merkLainnya ?: ''
                ];
            }
        }

        return count($vehicleData) > 0 ? $vehicleData : null;
    }

    private function prepareVehicleDataForPublic($hasComboPackage, $uniqueVehicleTypes)
    {
        $vehicleData = [];

        if ($hasComboPackage) {

            $noPlatMotor = $this->request->getPost('no_plat_motor');
            $merkMotor = $this->request->getPost('merk_motor');
            $noPlatMobil = $this->request->getPost('no_plat_mobil');
            $merkMobil = $this->request->getPost('merk_mobil');

            if (!empty($noPlatMotor)) {
                $vehicleData[] = [
                    'no_plat' => $noPlatMotor,
                    'merk_kendaraan' => $merkMotor ?: ''
                ];
            }

            if (!empty($noPlatMobil)) {
                $vehicleData[] = [
                    'no_plat' => $noPlatMobil,
                    'merk_kendaraan' => $merkMobil ?: ''
                ];
            }
        } else {

            foreach ($uniqueVehicleTypes as $type) {
                if ($type === 'motor') {
                    $noPlatMotor = $this->request->getPost('no_plat_motor');
                    $merkMotor = $this->request->getPost('merk_motor');

                    if (!empty($noPlatMotor)) {
                        $vehicleData[] = [
                            'no_plat' => $noPlatMotor,
                            'merk_kendaraan' => $merkMotor ?: ''
                        ];
                    }
                } elseif ($type === 'mobil') {
                    $noPlatMobil = $this->request->getPost('no_plat_mobil');
                    $merkMobil = $this->request->getPost('merk_mobil');

                    if (!empty($noPlatMobil)) {
                        $vehicleData[] = [
                            'no_plat' => $noPlatMobil,
                            'merk_kendaraan' => $merkMobil ?: ''
                        ];
                    }
                } elseif ($type === 'lainnya') {
                    $noPlatLainnya = $this->request->getPost('no_plat_lainnya');
                    $merkLainnya = $this->request->getPost('merk_lainnya');

                    if (!empty($noPlatLainnya)) {
                        $vehicleData[] = [
                            'no_plat' => $noPlatLainnya,
                            'merk_kendaraan' => $merkLainnya ?: ''
                        ];
                    }
                }
            }
        }

        return count($vehicleData) > 0 ? $vehicleData : null;
    }

    public function storePublic()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }


        $selectedServicesJson = $this->request->getPost('selected_services');
        $selectedServices = json_decode($selectedServicesJson, true);

        if (!$selectedServices || !is_array($selectedServices) || empty($selectedServices)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Layanan harus dipilih'
            ]);
        }


        $serviceNames = [];
        $vehicleTypes = [];
        foreach ($selectedServices as $kodeLayanan) {
            $service = $this->layananModel->find($kodeLayanan);
            if ($service) {
                $serviceNames[] = $service['nama_layanan'];
                $vehicleTypes[] = $service['jenis_kendaraan'];
            }
        }


        $hasComboPackage = false;
        foreach ($serviceNames as $namaLayanan) {
            $namaLayananLower = strtolower($namaLayanan);
            if (
                strpos($namaLayananLower, 'combo') !== false ||
                strpos($namaLayananLower, 'paket') !== false ||
                strpos($namaLayananLower, 'motor + mobil') !== false ||
                strpos($namaLayananLower, 'motor & mobil') !== false ||
                strpos($namaLayananLower, 'motor dan mobil') !== false ||
                strpos($namaLayananLower, 'all in one') !== false ||
                strpos($namaLayananLower, 'lengkap') !== false
            ) {
                $hasComboPackage = true;
                break;
            }
        }


        $rules = [
            'selected_services' => 'required',
            'total_durasi' => 'required|numeric',
            'total_harga' => 'required|numeric',
            'tanggal' => 'required|valid_date',
            'jam' => 'required',
            'catatan' => 'permit_empty'
        ];


        $uniqueVehicleTypes = array_unique($vehicleTypes);
        if ($hasComboPackage) {

            $rules['no_plat_motor'] = 'required|max_length[20]';
            $rules['merk_motor'] = 'permit_empty|max_length[50]';
            $rules['no_plat_mobil'] = 'required|max_length[20]';
            $rules['merk_mobil'] = 'permit_empty|max_length[50]';
        } else {

            foreach ($uniqueVehicleTypes as $type) {
                if ($type === 'motor') {
                    $rules['no_plat_motor'] = 'required|max_length[20]';
                    $rules['merk_motor'] = 'permit_empty|max_length[50]';
                } elseif ($type === 'mobil') {
                    $rules['no_plat_mobil'] = 'required|max_length[20]';
                    $rules['merk_mobil'] = 'permit_empty|max_length[50]';
                } elseif ($type === 'lainnya') {
                    $rules['no_plat_lainnya'] = 'required|max_length[20]';
                    $rules['merk_lainnya'] = 'permit_empty|max_length[50]';
                }
            }
        }


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


        $validServices = [];
        foreach ($selectedServices as $kodeLayanan) {

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


        $pelangganId = null;
        $userId = null;

        if ($isLoggedIn) {

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

            $pelangganData = [
                'nama_pelanggan' => $this->request->getPost('nama_pelanggan'),
                'no_hp' => $this->request->getPost('no_hp'),
                'alamat' => 'Guest booking - ' . date('Y-m-d H:i:s')
            ];


            $existingPelanggan = $this->pelangganModel->where('no_hp', $pelangganData['no_hp'])->first();
            if ($existingPelanggan) {
                $pelangganId = $existingPelanggan['kode_pelanggan'];
            } else {

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


        $tanggal = $this->request->getPost('tanggal');
        $jam = $this->request->getPost('jam');
        $totalDurasi = (int) $this->request->getPost('total_durasi');
        $jenisKendaraan = $this->request->getPost('jenis_kendaraan');


        list($hours, $minutes) = explode(':', $jam);
        $startTimeMinutes = ($hours * 60) + $minutes;
        $endTimeMinutes = $startTimeMinutes + $totalDurasi;


        if (!$this->bookingModel->checkSlotAvailabilityWithKaryawan($tanggal, $jam, $totalDurasi)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Tidak ada karyawan yang tersedia pada slot waktu tersebut. Silakan pilih waktu lain.'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {

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


            $sharedKaryawan = $this->bookingModel->getRandomAvailableKaryawan(
                $tanggal,
                $jam,
                $totalDurasi
            );

            if (!$sharedKaryawan) {
                throw new \Exception('Tidak ada karyawan yang tersedia untuk menangani semua layanan pada waktu tersebut');
            }

            log_message('info', "Assigned shared karyawan {$sharedKaryawan['namakaryawan']} (ID: {$sharedKaryawan['idkaryawan']}) for entire booking duration ({$totalDurasi} minutes)");


            $vehicleData = $this->prepareVehicleDataForPublic($hasComboPackage, $uniqueVehicleTypes);

            if (!$vehicleData || empty($vehicleData)) {
                throw new \Exception('Data kendaraan tidak valid atau tidak lengkap');
            }



            $bookingIds = [];
            $totalHarga = 0;


            $allNoPlat = [];
            $allMerkKendaraan = [];
            foreach ($vehicleData as $vehicle) {
                $allNoPlat[] = strtoupper($vehicle['no_plat']);
                if (!empty($vehicle['merk_kendaraan'])) {
                    $allMerkKendaraan[] = $vehicle['merk_kendaraan'];
                }
            }

            $combinedNoPlat = implode(', ', $allNoPlat);
            $combinedMerkKendaraan = implode(', ', $allMerkKendaraan);

            foreach ($validServices as $index => $service) {

                $serviceStartMinutes = $startTimeMinutes;
                if ($index > 0) {

                    for ($i = 0; $i < $index; $i++) {
                        $serviceStartMinutes += (int)$validServices[$i]['durasi_menit'];
                    }
                }

                $serviceJam = sprintf(
                    '%02d:%02d',
                    floor($serviceStartMinutes / 60),
                    $serviceStartMinutes % 60
                );


                $paymentExpires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

                $bookingData = [
                    'kode_booking' => $sharedKodeBooking, // Use shared kode_booking
                    'pelanggan_id' => $pelangganId,
                    'tanggal' => $tanggal,
                    'jam' => $serviceJam,
                    'no_plat' => $combinedNoPlat, // Combined vehicle plates
                    'merk_kendaraan' => $combinedMerkKendaraan, // Combined vehicle brands
                    'layanan_id' => $service['kode_layanan'],
                    'status' => 'menunggu_konfirmasi',
                    'payment_expires_at' => $paymentExpires,
                    'catatan' => $this->request->getPost('catatan'),
                    'user_id' => $userId,
                    'id_karyawan' => $sharedKaryawan['idkaryawan'] // Use SAME karyawan for all services
                ];

                log_message('info', "Using shared karyawan {$sharedKaryawan['namakaryawan']} (ID: {$sharedKaryawan['idkaryawan']}) for service {$service['nama_layanan']} at {$serviceJam} for combined vehicles: {$combinedNoPlat}");
                log_message('info', "Booking data to be inserted: " . json_encode($bookingData));

                if ($this->bookingModel->insert($bookingData)) {
                    $bookingIds[] = $this->bookingModel->getInsertID();
                    $totalHarga += (float)$service['harga'];
                    log_message('info', "Successfully inserted booking for service: {$service['nama_layanan']} for combined vehicles: {$combinedNoPlat}");
                } else {
                    $errors = $this->bookingModel->errors();
                    log_message('error', "Booking validation errors: " . json_encode($errors));
                    log_message('error', "Booking data that failed: " . json_encode($bookingData));
                    log_message('error', "Karyawan data: " . json_encode($sharedKaryawan));
                    throw new \Exception('Gagal menyimpan booking untuk layanan: ' . $service['nama_layanan'] . ' untuk kendaraan: ' . $combinedNoPlat . '. Errors: ' . json_encode($errors));
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

            $vehicleCount = count($vehicleData);
            $serviceCount = count($validServices);

            $successMessage = "Booking berhasil dibuat! Kode booking: {$sharedKodeBooking}";
            $successMessage .= "\n{$serviceCount} layanan untuk {$vehicleCount} kendaraan";
            $successMessage .= "\nJadwal: {$jam} hingga {$endTime}";
            $successMessage .= "\nKendaraan: {$combinedNoPlat}";
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

    public function detail($bookingId)
    {

        if (!session()->get('logged_in')) {
            return redirect()->to('auth');
        }

        $booking = $this->bookingModel->getBookingWithDetails($bookingId);

        if (!$booking) {
            return redirect()->to('pelanggan/dashboard')->with('error', 'Booking tidak ditemukan.');
        }


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


        $relatedBookings = [];
        if ($booking['kode_booking']) {
            $relatedBookings = $this->bookingModel->getBookingsByKodeBooking($booking['kode_booking']);
        }


        $antrian = $this->antrianModel->where('booking_id', $bookingId)->first();


        $transaksi = null;
        if ($booking['kode_booking']) {

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

    public function history()
    {

        if (!session()->get('logged_in') || session()->get('role') !== 'pelanggan') {
            return redirect()->to('auth');
        }

        $userId = session()->get('user_id');
        $pelanggan = $this->pelangganModel->where('user_id', $userId)->first();

        if (!$pelanggan) {
            return redirect()->to('pelanggan/dashboard')->with('error', 'Data pelanggan tidak ditemukan.');
        }


        $allBookings = $this->bookingModel->getBookingsByPelanggan($pelanggan['kode_pelanggan']);


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

    public function confirm($bookingId)
    {

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


        $db = \Config\Database::connect();
        $db->transStart();

        try {

            $this->bookingModel->update($bookingId, ['status' => 'dikonfirmasi']);


            $antrianData = [
                'booking_id' => $bookingId,
                'tanggal' => $booking['tanggal'],
                'status' => 'menunggu'
            ];

            if (!$this->antrianModel->insert($antrianData)) {
                $errors = $this->antrianModel->errors();
                log_message('error', 'Failed to create antrian: ' . json_encode($errors));
                throw new \Exception('Gagal membuat antrian: ' . json_encode($errors));
            }

            $antrianId = $this->antrianModel->getInsertID();
            $antrian = $this->antrianModel->find($antrianId);
            log_message('info', 'Antrian created for booking ' . $bookingId . ': ' . $antrian['nomor_antrian']);

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

    public function processPayment($bookingId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }


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


        $db = \Config\Database::connect();
        $db->transStart();

        try {

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

    public function cancel($bookingId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }


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


        if (in_array($booking['status'], ['selesai', 'dibatalkan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking dengan status ' . $booking['status'] . ' tidak dapat dibatalkan'
            ]);
        }

        try {

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

            $karyawanModel = new \App\Models\KaryawanModel();
            $totalKaryawan = $karyawanModel->countAll();


            $existingBookings = $this->bookingModel
                ->select('booking.jam, booking.layanan_id, booking.id_karyawan')
                ->join('layanan l', 'l.kode_layanan = booking.layanan_id', 'left')
                ->select('l.durasi_menit as durasi')
                ->where('booking.tanggal', $tanggal)
                ->where('booking.status !=', 'dibatalkan')
                ->where('booking.id_karyawan IS NOT NULL') // Only bookings with assigned karyawan
                ->findAll();


            $currentTime = date('H:i');
            $isToday = ($tanggal === date('Y-m-d'));


            if (empty($totalDurasi)) {

                $availableSlots = $this->generateSimpleAvailableSlots($existingBookings, $totalKaryawan, $isToday, $currentTime);


                $existingBookingsArray = [];
                foreach ($existingBookings as $booking) {

                    if (!empty($booking['id_karyawan']) && !empty($booking['jam'])) {
                        $existingBookingsArray[] = [
                            'jam' => $booking['jam'],
                            'layanan_id' => $booking['layanan_id'],
                            'id_karyawan' => $booking['id_karyawan'],
                            'durasi' => (int)($booking['durasi'] ?? 60)
                        ];
                    }
                }


                log_message('debug', "Processing date: $tanggal");
                log_message('debug', "Total karyawan: $totalKaryawan");
                log_message('debug', "Raw bookings: " . count($existingBookings));
                log_message('debug', "Valid bookings: " . count($existingBookingsArray));
                log_message('debug', "Available slots: " . count($availableSlots));



                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $availableSlots,

                    'existing_bookings' => $existingBookingsArray,
                    'total_karyawan' => (int)$totalKaryawan,
                    'is_today' => $isToday,
                    'current_time' => $isToday ? $currentTime : null
                ]);
            }


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

    private function generateSimpleAvailableSlots($existingBookings, $totalKaryawan, $isToday, $currentTime)
    {
        $startHour = 8;
        $endHour = 17;
        $availableSlots = [];




        for ($hour = $startHour; $hour < $endHour; $hour++) {
            $slots = [
                sprintf('%02d:00', $hour),
                sprintf('%02d:30', $hour)
            ];

            foreach ($slots as $slot) {

                if ($isToday && $currentTime && $slot <= $currentTime) {
                    continue;
                }


                $busyKaryawanSet = [];
                foreach ($existingBookings as $booking) {

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


                log_message('debug', "Slot $slot - busy karyawan: " . count($busyKaryawanSet) . ", available: $availableKaryawan");

                if ($availableKaryawan > 0) {
                    $availableSlots[] = $slot;
                }
            }
        }
        return $availableSlots;
    }

    private function addMinutesToTime($timeStr, $minutes)
    {
        $time = new \DateTime($timeStr);
        $time->add(new \DateInterval('PT' . $minutes . 'M'));
        return $time->format('H:i');
    }

    private function timesOverlap($start1, $end1, $start2, $end2)
    {
        return $start1 < $end2 && $end1 > $start2;
    }



    public function index()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }



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

                'no_transaksi' => $booking['no_transaksi'],
                'status_pembayaran' => $booking['status_pembayaran'] ?? 'belum_bayar',
                'bukti_pembayaran' => $booking['bukti_pembayaran'],
                'total_harga' => $booking['total_harga_transaksi'] ?? $booking['total_harga_layanan'],
                'metode_pembayaran' => $booking['metode_pembayaran'] ?? '-',
                'tanggal_transaksi' => $booking['tanggal_transaksi'] ?? $booking['created_at'],
                'transaksi_id' => $booking['transaksi_id']
            ];
        }


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

    public function adminCreate()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


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

    public function adminStore()
    {

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


            $transaksiModel = new \App\Models\TransaksiModel();


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


            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menambahkan booking: ' . $e->getMessage()
                ]);
            }

            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan booking: ' . $e->getMessage());
        }
    }

    public function show($bookingId)
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $booking = $this->bookingModel->getBookingWithDetails($bookingId);

        if (!$booking) {
            return redirect()->to('admin/booking')->with('error', 'Booking tidak ditemukan');
        }


        $relatedBookings = [];
        if ($booking['kode_booking']) {
            $relatedBookings = $this->bookingModel->getBookingsByKodeBooking($booking['kode_booking']);
        }


        $antrian = $this->antrianModel->where('booking_id', $bookingId)->first();


        $transaksi = null;
        if ($booking['kode_booking']) {

            $allRelatedBookings = $this->bookingModel->where('kode_booking', $booking['kode_booking'])->findAll();
            foreach ($allRelatedBookings as $relatedBooking) {
                $foundTransaksi = $this->transaksiModel->where('booking_id', $relatedBooking['id'])->first();
                if ($foundTransaksi) {
                    $transaksi = $foundTransaksi;
                    break; // Use the first transaction found for the booking group
                }
            }
        }


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

    public function edit($bookingId)
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        $booking = $this->bookingModel->getBookingWithDetails($bookingId);

        if (!$booking) {
            return redirect()->to('admin/booking')->with('error', 'Booking tidak ditemukan');
        }


        $allBookings = $this->bookingModel->where('kode_booking', $booking['kode_booking'])->findAll();


        $bookingServices = [];
        foreach ($allBookings as $b) {
            $layanan = $this->layananModel->where('kode_layanan', $b['layanan_id'])->first();
            if ($layanan) {
                $bookingServices[] = $layanan;
            }
        }


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

    public function update($bookingId)
    {

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
            'id_karyawan' => 'permit_empty',
            'status' => 'required|in_list[menunggu_konfirmasi,dikonfirmasi,selesai,dibatalkan]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }


        $layananIds = $this->request->getPost('layanan_ids');
        if (empty($layananIds)) {
            return redirect()->back()->withInput()->with('error', 'Pilih minimal satu layanan');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {

            $existingBookings = $this->bookingModel->where('kode_booking', $booking['kode_booking'])->findAll();


            $bookingsWithTransactions = [];
            $bookingsWithoutTransactions = [];

            foreach ($existingBookings as $existingBooking) {
                $hasTransaction = $this->db->table('transaksi')
                    ->where('booking_id', $existingBooking['id'])
                    ->countAllResults() > 0;

                if ($hasTransaction) {
                    $bookingsWithTransactions[] = $existingBooking;
                } else {
                    $bookingsWithoutTransactions[] = $existingBooking;
                }
            }


            foreach ($bookingsWithoutTransactions as $bookingToDelete) {
                $this->bookingModel->delete($bookingToDelete['id']);
            }


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


            $idKaryawan = $this->request->getPost('id_karyawan');
            if (empty($idKaryawan)) {
                $tanggal = $this->request->getPost('tanggal');
                $jam = $this->request->getPost('jam');
                $availableKaryawan = $this->bookingModel->getRandomAvailableKaryawan($tanggal, $jam, $totalDurasi);
                $idKaryawan = $availableKaryawan ? $availableKaryawan['idkaryawan'] : null;
            }


            list($hours, $minutes) = explode(':', $this->request->getPost('jam'));
            $startTimeMinutes = ($hours * 60) + $minutes;


            $updatedBookingCount = 0;

            foreach ($validServices as $index => $service) {

                $serviceStartMinutes = $startTimeMinutes;
                if ($index > 0) {

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
                    'merk_kendaraan' => $this->request->getPost('merk_kendaraan'),
                    'layanan_id' => $service['kode_layanan'],
                    'id_karyawan' => $idKaryawan,
                    'status' => $this->request->getPost('status'),
                    'catatan' => $this->request->getPost('catatan'),
                    'payment_expires_at' => $booking['payment_expires_at'], // Keep original expiry
                    'user_id' => $booking['user_id'] // Keep original user
                ];


                $updated = false;
                if ($updatedBookingCount < count($bookingsWithTransactions)) {
                    $existingBooking = $bookingsWithTransactions[$updatedBookingCount];
                    if ($this->bookingModel->update($existingBooking['id'], $bookingData)) {
                        $updated = true;
                        $updatedBookingCount++;
                    }
                }


                if (!$updated) {
                    if (!$this->bookingModel->insert($bookingData)) {
                        throw new \Exception('Gagal menyimpan booking untuk layanan: ' . $service['nama_layanan']);
                    }
                }
            }


            if ($updatedBookingCount < count($bookingsWithTransactions)) {

                for ($i = $updatedBookingCount; $i < count($bookingsWithTransactions); $i++) {
                    $remainingBooking = $bookingsWithTransactions[$i];
                    log_message('warning', "Booking ID {$remainingBooking['id']} has transaction but no corresponding service in update. Keeping existing data.");
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal memperbarui booking');
            }


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


            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui booking: ' . $e->getMessage()
                ]);
            }

            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui booking: ' . $e->getMessage());
        }
    }

    public function delete($bookingId)
    {

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

    public function approvePayment($transaksiId)
    {

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

            $transaksiModel->update($transaksiId, [
                'status_pembayaran' => 'dibayar',
                'updated_at' => date('Y-m-d H:i:s')
            ]);


            if ($transaksi['booking_id']) {
                $booking = $this->bookingModel->find($transaksi['booking_id']);
                if ($booking && is_array($booking)) {

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

    public function rejectPayment($transaksiId)
    {

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

            $transaksiModel->update($transaksiId, [
                'status_pembayaran' => 'batal',
                'catatan' => $alasan,
                'updated_at' => date('Y-m-d H:i:s')
            ]);


            if ($transaksi['booking_id']) {
                $booking = $this->bookingModel->find($transaksi['booking_id']);
                if ($booking && is_array($booking)) {

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

    public function confirmBookingByCode($kodeBooking)
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akses ditolak'
            ]);
        }


        $bookings = $this->bookingModel->where('kode_booking', $kodeBooking)->findAll();

        if (empty($bookings)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking tidak ditemukan'
            ]);
        }


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

            $this->bookingModel->where('kode_booking', $kodeBooking)
                ->set(['status' => 'dikonfirmasi'])
                ->update();


            $antrianData = [
                'booking_id' => $firstBooking['id'],
                'tanggal' => $firstBooking['tanggal'],
                'status' => 'menunggu'
            ];

            if (!$this->antrianModel->insert($antrianData)) {
                $errors = $this->antrianModel->errors();
                log_message('error', 'Failed to create antrian: ' . json_encode($errors));
                throw new \Exception('Gagal membuat antrian: ' . json_encode($errors));
            }

            $antrianId = $this->antrianModel->getInsertID();
            $antrian = $this->antrianModel->find($antrianId);
            log_message('info', 'Antrian created for booking ' . $kodeBooking . ': ' . $antrian['nomor_antrian']);

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

    public function rejectBookingByCode($kodeBooking)
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akses ditolak'
            ]);
        }


        $bookings = $this->bookingModel->where('kode_booking', $kodeBooking)->findAll();

        if (empty($bookings)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking tidak ditemukan'
            ]);
        }


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

            $this->bookingModel->where('kode_booking', $kodeBooking)
                ->set([
                    'status' => 'dibatalkan',
                    'catatan' => $alasan
                ])
                ->update();


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

    public function getTransaction($kodeBooking)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }


        $booking = $this->bookingModel->where('kode_booking', $kodeBooking)->first();

        if (!$booking) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking tidak ditemukan'
            ]);
        }


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

    public function deleteTransaction($transaksiId)
    {

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

            $booking = $this->bookingModel->find($transaksi['booking_id']);

            if ($booking && is_array($booking)) {

                $this->bookingModel->where('kode_booking', $booking['kode_booking'])->delete();
                log_message('info', "Deleted bookings with kode_booking: {$booking['kode_booking']}");
            }


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

    public function laporan()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $tanggal_filter = $this->request->getGet('tanggal');


        $builder = $this->db->table('booking b');
        $builder->select('
            b.kode_booking,
            MIN(b.pelanggan_id) as pelanggan_id,
            MIN(p.nama_pelanggan) as nama_pelanggan,
            MIN(b.tanggal) as tanggal,
            MIN(b.jam) as jam,
            MIN(l.jenis_kendaraan) as jenis_kendaraan,
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


        $data = [
            'title' => 'Laporan Booking Pertanggal',
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

    public function exportPDF()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $tanggal_filter = $this->request->getGet('tanggal');


        $builder = $this->db->table('booking b');
        $builder->select('
            b.kode_booking,
            MIN(b.pelanggan_id) as pelanggan_id,
            MIN(p.nama_pelanggan) as nama_pelanggan,
            MIN(b.tanggal) as tanggal,
            MIN(b.jam) as jam,
            MIN(l.jenis_kendaraan) as jenis_kendaraan,
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


        require_once ROOTPATH . 'vendor/autoload.php';
        require_once APPPATH . 'Helpers/PdfHelper.php';

        $html = view('admin/booking/laporan_pdf', $data);
        
        if ($tanggal_filter) {
            $filename = 'Laporan_Booking_' . date('d-m-Y', strtotime($tanggal_filter));
        } else {
            $filename = 'Laporan_Booking_' . $data['nama_bulan'][$bulan] . '_' . $tahun;
        }
        
        $pdfResult = \App\Helpers\PdfHelper::generatePdf($html, $filename, 'A4', 'landscape');
        
        return \App\Helpers\PdfHelper::streamPdf($pdfResult, false);
    }

    public function laporanPerbulan()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');


        $builder = $this->db->table('booking b');
        $builder->select('
            b.kode_booking,
            MIN(b.pelanggan_id) as idpelanggan,
            MIN(p.nama_pelanggan) as nama_pelanggan,
            MIN(b.tanggal) as tanggal,
            MIN(b.jam) as jam,
            MIN(b.no_plat) as noplat,
            MIN(l.jenis_kendaraan) as jenis_kendaraan,
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

    public function exportPerbulanPDF()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');


        $builder = $this->db->table('booking b');
        $builder->select('
            b.kode_booking,
            MIN(b.pelanggan_id) as idpelanggan,
            MIN(p.nama_pelanggan) as nama_pelanggan,
            MIN(b.tanggal) as tanggal,
            MIN(b.jam) as jam,
            MIN(b.no_plat) as noplat,
            MIN(l.jenis_kendaraan) as jenis_kendaraan,
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


        require_once ROOTPATH . 'vendor/autoload.php';
        require_once APPPATH . 'Helpers/PdfHelper.php';

        $html = view('admin/booking/laporan_perbulan_pdf', $data);
        $filename = 'Laporan_Booking_PerBulan_' . $data['nama_bulan'][$bulan] . '_' . $tahun;
        
        $pdfResult = \App\Helpers\PdfHelper::generatePdf($html, $filename, 'A4', 'landscape');
        
        return \App\Helpers\PdfHelper::streamPdf($pdfResult, false);
    }
}
