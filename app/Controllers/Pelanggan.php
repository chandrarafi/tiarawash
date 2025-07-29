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

        // Pastikan tabel pelanggan ada
        $this->createPelangganTableIfNotExists();
    }

    /**
     * Membuat tabel pelanggan jika belum ada
     */
    private function createPelangganTableIfNotExists()
    {
        // Periksa apakah tabel pelanggan sudah ada
        $tableExists = $this->db->tableExists('pelanggan');

        if (!$tableExists) {
            // Buat tabel pelanggan tanpa foreign key constraint
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

            log_message('info', 'Tabel pelanggan berhasil dibuat');
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

    /**
     * Menampilkan form tambah pelanggan
     */
    public function create()
    {
        return view('admin/pelanggan/create', [
            'title' => 'Tambah Pelanggan'
        ]);
    }

    /**
     * Menampilkan form edit pelanggan
     */
    public function edit($kode = null)
    {
        if (!$kode) {
            return redirect()->to('admin/pelanggan')->with('error', 'Kode pelanggan tidak valid');
        }

        $pelanggan = $this->pelangganModel->find($kode);
        if (!$pelanggan) {
            return redirect()->to('admin/pelanggan')->with('error', 'Data pelanggan tidak ditemukan');
        }

        // Debug log
        log_message('debug', 'Menampilkan halaman edit untuk pelanggan: ' . $kode);

        return view('admin/pelanggan/edit', [
            'title' => 'Edit Pelanggan',
            'kode_pelanggan' => $kode
        ]);
    }

    /**
     * Mendapatkan semua data pelanggan untuk DataTables
     */
    public function getPelanggan()
    {
        $request = $this->request;

        // Parameter DataTables
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

        // Pencarian
        if (!empty($search)) {
            $builder->groupStart()
                ->like('pelanggan.kode_pelanggan', $search)
                ->orLike('pelanggan.nama_pelanggan', $search)
                ->orLike('users.email', $search)
                ->orLike('pelanggan.no_hp', $search)
                ->orLike('pelanggan.alamat', $search)
                ->groupEnd();
        }

        // Total records
        $totalRecords = $builder->countAllResults(false);

        // Pengurutan
        if ($order) {
            $columnIndex = $order['column'];
            $columnName = $columns[$columnIndex];
            $columnDir = $order['dir'];
            $builder->orderBy($columnName, $columnDir);
        } else {
            $builder->orderBy('pelanggan.kode_pelanggan', 'ASC');
        }

        // Limit & offset
        $builder->limit($length, $start);

        $data = $builder->get()->getResultArray();

        // Format data untuk DataTables
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

    /**
     * Mendapatkan kode pelanggan baru
     */
    public function getNewKode()
    {
        $newKode = $this->pelangganModel->generateKode();
        return $this->respond(['status' => 'success', 'kode' => $newKode]);
    }

    /**
     * Mendapatkan data user untuk select
     */
    public function getUsers()
    {
        $users = $this->userModel->where('role', 'pelanggan')->findAll();

        // Filter user yang belum terdaftar sebagai pelanggan
        $userPelanggan = $this->pelangganModel->findColumn('user_id');
        $userPelanggan = $userPelanggan ?? [];

        $filteredUsers = array_filter($users, function ($user) use ($userPelanggan) {
            return !in_array($user['id'], $userPelanggan);
        });

        return $this->respond(['status' => 'success', 'data' => array_values($filteredUsers)]);
    }

    /**
     * Mendapatkan data pelanggan berdasarkan kode
     */
    public function getByKode($kode)
    {
        $pelanggan = $this->pelangganModel->getPelangganWithUser($kode);

        if (!$pelanggan) {
            return $this->fail('Pelanggan tidak ditemukan', 404);
        }

        // Log data untuk debugging
        log_message('debug', 'Data pelanggan yang diambil: ' . json_encode($pelanggan));

        return $this->respond(['status' => 'success', 'data' => $pelanggan]);
    }

    /**
     * Menyimpan data pelanggan baru
     */
    public function save()
    {
        $data = $this->request->getPost();
        $createAccount = $this->request->getPost('create_account') === '1';

        // Mulai transaksi database
        $this->db->transBegin();

        try {
            // Jika membuat akun baru
            if ($createAccount) {
                // Validasi data akun
                $userValidation = [
                    'username' => 'required|alpha_numeric_space|min_length[3]|is_unique[users.username]',
                    'email' => 'required|valid_email|is_unique[users.email]',
                    'password' => 'required|min_length[6]',
                ];

                if (!$this->validate($userValidation)) {
                    return $this->fail($this->validator->getErrors(), 400);
                }

                // Buat akun user baru
                $userData = [
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => $data['password'],
                    'name' => $data['nama_pelanggan'],
                    'role' => 'pelanggan',
                    'status' => 'active'
                ];

                // Simpan user terlebih dahulu
                $userInsertResult = $this->userModel->insert($userData);

                if (!$userInsertResult) {
                    // Jika gagal menyimpan user, rollback dan kembalikan error
                    $this->db->transRollback();
                    $errors = $this->userModel->errors();
                    $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                    log_message('error', 'Gagal menyimpan data user: ' . $errorMessage);
                    return $this->fail('Gagal menyimpan data user: ' . $errorMessage, 500);
                }

                // Ambil ID user yang baru dibuat
                $userId = $this->userModel->getInsertID();
                log_message('debug', 'User berhasil dibuat dengan ID: ' . $userId);

                // Set user_id untuk data pelanggan
                $data['user_id'] = $userId;
            } else {
                // Jika tidak membuat akun baru, user_id bisa null
                // Pastikan user_id adalah null, bukan string kosong atau 0
                $data['user_id'] = null;
            }

            // Validasi data pelanggan
            $pelangganValidation = [
                'nama_pelanggan' => 'required|max_length[100]',
                'no_hp' => 'required|max_length[15]',
                'alamat' => 'required',
            ];

            if (!$this->validate($pelangganValidation)) {
                return $this->fail($this->validator->getErrors(), 400);
            }

            // Generate kode pelanggan baru
            $data['kode_pelanggan'] = $this->pelangganModel->generateKode();

            // Simpan data pelanggan
            $pelangganData = [
                'kode_pelanggan' => $data['kode_pelanggan'],
                'user_id' => $data['user_id'],
                'nama_pelanggan' => $data['nama_pelanggan'],
                'no_hp' => $data['no_hp'],
                'alamat' => $data['alamat'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Debug log
            log_message('debug', 'Data pelanggan yang akan disimpan: ' . json_encode($pelangganData));

            // Nonaktifkan validasi sementara untuk debugging
            $this->pelangganModel->skipValidation(true);

            // Simpan pelanggan menggunakan model
            $insertResult = $this->pelangganModel->insert($pelangganData);

            if (!$insertResult) {
                // Jika gagal menyimpan, rollback dan kembalikan error
                $this->db->transRollback();
                $errors = $this->pelangganModel->errors();
                $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                log_message('error', 'Gagal menyimpan data pelanggan: ' . $errorMessage);
                return $this->fail('Gagal menyimpan data pelanggan: ' . $errorMessage, 500);
            }

            // Commit transaksi
            $this->db->transCommit();

            return $this->respondCreated([
                'status' => 'success',
                'message' => 'Data pelanggan berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            $this->db->transRollback();
            log_message('error', 'Exception saat menyimpan data: ' . $e->getMessage() . ' pada baris ' . $e->getLine());
            return $this->fail('Gagal menyimpan data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Mengupdate data pelanggan
     */
    public function update($kode)
    {
        $data = $this->request->getPost();
        $changeAccount = $this->request->getPost('change_account') === '1';

        // Cek apakah pelanggan ada
        $pelanggan = $this->pelangganModel->find($kode);
        if (!$pelanggan) {
            return $this->fail('Pelanggan tidak ditemukan', 404);
        }

        // Mulai transaksi database
        $this->db->transBegin();

        try {
            // Jika mengubah informasi akun
            if ($changeAccount) {
                // Cek apakah pelanggan sudah memiliki akun
                $hasAccount = !empty($pelanggan['user_id']);

                // Validasi data akun
                $userValidation = [
                    'username' => 'required|alpha_numeric_space|min_length[3]',
                    'email' => 'required|valid_email',
                ];

                // Password hanya wajib jika membuat akun baru
                if (!$hasAccount) {
                    $userValidation['password'] = 'required|min_length[6]';
                }

                // Tambahkan validasi unique kecuali untuk akun yang sama
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

                // Data user yang akan disimpan
                $userData = [
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'name' => $data['nama_pelanggan'],
                    'role' => 'pelanggan',
                    'status' => 'active'
                ];

                // Tambahkan password jika diisi
                if (!empty($data['password'])) {
                    $userData['password'] = $data['password'];
                }

                if ($hasAccount) {
                    // Update user yang sudah ada
                    $userUpdateResult = $this->userModel->update($pelanggan['user_id'], $userData);

                    if (!$userUpdateResult) {
                        // Jika gagal update user, rollback dan kembalikan error
                        $this->db->transRollback();
                        $errors = $this->userModel->errors();
                        $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                        log_message('error', 'Gagal memperbarui data user: ' . $errorMessage);
                        return $this->fail('Gagal memperbarui data user: ' . $errorMessage, 500);
                    }

                    // User ID tetap sama
                    $data['user_id'] = $pelanggan['user_id'];
                } else {
                    // Buat akun user baru
                    $userInsertResult = $this->userModel->insert($userData);

                    if (!$userInsertResult) {
                        // Jika gagal menyimpan user, rollback dan kembalikan error
                        $this->db->transRollback();
                        $errors = $this->userModel->errors();
                        $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                        log_message('error', 'Gagal menyimpan data user: ' . $errorMessage);
                        return $this->fail('Gagal menyimpan data user: ' . $errorMessage, 500);
                    }

                    // Ambil ID user yang baru dibuat
                    $userId = $this->userModel->getInsertID();
                    log_message('debug', 'User berhasil dibuat dengan ID: ' . $userId);

                    // Set user_id untuk data pelanggan
                    $data['user_id'] = $userId;
                }
            }

            // Validasi data pelanggan
            $pelangganValidation = [
                'nama_pelanggan' => 'required|max_length[100]',
                'no_hp' => 'required|max_length[15]',
                'alamat' => 'required',
            ];

            if (!$this->validate($pelangganValidation)) {
                return $this->fail($this->validator->getErrors(), 400);
            }

            // Update data pelanggan
            $pelangganData = [
                'nama_pelanggan' => $data['nama_pelanggan'],
                'no_hp' => $data['no_hp'],
                'alamat' => $data['alamat'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Update user_id jika diubah
            if ($changeAccount) {
                $pelangganData['user_id'] = $data['user_id'] ?? null;
            } else if (isset($data['remove_account']) && $data['remove_account'] === '1') {
                // Jika memilih untuk menghapus akun
                $pelangganData['user_id'] = null;
            }

            // Debug log
            log_message('debug', 'Data pelanggan yang akan diupdate: ' . json_encode($pelangganData));

            // Update pelanggan
            $updateResult = $this->pelangganModel->update($kode, $pelangganData);

            if (!$updateResult) {
                // Jika gagal update, rollback dan kembalikan error
                $this->db->transRollback();
                $errors = $this->pelangganModel->errors();
                $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Unknown database error';
                log_message('error', 'Gagal memperbarui data pelanggan: ' . $errorMessage);
                return $this->fail('Gagal memperbarui data pelanggan: ' . $errorMessage, 500);
            }

            // Commit transaksi
            $this->db->transCommit();

            return $this->respond([
                'status' => 'success',
                'message' => 'Data pelanggan berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            $this->db->transRollback();
            log_message('error', 'Exception saat memperbarui data: ' . $e->getMessage() . ' pada baris ' . $e->getLine());
            return $this->fail('Gagal memperbarui data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Menghapus data pelanggan
     */
    public function delete($kode)
    {
        // Cek apakah pelanggan ada
        $pelanggan = $this->pelangganModel->find($kode);
        if (!$pelanggan) {
            return $this->fail('Pelanggan tidak ditemukan', 404);
        }

        // Mulai transaksi database
        $this->db->transBegin();

        try {
            // Jika pelanggan memiliki akun user, hapus juga data usernya
            if (!empty($pelanggan['user_id'])) {
                $userId = $pelanggan['user_id'];

                // Hapus data user
                $this->userModel->delete($userId);

                // Log untuk debugging
                log_message('info', 'Menghapus user dengan ID: ' . $userId . ' terkait dengan pelanggan: ' . $kode);
            }

            // Hapus data pelanggan
            $this->pelangganModel->delete($kode);

            // Commit transaksi
            $this->db->transCommit();

            return $this->respond(['status' => 'success', 'message' => 'Data pelanggan berhasil dihapus']);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            $this->db->transRollback();

            log_message('error', 'Gagal menghapus data: ' . $e->getMessage());
            return $this->fail('Gagal menghapus data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Halaman profil pelanggan
     */
    public function profile()
    {
        // Hanya bisa diakses oleh customer
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

    /**
     * Update profil pelanggan
     */
    public function updateProfile()
    {
        // Hanya bisa diakses oleh customer
        if (session()->get('role') !== 'customer') {
            return redirect()->to('/');
        }

        $userId = session()->get('id');
        $pelanggan = $this->pelangganModel->getPelangganByUserId($userId);

        if (!$pelanggan) {
            return redirect()->to('/')->with('error', 'Profil pelanggan tidak ditemukan');
        }

        $data = $this->request->getPost();

        // Validasi hanya field yang diizinkan
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
}
