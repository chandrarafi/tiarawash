<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PelangganModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\BaseConnection;

class Pelanggan extends BaseController
{
    use ResponseTrait;

    protected $pelangganModel;
    protected $userModel;
    protected $db;

    public function __construct()
    {
        $this->pelangganModel = new PelangganModel();
        $this->userModel = new UserModel();
        $this->db = db_connect();


        $this->createPelangganTableIfNotExists();
    }

    private function createPelangganTableIfNotExists()
    {

        $tableExists = $this->db->tableExists('pelanggan');

        if (!$tableExists) {

            $this->db->query("
                CREATE TABLE IF NOT EXISTS `pelanggan` (
                    `kode_pelanggan` VARCHAR(10) PRIMARY KEY,
                    `user_id` INT UNSIGNED NULL,
                    `nama_pelanggan` VARCHAR(100) NOT NULL,
                    `no_hp` VARCHAR(15) NULL,
                    `alamat` TEXT NULL,
                    `created_at` DATETIME NULL,
                    `updated_at` DATETIME NULL
                ) ENGINE=InnoDB
            ");

            log_message('info', 'Tabel pelanggan berhasil dibuat.');
        }
    }

    public function dashboard()
    {

        $userId = session()->get('user_id');
        $userEmail = session()->get('email');
        $userName = session()->get('name');


        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);


        $bookingModel = new \App\Models\BookingModel();
        $transaksiModel = new \App\Models\TransaksiModel();
        $layananModel = new \App\Models\LayananModel();
        $antrianModel = new \App\Models\AntrianModel();


        $pelanggan = $this->pelangganModel->where('user_id', $userId)->first();
        $pelangganId = $pelanggan ? $pelanggan['kode_pelanggan'] : null;


        $stats = [
            'total_booking' => 0,
            'booking_bulan_ini' => 0,
            'total_transaksi' => 0,
            'rating_rata' => 4.8
        ];

        if ($pelangganId) {

            $stats['total_booking'] = $bookingModel->where('pelanggan_id', $pelangganId)->countAllResults();


            $currentMonth = date('Y-m');
            $stats['booking_bulan_ini'] = $bookingModel
                ->where('pelanggan_id', $pelangganId)
                ->like('tanggal', $currentMonth, 'after')
                ->countAllResults();


            $stats['total_transaksi'] = $transaksiModel
                ->join('booking', 'booking.id = transaksi.booking_id', 'left')
                ->where('booking.pelanggan_id', $pelangganId)
                ->countAllResults();
        }


        $recentBookings = [];
        if ($pelangganId) {
            $recentBookings = $bookingModel->getBookingWithDetails();
            $recentBookings = array_filter($recentBookings, function ($booking) use ($pelangganId) {
                return $booking['pelanggan_id'] == $pelangganId;
            });
            $recentBookings = array_slice($recentBookings, 0, 5); // Get last 5 bookings
        }


        $recentTransactions = [];
        if ($pelangganId) {

            $recentTransactions = $transaksiModel
                ->select('transaksi.*, booking.pelanggan_id, layanan.nama_layanan, layanan.jenis_kendaraan')
                ->join('booking', 'booking.id = transaksi.booking_id', 'left')
                ->join('layanan', 'layanan.kode_layanan = booking.layanan_id', 'left')
                ->where('booking.pelanggan_id', $pelangganId)
                ->orderBy('transaksi.created_at', 'DESC')
                ->limit(5)
                ->findAll();
        }


        $todayQueues = [];
        if ($pelangganId) {
            $today = date('Y-m-d');
            $todayQueues = $antrianModel->getAntrianByDate($today);

            $todayQueues = array_filter($todayQueues, function ($antrian) use ($pelangganId) {
                return $antrian['pelanggan_id'] == $pelangganId;
            });
        }


        $activeServices = $layananModel->where('status', 'aktif')->findAll();


        $recentActivities = [];


        foreach ($recentBookings as $booking) {
            $recentActivities[] = [
                'type' => 'booking',
                'title' => 'Booking ' . ucfirst($booking['jenis_kendaraan'] ?? 'kendaraan'),
                'description' => 'Booking untuk layanan ' . ($booking['nama_layanan'] ?? 'Unknown'),
                'time' => $booking['created_at'],
                'status' => $booking['status'],
                'icon' => 'calendar-check',
                'color' => $this->getStatusColor($booking['status'])
            ];
        }


        foreach ($recentTransactions as $transaction) {
            $recentActivities[] = [
                'type' => 'transaction',
                'title' => 'Pembayaran ' . ucfirst($transaction['jenis_kendaraan'] ?? 'kendaraan'),
                'description' => 'Pembayaran untuk ' . ($transaction['nama_layanan'] ?? 'layanan') . ' sebesar Rp ' . number_format($transaction['total_harga'], 0, ',', '.'),
                'time' => $transaction['created_at'],
                'status' => $transaction['status_pembayaran'],
                'icon' => 'credit-card',
                'color' => $this->getPaymentStatusColor($transaction['status_pembayaran'])
            ];
        }


        usort($recentActivities, function ($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });


        $recentActivities = array_slice($recentActivities, 0, 4);

        $data = [
            'title' => 'Dashboard',
            'subtitle' => 'Selamat datang di portal pelanggan TiaraWash',
            'user' => $user,
            'pelanggan' => $pelanggan,
            'totalBookings' => $stats['total_booking'] ?? 0,
            'pendingBookings' => $pelangganId ? $bookingModel->where('pelanggan_id', $pelangganId)->where('status', 'pending')->countAllResults() : 0,
            'completedBookings' => $pelangganId ? $bookingModel->where('pelanggan_id', $pelangganId)->where('status', 'selesai')->countAllResults() : 0,
            'totalSpent' => $pelangganId ? ($transaksiModel->selectSum('transaksi.total_harga')->join('booking', 'booking.id = transaksi.booking_id', 'left')->where('booking.pelanggan_id', $pelangganId)->where('transaksi.status_pembayaran', 'dibayar')->get()->getRow()->total_harga ?? 0) : 0,
            'recentBookings' => $recentBookings ?? [],
            'recentTransactions' => $recentTransactions ?? [],
            'activeServices' => $activeServices ?? [],
            'stats' => $stats ?? [],
            'recentActivities' => $recentActivities ?? [],
            'todayQueues' => $todayQueues ?? []
        ];

        return view('pelanggan/dashboard', $data);
    }

    private function getStatusColor($status)
    {
        switch (strtolower($status)) {
            case 'pending':
                return 'warning';
            case 'confirmed':
            case 'dikonfirmasi':
                return 'info';
            case 'in_progress':
            case 'diproses':
                return 'primary';
            case 'completed':
            case 'selesai':
                return 'success';
            case 'cancelled':
            case 'dibatalkan':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    private function getPaymentStatusColor($status)
    {
        switch (strtolower($status)) {
            case 'pending':
                return 'warning';
            case 'paid':
            case 'lunas':
            case 'success':
                return 'success';
            case 'failed':
            case 'gagal':
                return 'danger';
            case 'refunded':
            case 'dikembalikan':
                return 'info';
            default:
                return 'secondary';
        }
    }

    private function getGradientColors($color)
    {
        switch ($color) {
            case 'success':
                return '#4facfe 0%, #00f2fe 100%';
            case 'warning':
                return '#43e97b 0%, #38f9d7 100%';
            case 'danger':
                return '#fa709a 0%, #fee140 100%';
            case 'info':
                return '#667eea 0%, #764ba2 100%';
            case 'primary':
                return '#0088cc 0%, #00aaff 100%';
            default:
                return '#6c757d 0%, #495057 100%';
        }
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Pelanggan',
            'active' => 'pelanggan'
        ];
        return view('admin/pelanggan/index', $data);
    }

    public function create()
    {
        return view('admin/pelanggan/create', [
            'title' => 'Tambah Pelanggan'
        ]);
    }

    public function edit($kode = null)
    {
        if (!$kode) {
            return redirect()->to('admin/pelanggan')->with('error', 'Kode pelanggan tidak valid');
        }

        $pelanggan = $this->pelangganModel->find($kode);
        if (!$pelanggan) {
            return redirect()->to('admin/pelanggan')->with('error', 'Data pelanggan tidak ditemukan');
        }


        log_message('debug', 'Menampilkan halaman edit untuk pelanggan: ' . $kode);

        return view('admin/pelanggan/edit', [
            'title' => 'Edit Pelanggan',
            'kode_pelanggan' => $kode
        ]);
    }

    public function getPelanggan()
    {
        $request = $this->request;


        $start = $request->getGet('start') ?? 0;
        $length = $request->getGet('length') ?? 10;
        $search = $request->getGet('search')['value'] ?? '';
        $order = $request->getGet('order')[0] ?? null;

        $columns = [
            'pelanggan.kode_pelanggan',
            'pelanggan.nama_pelanggan',
            'users.email',
            'pelanggan.no_hp',
            'pelanggan.alamat',
        ];

        $builder = $this->pelangganModel->builder();
        $builder->select('pelanggan.*, users.email, users.username');
        $builder->join('users', 'users.id = pelanggan.user_id', 'left');


        if (!empty($search)) {
            $builder->groupStart()
                ->like('pelanggan.kode_pelanggan', $search)
                ->orLike('pelanggan.nama_pelanggan', $search)
                ->orLike('users.email', $search)
                ->orLike('pelanggan.no_hp', $search)
                ->orLike('pelanggan.alamat', $search)
                ->groupEnd();
        }


        $totalRecords = $builder->countAllResults(false);


        if ($order) {
            $columnIndex = $order['column'];
            $columnName = $columns[$columnIndex];
            $columnDir = $order['dir'];
            $builder->orderBy($columnName, $columnDir);
        } else {
            $builder->orderBy('pelanggan.kode_pelanggan', 'ASC');
        }


        $builder->limit($length, $start);

        $data = $builder->get()->getResultArray();


        $formattedData = [];
        foreach ($data as $row) {
            $formattedData[] = [
                'kode_pelanggan' => $row['kode_pelanggan'],
                'nama_pelanggan' => $row['nama_pelanggan'],
                'email' => $row['email'] ?? '<span class="text-muted">Tidak ada akun</span>',
                'no_hp' => $row['no_hp'],
                'alamat' => $row['alamat'],
            ];
        }

        return $this->respond([
            'draw' => intval($request->getGet('draw')),
            'recordsTotal' => $this->pelangganModel->countAll(),
            'recordsFiltered' => $totalRecords,
            'data' => $formattedData
        ]);
    }

    public function getNewKode()
    {
        $newKode = $this->pelangganModel->generateKode();
        return $this->respond(['status' => 'success', 'kode' => $newKode]);
    }

    public function getUsers()
    {
        $users = $this->userModel->where('role', 'pelanggan')->findAll();


        $userPelanggan = $this->pelangganModel->findColumn('user_id');
        $userPelanggan = $userPelanggan ?? [];

        $filteredUsers = array_filter($users, function ($user) use ($userPelanggan) {
            return !in_array($user['id'], $userPelanggan);
        });

        return $this->respond(['status' => 'success', 'data' => array_values($filteredUsers)]);
    }

    public function getByKode($kode)
    {
        $pelanggan = $this->pelangganModel->getPelangganWithUser($kode);

        if (!$pelanggan) {
            return $this->fail('Pelanggan tidak ditemukan', 404);
        }


        log_message('debug', 'Data pelanggan yang diambil: ' . json_encode($pelanggan));

        return $this->respond(['status' => 'success', 'data' => $pelanggan]);
    }

    public function save()
    {
        $data = $this->request->getPost();
        $createAccount = $this->request->getPost('create_account') === '1';


        $this->db->transBegin();

        try {

            if ($createAccount) {

                $userValidation = [
                    'username' => 'required|alpha_numeric_space|min_length[3]|is_unique[users.username]',
                    'email' => 'required|valid_email|is_unique[users.email]',
                    'password' => 'required|min_length[6]',
                ];

                if (!$this->validate($userValidation)) {
                    return $this->fail($this->validator->getErrors(), 400);
                }


                $userData = [
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'name' => $data['nama_pelanggan'],
                    'role' => 'pelanggan',
                    'status' => 'active'
                ];


                $userInsertResult = $this->userModel->insert($userData);

                if (!$userInsertResult) {

                    $this->db->transRollback();
                    $errors = $this->userModel->errors();
                    $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                    log_message('error', 'Gagal menyimpan data user: ' . $errorMessage);
                    return $this->fail('Gagal menyimpan data user: ' . $errorMessage, 500);
                }


                $userId = $this->userModel->getInsertID();
                log_message('debug', 'User berhasil dibuat dengan ID: ' . $userId);


                $data['user_id'] = $userId;
            } else {


                $data['user_id'] = null;
            }


            $pelangganValidation = [
                'nama_pelanggan' => 'required|max_length[100]',
                'no_hp' => 'required|max_length[15]',
                'alamat' => 'required',
            ];

            if (!$this->validate($pelangganValidation)) {
                return $this->fail($this->validator->getErrors(), 400);
            }


            $data['kode_pelanggan'] = $this->pelangganModel->generateKode();


            $pelangganData = [
                'kode_pelanggan' => $data['kode_pelanggan'],
                'user_id' => $data['user_id'],
                'nama_pelanggan' => $data['nama_pelanggan'],
                'no_hp' => $data['no_hp'],
                'alamat' => $data['alamat'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];


            log_message('debug', 'Data pelanggan yang akan disimpan: ' . json_encode($pelangganData));


            $this->pelangganModel->skipValidation(true);


            $insertResult = $this->pelangganModel->insert($pelangganData);

            if (!$insertResult) {

                $this->db->transRollback();
                $errors = $this->pelangganModel->errors();
                $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                log_message('error', 'Gagal menyimpan data pelanggan: ' . $errorMessage);
                return $this->fail('Gagal menyimpan data pelanggan: ' . $errorMessage, 500);
            }


            $this->db->transCommit();

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Data pelanggan berhasil disimpan'
            ]);
        } catch (\Exception $e) {

            $this->db->transRollback();
            log_message('error', 'Exception saat menyimpan data: ' . $e->getMessage() . ' pada baris ' . $e->getLine());
            return $this->fail('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    public function update($kode)
    {
        $data = $this->request->getPost();
        $changeAccount = $this->request->getPost('change_account') === '1';


        $pelanggan = $this->pelangganModel->find($kode);
        if (!$pelanggan) {
            return $this->fail('Pelanggan tidak ditemukan', 404);
        }


        $this->db->transBegin();

        try {

            if ($changeAccount) {

                $hasAccount = !empty($pelanggan['user_id']);


                $userValidation = [
                    'username' => 'required|alpha_numeric_space|min_length[3]',
                    'email' => 'required|valid_email',
                ];


                if (!$hasAccount) {
                    $userValidation['password'] = 'required|min_length[6]';
                }


                if ($hasAccount) {
                    $userValidation['username'] .= '|is_unique[users.username,id,' . $pelanggan['user_id'] . ']';
                    $userValidation['email'] .= '|is_unique[users.email,id,' . $pelanggan['user_id'] . ']';
                } else {
                    $userValidation['username'] .= '|is_unique[users.username]';
                    $userValidation['email'] .= '|is_unique[users.email]';
                }

                if (!$this->validate($userValidation)) {
                    return $this->fail($this->validator->getErrors(), 400);
                }


                $userData = [
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'name' => $data['nama_pelanggan'],
                    'role' => 'pelanggan',
                    'status' => 'active'
                ];


                if (!empty($data['password'])) {
                    $userData['password'] = $data['password'];
                }

                if ($hasAccount) {

                    $userUpdateResult = $this->userModel->update($pelanggan['user_id'], $userData);

                    if (!$userUpdateResult) {

                        $this->db->transRollback();
                        $errors = $this->userModel->errors();
                        $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                        log_message('error', 'Gagal memperbarui data user: ' . $errorMessage);
                        return $this->fail('Gagal memperbarui data user: ' . $errorMessage, 500);
                    }


                    $data['user_id'] = $pelanggan['user_id'];
                } else {

                    $userInsertResult = $this->userModel->insert($userData);

                    if (!$userInsertResult) {

                        $this->db->transRollback();
                        $errors = $this->userModel->errors();
                        $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                        log_message('error', 'Gagal menyimpan data user: ' . $errorMessage);
                        return $this->fail('Gagal menyimpan data user: ' . $errorMessage, 500);
                    }


                    $userId = $this->userModel->getInsertID();
                    log_message('debug', 'User berhasil dibuat dengan ID: ' . $userId);


                    $data['user_id'] = $userId;
                }
            }


            $pelangganValidation = [
                'nama_pelanggan' => 'required|max_length[100]',
                'no_hp' => 'required|max_length[15]',
                'alamat' => 'required',
            ];

            if (!$this->validate($pelangganValidation)) {
                return $this->fail($this->validator->getErrors(), 400);
            }


            $pelangganData = [
                'nama_pelanggan' => $data['nama_pelanggan'],
                'no_hp' => $data['no_hp'],
                'alamat' => $data['alamat'],
                'updated_at' => date('Y-m-d H:i:s')
            ];


            if ($changeAccount) {
                $pelangganData['user_id'] = $data['user_id'] ?? null;
            } else if (isset($data['remove_account']) && $data['remove_account'] === '1') {

                $pelangganData['user_id'] = null;
            }


            log_message('debug', 'Data pelanggan yang akan diupdate: ' . json_encode($pelangganData));


            $updateResult = $this->pelangganModel->update($kode, $pelangganData);

            if (!$updateResult) {

                $this->db->transRollback();
                $errors = $this->pelangganModel->errors();
                $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                log_message('error', 'Gagal memperbarui data pelanggan: ' . $errorMessage);
                return $this->fail('Gagal memperbarui data pelanggan: ' . $errorMessage, 500);
            }


            $this->db->transCommit();

            return $this->respond([
                'status' => 'success',
                'message' => 'Data pelanggan berhasil diperbarui'
            ]);
        } catch (\Exception $e) {

            $this->db->transRollback();
            log_message('error', 'Exception saat memperbarui data: ' . $e->getMessage() . ' pada baris ' . $e->getLine());
            return $this->fail('Gagal memperbarui data: ' . $e->getMessage(), 500);
        }
    }

    public function delete($kode)
    {

        $pelanggan = $this->pelangganModel->find($kode);
        if (!$pelanggan) {
            return $this->fail('Pelanggan tidak ditemukan', 404);
        }


        $this->db->transBegin();

        try {

            if (!empty($pelanggan['user_id'])) {
                $userId = $pelanggan['user_id'];


                $this->userModel->delete($userId);


                log_message('info', 'Menghapus user dengan ID: ' . $userId . ' terkait dengan pelanggan: ' . $kode);
            }


            $this->pelangganModel->delete($kode);


            $this->db->transCommit();

            return $this->respond(['status' => 'success', 'message' => 'Data pelanggan berhasil dihapus']);
        } catch (\Exception $e) {

            $this->db->transRollback();

            log_message('error', 'Gagal menghapus data: ' . $e->getMessage());
            return $this->fail('Gagal menghapus data: ' . $e->getMessage(), 500);
        }
    }

    public function profile()
    {

        if (session()->get('role') !== 'customer') {
            return redirect()->to('/');
        }

        $userId = session()->get('id');
        $pelanggan = $this->pelangganModel->getPelangganByUserId($userId);

        return view('pelanggan/profile', [
            'title' => 'Profil Saya',
            'pelanggan' => $pelanggan
        ]);
    }

    public function updateProfile()
    {

        if (session()->get('role') !== 'customer') {
            return redirect()->to('/');
        }

        $userId = session()->get('id');
        $pelanggan = $this->pelangganModel->getPelangganByUserId($userId);

        if (!$pelanggan) {
            return redirect()->to('/')->with('error', 'Profil pelanggan tidak ditemukan');
        }

        $data = $this->request->getPost();


        $validationRules = [
            'nama_pelanggan' => 'required|max_length[100]',
            'no_hp' => 'required|max_length[15]',
            'alamat' => 'required',
        ];

        if (!$this->validate($validationRules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        try {
            $this->pelangganModel->update($pelanggan['kode_pelanggan'], [
                'nama_pelanggan' => $data['nama_pelanggan'],
                'no_hp' => $data['no_hp'],
                'alamat' => $data['alamat'],
            ]);

            return redirect()->to('pelanggan/profile')->with('success', 'Profil berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }

    public function laporan()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $tanggal_cetak = $this->request->getGet('tanggal_cetak') ?? date('d/m/Y');


        $builder = $this->pelangganModel->builder();
        $builder->orderBy('kode_pelanggan', 'ASC');

        $pelanggan = $builder->get()->getResultArray();


        $data = [
            'title' => 'Laporan Data Pelanggan',
            'subtitle' => 'Laporan data pelanggan untuk admin dan pimpinan',
            'active' => 'laporan-pelanggan',
            'pelanggan' => $pelanggan,
            'tanggal_cetak' => $tanggal_cetak,
            'total_pelanggan' => count($pelanggan)
        ];

        return view('admin/pelanggan/laporan', $data);
    }

    public function exportPDF()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $tanggal_cetak = $this->request->getGet('tanggal_cetak') ?? date('d/m/Y');


        $builder = $this->pelangganModel->builder();
        $builder->orderBy('kode_pelanggan', 'ASC');

        $pelanggan = $builder->get()->getResultArray();


        $data = [
            'pelanggan' => $pelanggan,
            'tanggal_cetak' => $tanggal_cetak,
            'total_pelanggan' => count($pelanggan)
        ];


        require_once ROOTPATH . 'vendor/autoload.php';
        require_once APPPATH . 'Helpers/PdfHelper.php';

        $html = view('admin/pelanggan/laporan_pdf', $data);
        $filename = 'Laporan_Data_Pelanggan_' . str_replace('/', '-', $tanggal_cetak);

        $pdfResult = \App\Helpers\PdfHelper::generatePdf($html, $filename, 'A4', 'landscape');

        return \App\Helpers\PdfHelper::streamPdf($pdfResult, false);
    }
}
