<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PerlengkapanModel;
use CodeIgniter\API\ResponseTrait;

class Perlengkapan extends BaseController
{
    use ResponseTrait;

    protected $perlengkapanModel;

    public function __construct()
    {
        $this->perlengkapanModel = new PerlengkapanModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Perlengkapan',
            'active' => 'perlengkapan'
        ];
        return view('admin/perlengkapan/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Perlengkapan',
            'active' => 'perlengkapan'
        ];
        return view('admin/perlengkapan/create', $data);
    }

    public function edit($id = null)
    {
        if (!$id) {
            return redirect()->to('admin/perlengkapan')->with('error', 'ID perlengkapan tidak ditemukan');
        }

        $perlengkapan = $this->perlengkapanModel->find($id);
        if (!$perlengkapan) {
            return redirect()->to('admin/perlengkapan')->with('error', 'Data perlengkapan tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Perlengkapan',
            'active' => 'perlengkapan',
            'id' => $id
        ];
        return view('admin/perlengkapan/edit', $data);
    }

    // API untuk DataTables
    public function getPerlengkapan()
    {
        $request = $this->request;
        $draw = $request->getGet('draw');
        $start = $request->getGet('start');
        $length = $request->getGet('length');
        $search = $request->getGet('search')['value'];
        $kategori = $request->getGet('kategori');

        // Query dasar
        $builder = $this->perlengkapanModel->builder();

        // Filter berdasarkan kategori jika ada
        if ($kategori && $kategori !== 'semua') {
            $builder->where('kategori', $kategori);
        }

        // Filter pencarian
        if ($search) {
            $builder->groupStart()
                ->like('nama', $search)
                ->orLike('kategori', $search)
                ->orLike('deskripsi', $search)
                ->groupEnd();
        }

        // Hitung total records dan filtered records
        $totalRecords = $builder->countAllResults(false);
        $totalFiltered = $totalRecords;

        // Ambil data dengan limit dan offset
        $builder->orderBy('id', 'DESC');
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

    public function getById($id)
    {
        $perlengkapan = $this->perlengkapanModel->find($id);
        if ($perlengkapan) {
            return $this->respond($perlengkapan);
        } else {
            return $this->failNotFound('Data perlengkapan tidak ditemukan');
        }
    }

    public function save()
    {
        $rules = $this->perlengkapanModel->getValidationRules();
        $messages = $this->perlengkapanModel->getValidationMessages();

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON([
                'status' => 'error',
                'messages' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'nama' => $this->request->getPost('nama'),
            'kategori' => $this->request->getPost('kategori'),
            'stok' => $this->request->getPost('stok'),
            'harga' => $this->request->getPost('harga'),
            'deskripsi' => $this->request->getPost('deskripsi')
        ];

        try {
            $this->perlengkapanModel->insert($data);

            // Log aktivitas
            log_message('info', 'Perlengkapan baru ditambahkan: {nama} ({kategori})', $data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data perlengkapan berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error saat menyimpan perlengkapan: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menyimpan data perlengkapan'
            ]);
        }
    }

    public function update()
    {
        $rules = $this->perlengkapanModel->getValidationRules();
        $messages = $this->perlengkapanModel->getValidationMessages();

        if (!$this->validate($rules, $messages)) {
            return $this->response->setJSON([
                'status' => 'error',
                'messages' => $this->validator->getErrors()
            ]);
        }

        $id = $this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID perlengkapan tidak ditemukan'
            ]);
        }

        // Ambil data lama untuk log perubahan
        $oldData = $this->perlengkapanModel->find($id);
        if (!$oldData) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data perlengkapan tidak ditemukan'
            ]);
        }

        $data = [
            'id' => $id,
            'nama' => $this->request->getPost('nama'),
            'kategori' => $this->request->getPost('kategori'),
            'stok' => $this->request->getPost('stok'),
            'harga' => $this->request->getPost('harga'),
            'deskripsi' => $this->request->getPost('deskripsi')
        ];

        try {
            $this->perlengkapanModel->update($id, $data);

            // Log perubahan stok untuk pencatatan inventaris
            if ($oldData['stok'] != $data['stok']) {
                $selisih = $data['stok'] - $oldData['stok'];
                $status = $selisih > 0 ? 'Penambahan' : 'Pengurangan';
                log_message('info', '{status} stok {nama}: {selisih} unit (sebelum: {stok_lama}, sesudah: {stok_baru})', [
                    'status' => $status,
                    'nama' => $data['nama'],
                    'selisih' => abs($selisih),
                    'stok_lama' => $oldData['stok'],
                    'stok_baru' => $data['stok']
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data perlengkapan berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error saat memperbarui perlengkapan: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal memperbarui data perlengkapan'
            ]);
        }
    }

    public function delete($id = null)
    {
        if (!$id) {
            return $this->fail('ID perlengkapan tidak ditemukan');
        }

        $perlengkapan = $this->perlengkapanModel->find($id);
        if (!$perlengkapan) {
            return $this->failNotFound('Data perlengkapan tidak ditemukan');
        }

        try {
            $this->perlengkapanModel->delete($id);

            // Log aktivitas
            log_message('info', 'Perlengkapan dihapus: {nama} ({kategori})', $perlengkapan);

            return $this->respondDeleted([
                'status' => true,
                'message' => 'Data perlengkapan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error saat menghapus perlengkapan: ' . $e->getMessage());

            return $this->fail([
                'status' => false,
                'message' => 'Gagal menghapus data perlengkapan'
            ]);
        }
    }

    // Fungsi tambahan untuk manajemen stok
    public function updateStok()
    {
        $id = $this->request->getPost('id');
        $jumlah = $this->request->getPost('jumlah');
        $keterangan = $this->request->getPost('keterangan');
        $tipe = $this->request->getPost('tipe'); // 'masuk' atau 'keluar'

        if (!$id || !$jumlah) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID dan jumlah harus diisi'
            ]);
        }

        $perlengkapan = $this->perlengkapanModel->find($id);
        if (!$perlengkapan) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data perlengkapan tidak ditemukan'
            ]);
        }

        $stokLama = $perlengkapan['stok'];
        $stokBaru = $tipe === 'masuk' ? $stokLama + $jumlah : $stokLama - $jumlah;

        // Validasi stok tidak boleh negatif
        if ($stokBaru < 0) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Stok tidak mencukupi untuk pengurangan'
            ]);
        }

        try {
            $this->perlengkapanModel->update($id, ['stok' => $stokBaru]);

            // Log perubahan stok
            log_message('info', 'Perubahan stok {nama}: {tipe} {jumlah} unit (sebelum: {stok_lama}, sesudah: {stok_baru}), Keterangan: {keterangan}', [
                'nama' => $perlengkapan['nama'],
                'tipe' => $tipe,
                'jumlah' => $jumlah,
                'stok_lama' => $stokLama,
                'stok_baru' => $stokBaru,
                'keterangan' => $keterangan ?? '-'
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Stok berhasil diperbarui',
                'stok_baru' => $stokBaru
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error saat memperbarui stok: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal memperbarui stok'
            ]);
        }
    }

    // Fungsi untuk mendapatkan perlengkapan dengan stok menipis (untuk notifikasi)
    public function getStokMenipis()
    {
        $batasMinimal = 10; // Batas minimal stok yang dianggap menipis

        $stokMenipis = $this->perlengkapanModel->where('stok <', $batasMinimal)->findAll();

        return $this->respond([
            'status' => 'success',
            'data' => $stokMenipis,
            'count' => count($stokMenipis)
        ]);
    }

    /**
     * Laporan Data Perlengkapan PerBulan
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
        $builder = $this->perlengkapanModel->builder();
        $builder->where('MONTH(created_at)', $bulan);
        $builder->where('YEAR(created_at)', $tahun);
        $builder->orderBy('id', 'ASC');

        $perlengkapan = $builder->get()->getResultArray();

        // Prepare data for view
        $data = [
            'title' => 'Laporan Data Perlengkapan PerBulan',
            'subtitle' => 'Laporan perlengkapan perbulan untuk admin dan pimpinan',
            'active' => 'laporan-perlengkapan-perbulan',
            'perlengkapan' => $perlengkapan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'total_items' => count($perlengkapan),
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

        return view('admin/perlengkapan/laporan_perbulan', $data);
    }

    /**
     * Export Laporan Perlengkapan PerBulan ke PDF
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
        $builder = $this->perlengkapanModel->builder();
        $builder->where('MONTH(created_at)', $bulan);
        $builder->where('YEAR(created_at)', $tahun);
        $builder->orderBy('id', 'ASC');

        $perlengkapan = $builder->get()->getResultArray();

        // Prepare data for PDF
        $data = [
            'perlengkapan' => $perlengkapan,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'total_items' => count($perlengkapan),
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
        $dompdf->loadHtml(view('admin/perlengkapan/laporan_perbulan_pdf', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Set filename
        $filename = 'Laporan_Perlengkapan_PerBulan_' . $data['nama_bulan'][$bulan] . '_' . $tahun . '.pdf';

        // Output PDF
        $dompdf->stream($filename, array('Attachment' => false));
    }
}
