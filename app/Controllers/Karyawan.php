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

        if ($this->request->getHeaderLine('Content-Type') === 'application/json') {
            $json = $this->request->getJSON(true);
            $data = $json;
        } else {
            $data = $this->request->getPost();
        }


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


        $idkaryawan = $data['idkaryawan'] ?? '';
        if (empty($idkaryawan)) {
            $idkaryawan = $this->karyawanModel->generateId();
        }


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

        if ($this->request->getHeaderLine('Content-Type') === 'application/json') {
            $json = $this->request->getJSON(true);
            $data = $json;
        } else {
            $data = $this->request->getPost();
        }


        if ($id === null && isset($data['id'])) {
            $id = $data['id'];
        }


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


    public function getKaryawan()
    {
        $request = $this->request;


        $start = $request->getGet('start') ?? 0;
        $length = $request->getGet('length') ?? 10;
        $search = $request->getGet('search')['value'] ?? '';
        $order = $request->getGet('order') ?? [];

        $orderColumn = $order[0]['column'] ?? 0;
        $orderDir = $order[0]['dir'] ?? 'asc';


        $columns = ['idkaryawan', 'namakaryawan', 'nohp', 'alamat'];
        $orderBy = $columns[$orderColumn] ?? 'idkaryawan';


        $builder = $this->karyawanModel->builder();


        if (!empty($search)) {
            $builder->groupStart()
                ->like('idkaryawan', $search)
                ->orLike('namakaryawan', $search)
                ->orLike('nohp', $search)
                ->orLike('alamat', $search)
                ->groupEnd();
        }


        $recordsFiltered = $builder->countAllResults(false);


        $builder->orderBy($orderBy, $orderDir);
        $builder->limit($length, $start);

        $karyawan = $builder->get()->getResultArray();


        $recordsTotal = $this->karyawanModel->countAllResults();


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

    public function laporan()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $tanggal_cetak = $this->request->getGet('tanggal_cetak') ?? date('d/m/Y');


        $karyawan = $this->karyawanModel->orderBy('idkaryawan', 'ASC')->findAll();


        $data = [
            'title' => 'Laporan Data Karyawan',
            'subtitle' => 'Laporan data karyawan untuk admin dan pimpinan',
            'active' => 'laporan-karyawan',
            'karyawan' => $karyawan,
            'tanggal_cetak' => $tanggal_cetak,
            'total_karyawan' => count($karyawan)
        ];

        return view('admin/karyawan/laporan', $data);
    }

    public function exportPDF()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $tanggal_cetak = $this->request->getGet('tanggal_cetak') ?? date('d/m/Y');


        $karyawan = $this->karyawanModel->orderBy('idkaryawan', 'ASC')->findAll();


        $data = [
            'karyawan' => $karyawan,
            'tanggal_cetak' => $tanggal_cetak,
            'total_karyawan' => count($karyawan)
        ];


        require_once ROOTPATH . 'vendor/autoload.php';
        require_once APPPATH . 'Helpers/PdfHelper.php';

        $html = view('admin/karyawan/laporan_pdf', $data);
        $filename = 'Laporan_Data_Karyawan_' . str_replace('/', '-', $tanggal_cetak);
        
        $pdfResult = \App\Helpers\PdfHelper::generatePdf($html, $filename, 'A4', 'landscape');
        
        return \App\Helpers\PdfHelper::streamPdf($pdfResult, false);
    }
}
