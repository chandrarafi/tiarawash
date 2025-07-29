<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PembelianModel;
use App\Models\DetailPembelianModel;
use App\Models\PerlengkapanModel;
use CodeIgniter\API\ResponseTrait;

class Pembelian extends BaseController
{
    use ResponseTrait;

    protected $pembelianModel;
    protected $detailPembelianModel;
    protected $perlengkapanModel;
    protected $db;

    public function __construct()
    {
        $this->pembelianModel = new PembelianModel();
        $this->detailPembelianModel = new DetailPembelianModel();
        $this->perlengkapanModel = new PerlengkapanModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Pembelian',
            'active' => 'pembelian'
        ];
        return view('admin/pembelian/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Pembelian',
            'active' => 'pembelian',
            'no_faktur' => $this->pembelianModel->generateNoFaktur()
        ];
        return view('admin/pembelian/create', $data);
    }

    public function edit($noFaktur = null)
    {
        if (!$noFaktur) {
            return redirect()->to('admin/pembelian')->with('error', 'Nomor faktur tidak ditemukan');
        }

        $pembelian = $this->pembelianModel->find($noFaktur);
        if (!$pembelian) {
            return redirect()->to('admin/pembelian')->with('error', 'Data pembelian tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Pembelian',
            'active' => 'pembelian',
            'pembelian' => $pembelian
        ];
        return view('admin/pembelian/edit', $data);
    }

    public function detail($noFaktur = null)
    {
        if (!$noFaktur) {
            return redirect()->to('admin/pembelian')->with('error', 'Nomor faktur tidak ditemukan');
        }

        $pembelian = $this->pembelianModel->getPembelianWithDetails($noFaktur);
        if (!$pembelian) {
            return redirect()->to('admin/pembelian')->with('error', 'Data pembelian tidak ditemukan');
        }

        $details = $this->detailPembelianModel->getDetailWithPerlengkapan($noFaktur);

        $data = [
            'title' => 'Detail Pembelian',
            'active' => 'pembelian',
            'pembelian' => $pembelian,
            'details' => $details
        ];
        return view('admin/pembelian/detail', $data);
    }

    // API untuk DataTables
    public function getPembelian()
    {
        $request = $this->request;
        $draw = $request->getGet('draw');
        $start = $request->getGet('start');
        $length = $request->getGet('length');
        $search = $request->getGet('search')['value'];

        // Query dasar
        $builder = $this->pembelianModel->builder();
        $builder->select('pembelian.*, users.name as user_name');
        $builder->join('users', 'users.id = pembelian.user_id', 'left');

        // Filter pencarian
        if ($search) {
            $builder->groupStart()
                ->like('no_faktur', $search)
                ->orLike('supplier', $search)
                ->orLike('tanggal', $search)
                ->orLike('users.name', $search)
                ->groupEnd();
        }

        // Hitung total records dan filtered records
        $totalRecords = $builder->countAllResults(false);
        $totalFiltered = $totalRecords;

        // Ambil data dengan limit dan offset
        $builder->orderBy('pembelian.tanggal', 'DESC');
        $builder->limit($length, $start);
        $data = $builder->get()->getResultArray();

        // Format data untuk DataTables
        $response = [
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data
        ];

        return $this->respond($response);
    }

    public function getPerlengkapan()
    {
        try {
            log_message('debug', 'getPerlengkapan method called');
            $perlengkapan = $this->perlengkapanModel->findAll();
            log_message('debug', 'Perlengkapan data loaded: ' . count($perlengkapan) . ' items');
            log_message('debug', 'Perlengkapan data: ' . json_encode($perlengkapan));
            return $this->respond($perlengkapan);
        } catch (\Exception $e) {
            log_message('error', 'Error in getPerlengkapan: ' . $e->getMessage());
            return $this->respond([]);
        }
    }

    public function getDetailPembelian($noFaktur)
    {
        try {
            log_message('debug', 'getDetailPembelian called with noFaktur: ' . $noFaktur);
            $details = $this->detailPembelianModel->getDetailWithPerlengkapan($noFaktur);
            log_message('debug', 'Detail pembelian loaded: ' . count($details) . ' items');
            log_message('debug', 'Detail data: ' . json_encode($details));
            return $this->respond($details);
        } catch (\Exception $e) {
            log_message('error', 'Error in getDetailPembelian: ' . $e->getMessage());
            return $this->respond([]);
        }
    }

    public function save()
    {
        $rules = [
            'no_faktur' => 'required|min_length[3]|max_length[20]',
            'tanggal' => 'required|valid_date',
            'supplier' => 'required|min_length[3]|max_length[100]',
            'items' => 'required',
        ];

        // Validasi manual untuk keunikan no_faktur
        $noFaktur = $this->request->getPost('no_faktur');
        try {
            $existingNoFaktur = $this->pembelianModel->where('no_faktur', $noFaktur)->first();
            if ($existingNoFaktur) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'messages' => ['no_faktur' => 'Nomor faktur sudah ada dalam database']
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error checking no_faktur uniqueness: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error validasi nomor faktur: ' . $e->getMessage()
            ]);
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'messages' => $this->validator->getErrors()
            ]);
        }

        // Ambil data pembelian
        $data = [
            'no_faktur' => $this->request->getPost('no_faktur'),
            'tanggal' => $this->request->getPost('tanggal'),
            'supplier' => $this->request->getPost('supplier'),
            'keterangan' => $this->request->getPost('keterangan'),
            'total_harga' => 0, // Akan diupdate setelah detail disimpan
            'user_id' => session()->get('user_id')
        ];

        // Ambil data items
        $items = json_decode($this->request->getPost('items'), true);
        if (!$items || !is_array($items) || count($items) === 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data item pembelian tidak valid'
            ]);
        }

        // Mulai transaksi database
        $this->db->transBegin();

        try {
            // Simpan data pembelian
            $insertResult = $this->pembelianModel->insert($data);
            if (!$insertResult) {
                throw new \Exception('Gagal menyimpan data pembelian: ' . implode(', ', $this->pembelianModel->errors()));
            }
            $noFaktur = $data['no_faktur']; // Use no_faktur instead of getInsertID()

            // Simpan detail pembelian
            $totalHarga = 0;
            foreach ($items as $item) {
                $subtotal = $item['jumlah'] * $item['harga_satuan'];
                $detailData = [
                    'no_faktur' => $noFaktur,
                    'perlengkapan_id' => $item['perlengkapan_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_satuan' => $item['harga_satuan'],
                    'subtotal' => $subtotal
                ];

                $detailInsertResult = $this->detailPembelianModel->insert($detailData);
                if (!$detailInsertResult) {
                    throw new \Exception('Gagal menyimpan detail pembelian: ' . implode(', ', $this->detailPembelianModel->errors()));
                }
                $detailId = $this->detailPembelianModel->getInsertID();

                log_message('debug', 'Detail pembelian inserted with ID: ' . $detailId);

                // Update stok perlengkapan
                $stokUpdateResult = $this->detailPembelianModel->updateStokPerlengkapan($detailId);
                if (!$stokUpdateResult) {
                    throw new \Exception('Gagal update stok perlengkapan untuk detail ID: ' . $detailId);
                }

                $totalHarga += $subtotal;
            }

            // Update total harga pembelian
            $updateResult = $this->pembelianModel->update($noFaktur, ['total_harga' => $totalHarga]);
            if (!$updateResult) {
                throw new \Exception('Gagal update total harga pembelian');
            }

            // Commit transaksi
            $this->db->transCommit();

            // Log aktivitas
            log_message('info', 'Pembelian baru dibuat: {no_faktur} oleh {user_id}', [
                'no_faktur' => $data['no_faktur'],
                'user_id' => $data['user_id']
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data pembelian berhasil disimpan',
                'no_faktur' => $noFaktur
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            $this->db->transRollback();

            log_message('error', 'Error saat menyimpan pembelian: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan data pembelian: ' . $e->getMessage()
            ]);
        }
    }

    public function update()
    {
        $noFaktur = $this->request->getPost('no_faktur');
        if (!$noFaktur) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nomor faktur tidak ditemukan'
            ]);
        }

        $pembelian = $this->pembelianModel->find($noFaktur);
        if (!$pembelian) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data pembelian tidak ditemukan'
            ]);
        }

        // Use validation rules for edit
        $rules = $this->pembelianModel->getValidationRulesForEdit($noFaktur);

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'messages' => $this->validator->getErrors()
            ]);
        }

        // Ambil data pembelian
        $data = [
            'no_faktur' => $noFaktur,
            'tanggal' => $this->request->getPost('tanggal'),
            'supplier' => $this->request->getPost('supplier'),
            'keterangan' => $this->request->getPost('keterangan')
        ];

        // Mulai transaksi database
        $this->db->transBegin();

        try {
            // Update data pembelian
            $this->pembelianModel->update($noFaktur, $data);

            // Hitung total harga awal dari item yang sudah ada
            $totalHarga = 0;
            $existingDetails = $this->detailPembelianModel->where('no_faktur', $noFaktur)->findAll();
            foreach ($existingDetails as $detail) {
                $totalHarga += $detail['subtotal'];
            }

            // Cek apakah ada item baru yang perlu ditambahkan
            $items = json_decode($this->request->getPost('items'), true);
            if ($items && is_array($items) && count($items) > 0) {
                // Tambahkan item baru
                foreach ($items as $item) {
                    $subtotal = $item['jumlah'] * $item['harga_satuan'];
                    $detailData = [
                        'no_faktur' => $noFaktur,
                        'perlengkapan_id' => $item['perlengkapan_id'],
                        'jumlah' => $item['jumlah'],
                        'harga_satuan' => $item['harga_satuan'],
                        'subtotal' => $subtotal
                    ];

                    $this->detailPembelianModel->insert($detailData);
                    $detailId = $this->detailPembelianModel->getInsertID();

                    // Update stok perlengkapan
                    $this->detailPembelianModel->updateStokPerlengkapan($detailId);

                    $totalHarga += $subtotal;
                }
            }

            // Cek apakah ada item yang diperbarui jumlahnya
            $updatedItems = json_decode($this->request->getPost('updated_items'), true);
            if ($updatedItems && is_array($updatedItems) && count($updatedItems) > 0) {
                foreach ($updatedItems as $item) {
                    if (!isset($item['id'])) {
                        continue; // Lewati jika tidak ada ID
                    }

                    // Ambil data detail lama
                    $oldDetail = $this->detailPembelianModel->find($item['id']);
                    if (!$oldDetail) {
                        continue; // Lewati jika item tidak ditemukan
                    }

                    // Hitung selisih jumlah untuk update stok
                    $jumlahSelisih = $item['jumlah'] - $oldDetail['jumlah'];

                    // Update stok perlengkapan
                    if ($jumlahSelisih != 0) {
                        $perlengkapan = $this->perlengkapanModel->find($oldDetail['perlengkapan_id']);
                        if ($perlengkapan) {
                            $newStok = $perlengkapan['stok'] + $jumlahSelisih;
                            // Pastikan stok tidak negatif
                            $newStok = max(0, $newStok);
                            $this->perlengkapanModel->update($oldDetail['perlengkapan_id'], ['stok' => $newStok]);
                        }
                    }

                    // Hitung subtotal baru
                    $subtotal = $item['jumlah'] * $item['harga_satuan'];

                    // Update detail pembelian
                    $detailData = [
                        'jumlah' => $item['jumlah'],
                        'subtotal' => $subtotal
                    ];

                    $this->detailPembelianModel->update($item['id'], $detailData);

                    // Kurangi total harga lama dan tambahkan yang baru
                    $totalHarga = $totalHarga - $oldDetail['subtotal'] + $subtotal;
                }
            }

            // Update total harga pembelian
            $updateResult = $this->pembelianModel->update($noFaktur, ['total_harga' => $totalHarga]);
            if (!$updateResult) {
                throw new \Exception('Gagal update total harga pembelian');
            }

            // Commit transaksi
            $this->db->transCommit();

            // Log aktivitas
            log_message('info', 'Pembelian diupdate: {no_faktur} oleh {user_id}', [
                'no_faktur' => $data['no_faktur'],
                'user_id' => session()->get('user_id')
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data pembelian berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            $this->db->transRollback();

            log_message('error', 'Error saat memperbarui pembelian: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal memperbarui data pembelian: ' . $e->getMessage()
            ]);
        }
    }

    public function delete($noFaktur = null)
    {
        if (!$noFaktur) {
            return $this->fail('Nomor faktur tidak ditemukan');
        }

        $pembelian = $this->pembelianModel->find($noFaktur);
        if (!$pembelian) {
            return $this->failNotFound('Data pembelian tidak ditemukan');
        }

        // Mulai transaksi database
        $this->db->transBegin();

        try {
            // Dapatkan semua detail pembelian untuk mengembalikan stok
            $details = $this->detailPembelianModel->where('no_faktur', $noFaktur)->findAll();

            // Kembalikan stok perlengkapan
            foreach ($details as $detail) {
                $perlengkapan = $this->perlengkapanModel->find($detail['perlengkapan_id']);
                if ($perlengkapan) {
                    $newStok = $perlengkapan['stok'] - $detail['jumlah'];
                    // Pastikan stok tidak negatif
                    $newStok = max(0, $newStok);
                    $this->perlengkapanModel->update($detail['perlengkapan_id'], ['stok' => $newStok]);
                }
            }

            // Hapus detail pembelian
            $this->detailPembelianModel->where('no_faktur', $noFaktur)->delete();

            // Hapus pembelian
            $this->pembelianModel->delete($noFaktur);

            // Commit transaksi
            $this->db->transCommit();

            // Log aktivitas
            log_message('info', 'Pembelian dihapus: {no_faktur} oleh {user_id}', [
                'no_faktur' => $pembelian['no_faktur'],
                'user_id' => session()->get('user_id')
            ]);

            return $this->respondDeleted([
                'status' => true,
                'message' => 'Data pembelian berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            $this->db->transRollback();

            log_message('error', 'Error saat menghapus pembelian: ' . $e->getMessage());

            return $this->fail([
                'status' => false,
                'message' => 'Gagal menghapus data pembelian: ' . $e->getMessage()
            ]);
        }
    }

    public function saveDetail()
    {
        $rules = [
            'no_faktur' => 'required|max_length[20]',
            'perlengkapan_id' => 'required|integer',
            'jumlah' => 'required|integer|greater_than[0]',
            'harga_satuan' => 'required|numeric|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'messages' => $this->validator->getErrors()
            ]);
        }

        // Ambil data detail
        $noFaktur = $this->request->getPost('no_faktur');
        $perlengkapanId = $this->request->getPost('perlengkapan_id');
        $jumlah = $this->request->getPost('jumlah');
        $hargaSatuan = $this->request->getPost('harga_satuan');
        $subtotal = $jumlah * $hargaSatuan;

        $data = [
            'no_faktur' => $noFaktur,
            'perlengkapan_id' => $perlengkapanId,
            'jumlah' => $jumlah,
            'harga_satuan' => $hargaSatuan,
            'subtotal' => $subtotal
        ];

        // Mulai transaksi database
        $this->db->transBegin();

        try {
            // Simpan detail pembelian
            $this->detailPembelianModel->insert($data);
            $detailId = $this->detailPembelianModel->getInsertID();

            // Update stok perlengkapan
            $this->detailPembelianModel->updateStokPerlengkapan($detailId);

            // Update total harga pembelian
            $totalHarga = $this->detailPembelianModel->calculateTotal($noFaktur);
            $this->pembelianModel->update($noFaktur, ['total_harga' => $totalHarga]);

            // Commit transaksi
            $this->db->transCommit();

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Detail pembelian berhasil ditambahkan',
                'data' => $data,
                'total_harga' => $totalHarga
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            $this->db->transRollback();

            log_message('error', 'Error saat menambahkan detail pembelian: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menambahkan detail pembelian: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteDetail($id = null)
    {
        if (!$id) {
            return $this->fail('ID detail pembelian tidak ditemukan');
        }

        $detail = $this->detailPembelianModel->find($id);
        if (!$detail) {
            return $this->failNotFound('Data detail pembelian tidak ditemukan');
        }

        $noFaktur = $detail['no_faktur'];

        // Mulai transaksi database
        $this->db->transBegin();

        try {
            // Kembalikan stok perlengkapan
            $perlengkapan = $this->perlengkapanModel->find($detail['perlengkapan_id']);
            if ($perlengkapan) {
                $newStok = $perlengkapan['stok'] - $detail['jumlah'];
                // Pastikan stok tidak negatif
                $newStok = max(0, $newStok);
                $this->perlengkapanModel->update($detail['perlengkapan_id'], ['stok' => $newStok]);
            }

            // Hapus detail pembelian
            $this->detailPembelianModel->delete($id);

            // Update total harga pembelian
            $totalHarga = $this->detailPembelianModel->calculateTotal($noFaktur);
            $this->pembelianModel->update($noFaktur, ['total_harga' => $totalHarga]);

            // Commit transaksi
            $this->db->transCommit();

            return $this->respondDeleted([
                'status' => true,
                'message' => 'Detail pembelian berhasil dihapus',
                'total_harga' => $totalHarga
            ]);
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            $this->db->transRollback();

            log_message('error', 'Error saat menghapus detail pembelian: ' . $e->getMessage());

            return $this->fail([
                'status' => false,
                'message' => 'Gagal menghapus detail pembelian: ' . $e->getMessage()
            ]);
        }
    }

    public function laporan()
    {
        $data = [
            'title' => 'Laporan Pembelian',
            'active' => 'laporan-pembelian'
        ];
        return view('admin/pembelian/laporan', $data);
    }

    public function getLaporanData()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        if (!$startDate || !$endDate) {
            return $this->fail('Tanggal awal dan akhir harus diisi');
        }

        $laporan = $this->pembelianModel->getLaporanPembelian($startDate, $endDate);

        return $this->respond([
            'status' => 'success',
            'data' => $laporan
        ]);
    }
}
