<?php

namespace App\Controllers;

use App\Models\KaryawanModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Karyawan extends BaseController
{
    protected $karyawanModel;
    protected $validation;

    public function __construct()
    {
        $this->karyawanModel = new KaryawanModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $data = [
            'title' => 'Data Karyawan',
            'active' => 'karyawan'
        ];

        return view('admin/karyawan/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Karyawan Baru',
            'active' => 'karyawan',
            'validation' => $this->validation
        ];

        return view('admin/karyawan/create', $data);
    }

    public function store()
    {
        // Handle JSON request dari AJAX
        if ($this->request->getHeaderLine('Content-Type') === 'application/json') {
            $json = $this->request->getJSON(true);
            $data = $json;
        } else {
            $data = $this->request->getPost();
        }

        // Validasi input
        $rules = [
            'idkaryawan' => 'required|is_unique[karyawan.idkaryawan]',
            'namakaryawan' => 'required|max_length[100]',
            'nohp' => 'required|max_length[15]',
            'alamat' => 'required'
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'messages' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Generate ID jika kosong
        $idkaryawan = $data['idkaryawan'] ?? '';
        if (empty($idkaryawan)) {
            $idkaryawan = $this->karyawanModel->generateId();
        }

        // Simpan data
        try {
            $this->karyawanModel->save([
                'idkaryawan' => $idkaryawan,
                'namakaryawan' => $data['namakaryawan'],
                'nohp' => $data['nohp'],
                'alamat' => $data['alamat']
            ]);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Karyawan berhasil ditambahkan'
                ]);
            }

            session()->setFlashdata('success', 'Karyawan berhasil ditambahkan');
            return redirect()->to('/admin/karyawan');
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan data karyawan'
                ]);
            }
            session()->setFlashdata('error', 'Gagal menyimpan data karyawan');
            return redirect()->back()->withInput();
        }
    }

    public function edit($id = null)
    {
        $karyawan = $this->karyawanModel->find($id);

        if (!$karyawan) {
            throw new PageNotFoundException('Karyawan dengan ID ' . $id . ' tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Karyawan',
            'active' => 'karyawan',
            'karyawan' => $karyawan,
            'validation' => $this->validation
        ];

        return view('admin/karyawan/edit', $data);
    }

    public function update($id = null)
    {
        // Handle JSON request dari AJAX
        if ($this->request->getHeaderLine('Content-Type') === 'application/json') {
            $json = $this->request->getJSON(true);
            $data = $json;
        } else {
            $data = $this->request->getPost();
        }

        // Jika ID tidak ada di parameter, ambil dari data
        if ($id === null && isset($data['id'])) {
            $id = $data['id'];
        }

        // Pastikan karyawan ada
        $karyawan = $this->karyawanModel->find($id);
        if (!$karyawan) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Karyawan tidak ditemukan'
                ]);
            }
            throw new PageNotFoundException('Karyawan dengan ID ' . $id . ' tidak ditemukan');
        }

        // Validasi input
        $rules = [
            'namakaryawan' => 'required|max_length[100]',
            'nohp' => 'required|max_length[15]',
            'alamat' => 'required'
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'messages' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Update data
        try {
            $this->karyawanModel->update($id, [
                'namakaryawan' => $data['namakaryawan'],
                'nohp' => $data['nohp'],
                'alamat' => $data['alamat']
            ]);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Karyawan berhasil diperbarui'
                ]);
            }

            session()->setFlashdata('success', 'Karyawan berhasil diperbarui');
            return redirect()->to('/admin/karyawan');
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal memperbarui data karyawan'
                ]);
            }
            session()->setFlashdata('error', 'Gagal memperbarui data karyawan');
            return redirect()->back()->withInput();
        }
    }

    public function delete($id = null)
    {
        $karyawan = $this->karyawanModel->find($id);

        if (!$karyawan) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => 'error',
                    'message' => 'Karyawan tidak ditemukan'
                ]);
            }
            throw new PageNotFoundException('Karyawan dengan ID ' . $id . ' tidak ditemukan');
        }

        try {
            $this->karyawanModel->delete($id);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Karyawan berhasil dihapus'
                ]);
            }

            session()->setFlashdata('success', 'Karyawan berhasil dihapus');
            return redirect()->to('/admin/karyawan');
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(500)->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menghapus data karyawan'
                ]);
            }
            session()->setFlashdata('error', 'Gagal menghapus data karyawan');
            return redirect()->to('/admin/karyawan');
        }
    }

    // AJAX Methods untuk DataTables dan sistem lama
    public function getKaryawan()
    {
        $request = $this->request;

        // Parameters untuk DataTables
        $start = $request->getGet('start') ?? 0;
        $length = $request->getGet('length') ?? 10;
        $search = $request->getGet('search')['value'] ?? '';
        $order = $request->getGet('order') ?? [];

        $orderColumn = $order[0]['column'] ?? 0;
        $orderDir = $order[0]['dir'] ?? 'asc';

        // Columns untuk ordering
        $columns = ['idkaryawan', 'namakaryawan', 'nohp', 'alamat'];
        $orderBy = $columns[$orderColumn] ?? 'idkaryawan';

        // Query builder
        $builder = $this->karyawanModel->builder();

        // Search
        if (!empty($search)) {
            $builder->groupStart()
                ->like('idkaryawan', $search)
                ->orLike('namakaryawan', $search)
                ->orLike('nohp', $search)
                ->orLike('alamat', $search)
                ->groupEnd();
        }

        // Total records (filtered)
        $recordsFiltered = $builder->countAllResults(false);

        // Apply ordering dan limit
        $builder->orderBy($orderBy, $orderDir);
        $builder->limit($length, $start);

        $karyawan = $builder->get()->getResultArray();

        // Total records (unfiltered)
        $recordsTotal = $this->karyawanModel->countAllResults();

        // Format data untuk DataTables
        $data = [];
        foreach ($karyawan as $row) {
            $data[] = [
                'idkaryawan' => $row['idkaryawan'],
                'namakaryawan' => $row['namakaryawan'],
                'nohp' => $row['nohp'],
                'alamat' => $row['alamat'],
                'actions' => $this->generateActionButtons($row['idkaryawan'])
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($request->getGet('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    private function generateActionButtons($id)
    {
        return '
            <button type="button" class="btn btn-sm btn-info btn-edit" data-id="' . $id . '" title="Edit">
                <i class="bi bi-pencil"></i>
            </button>
            <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="' . $id . '" title="Hapus">
                <i class="bi bi-trash"></i>
            </button>
        ';
    }

    public function getNewId()
    {
        $newId = $this->karyawanModel->generateId();
        return $this->response->setJSON([
            'status' => 'success',
            'id' => $newId
        ]);
    }

    public function getKaryawanById($id)
    {
        $karyawan = $this->karyawanModel->find($id);

        if (!$karyawan) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Karyawan tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $karyawan
        ]);
    }
}
