<?php

namespace App\Controllers;

use App\Models\LayananModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Layanan extends BaseController
{
    protected $layananModel;
    protected $validation;

    public function __construct()
    {
        $this->layananModel = new LayananModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {
        $data = [
            'title' => 'Daftar Layanan',
            'layanan' => $this->layananModel->findAll()
        ];

        return view('admin/layanan/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Layanan Baru',
            'validation' => $this->validation
        ];

        return view('admin/layanan/create', $data);
    }

    public function store()
    {

        if ($this->request->isAJAX()) {
            return $this->storeAjax();
        }


        $rules = $this->layananModel->getValidationRules();


        $foto = $this->request->getFile('foto');
        if (!$foto || !$foto->isValid()) {
            unset($rules['foto']);
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }


        $fotoName = null;
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $fotoName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/layanan', $fotoName);
        }


        $this->layananModel->save([
            'kode_layanan' => $this->request->getPost('kode_layanan'),
            'nama_layanan' => $this->request->getPost('nama_layanan'),
            'jenis_kendaraan' => $this->request->getPost('jenis_kendaraan'),
            'harga' => $this->request->getPost('harga'),
            'durasi_menit' => $this->request->getPost('durasi_menit'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'foto' => $fotoName,
            'status' => $this->request->getPost('status') ?? 'aktif'
        ]);

        session()->setFlashdata('success', 'Layanan berhasil ditambahkan');
        return redirect()->to('/admin/layanan');
    }

    private function storeAjax()
    {
        $response = [
            'status' => false,
            'message' => '',
            'errors' => [],
            'data' => []
        ];

        try {

            log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));


            $postData = $this->request->getPost();


            if (empty($postData['kode_layanan'])) {
                $response['message'] = 'Kode layanan tidak boleh kosong';
                return $this->response->setJSON($response);
            }

            if (empty($postData['nama_layanan'])) {
                $response['message'] = 'Nama layanan tidak boleh kosong';
                return $this->response->setJSON($response);
            }


            $fotoName = null;
            $foto = $this->request->getFile('foto');
            log_message('debug', 'File info: ' . json_encode([
                'name' => $foto ? $foto->getName() : 'null',
                'size' => $foto ? $foto->getSize() : 'null',
                'isValid' => $foto ? $foto->isValid() : 'null',
                'hasMoved' => $foto ? $foto->hasMoved() : 'null'
            ]));

            if ($foto && $foto->isValid() && !$foto->hasMoved()) {

                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!in_array($foto->getMimeType(), $allowedTypes)) {
                    $response['message'] = 'Format file tidak didukung. Gunakan JPG, JPEG, atau PNG.';
                    return $this->response->setJSON($response);
                }

                if ($foto->getSize() > 2048000) { // 2MB
                    $response['message'] = 'Ukuran file terlalu besar. Maksimal 2MB.';
                    return $this->response->setJSON($response);
                }


                $uploadPath = FCPATH . 'uploads/layanan';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $fotoName = $foto->getRandomName();
                if (!$foto->move($uploadPath, $fotoName)) {
                    $response['message'] = 'Gagal mengupload foto';
                    return $this->response->setJSON($response);
                }

                log_message('debug', 'Photo uploaded successfully: ' . $fotoName);
            }


            $data = [
                'kode_layanan' => $postData['kode_layanan'],
                'nama_layanan' => $postData['nama_layanan'],
                'jenis_kendaraan' => $postData['jenis_kendaraan'] ?? 'motor',
                'harga' => (int)($postData['harga'] ?? 0),
                'durasi_menit' => (int)($postData['durasi_menit'] ?? 60),
                'deskripsi' => $postData['deskripsi'] ?? '',
                'foto' => $fotoName,
                'status' => $postData['status'] ?? 'aktif'
            ];


            log_message('debug', 'Data to save: ' . json_encode($data));


            $result = $this->layananModel->insert($data);

            log_message('debug', 'Insert result: ' . ($result ? 'true' : 'false'));
            if ($result) {
                $response['status'] = true;
                $response['message'] = 'Layanan berhasil ditambahkan';
                $response['data'] = $data;
            } else {
                $errors = $this->layananModel->errors();
                log_message('debug', 'Model errors: ' . json_encode($errors));
                if (!empty($errors)) {
                    $response['message'] = 'Validasi model gagal';
                    $response['errors'] = $errors;
                } else {
                    $response['message'] = 'Gagal menyimpan data ke database. Periksa log untuk detail.';
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in storeAjax: ' . $e->getMessage());
            $response['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $this->response->setJSON($response);
    }

    public function edit($kode = null)
    {
        $layanan = $this->layananModel->find($kode);

        if (!$layanan) {
            throw new PageNotFoundException('Layanan dengan kode ' . $kode . ' tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Layanan',
            'layanan' => $layanan,
            'validation' => $this->validation
        ];

        return view('admin/layanan/edit', $data);
    }

    public function update($kode = null)
    {

        if ($this->request->isAJAX()) {
            return $this->updateAjax($kode);
        }


        $existingLayanan = $this->layananModel->find($kode);
        if (!$existingLayanan) {
            throw new PageNotFoundException('Layanan dengan kode ' . $kode . ' tidak ditemukan');
        }


        $rules = $this->layananModel->getValidationRulesForEdit($kode);


        $foto = $this->request->getFile('foto');
        if (!$foto || !$foto->isValid()) {
            unset($rules['foto']);
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }


        $fotoName = $existingLayanan['foto']; // Keep existing foto
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {

            if ($existingLayanan['foto'] && file_exists(FCPATH . 'uploads/layanan/' . $existingLayanan['foto'])) {
                unlink(FCPATH . 'uploads/layanan/' . $existingLayanan['foto']);
            }

            $fotoName = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/layanan', $fotoName);
        }


        $this->layananModel->update($kode, [
            'kode_layanan' => $this->request->getPost('kode_layanan'),
            'nama_layanan' => $this->request->getPost('nama_layanan'),
            'jenis_kendaraan' => $this->request->getPost('jenis_kendaraan'),
            'harga' => $this->request->getPost('harga'),
            'durasi_menit' => $this->request->getPost('durasi_menit'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'foto' => $fotoName,
            'status' => $this->request->getPost('status')
        ]);

        session()->setFlashdata('success', 'Layanan berhasil diperbarui');
        return redirect()->to('/admin/layanan');
    }

    private function updateAjax($kode)
    {
        $response = [
            'status' => false,
            'message' => '',
            'errors' => [],
            'data' => []
        ];

        try {

            log_message('debug', 'UPDATE POST data: ' . json_encode($this->request->getPost()));


            $existingLayanan = $this->layananModel->find($kode);
            if (!$existingLayanan) {
                $response['message'] = 'Layanan dengan kode ' . $kode . ' tidak ditemukan';
                return $this->response->setJSON($response);
            }


            $postData = $this->request->getPost();


            if (empty($postData['nama_layanan'])) {
                $response['message'] = 'Nama layanan tidak boleh kosong';
                return $this->response->setJSON($response);
            }


            $fotoName = $existingLayanan['foto']; // Keep existing photo by default
            $foto = $this->request->getFile('foto');

            log_message('debug', 'File info: ' . json_encode([
                'name' => $foto ? $foto->getName() : 'null',
                'size' => $foto ? $foto->getSize() : 'null',
                'isValid' => $foto ? $foto->isValid() : 'null',
                'hasMoved' => $foto ? $foto->hasMoved() : 'null'
            ]));

            if ($foto && $foto->isValid() && !$foto->hasMoved()) {

                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!in_array($foto->getMimeType(), $allowedTypes)) {
                    $response['message'] = 'Format file tidak didukung. Gunakan JPG, JPEG, atau PNG.';
                    return $this->response->setJSON($response);
                }

                if ($foto->getSize() > 2048000) { // 2MB
                    $response['message'] = 'Ukuran file terlalu besar. Maksimal 2MB.';
                    return $this->response->setJSON($response);
                }


                if ($existingLayanan['foto'] && file_exists(FCPATH . 'uploads/layanan/' . $existingLayanan['foto'])) {
                    unlink(FCPATH . 'uploads/layanan/' . $existingLayanan['foto']);
                    log_message('debug', 'Old photo deleted: ' . $existingLayanan['foto']);
                }


                $uploadPath = FCPATH . 'uploads/layanan';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $fotoName = $foto->getRandomName();
                if (!$foto->move($uploadPath, $fotoName)) {
                    $response['message'] = 'Gagal mengupload foto';
                    return $this->response->setJSON($response);
                }

                log_message('debug', 'New photo uploaded successfully: ' . $fotoName);
            }


            $rules = $this->layananModel->getValidationRulesForEdit($kode);


            if (!$foto || !$foto->isValid()) {
                unset($rules['foto']);
            }


            $validationData = [
                'kode_layanan' => $postData['kode_layanan'],
                'nama_layanan' => $postData['nama_layanan'],
                'jenis_kendaraan' => $postData['jenis_kendaraan'] ?? 'motor',
                'harga' => (int)($postData['harga'] ?? 0),
                'durasi_menit' => (int)($postData['durasi_menit'] ?? 60),
                'deskripsi' => $postData['deskripsi'] ?? '',
                'status' => $postData['status'] ?? 'aktif'
            ];

            if (!$this->validate($rules, $validationData)) {
                $response['message'] = 'Validasi gagal';
                $response['errors'] = $this->validator->getErrors();
                return $this->response->setJSON($response);
            }


            $data = [
                'kode_layanan' => $postData['kode_layanan'],
                'nama_layanan' => $postData['nama_layanan'],
                'jenis_kendaraan' => $postData['jenis_kendaraan'] ?? 'motor',
                'harga' => (int)($postData['harga'] ?? 0),
                'durasi_menit' => (int)($postData['durasi_menit'] ?? 60),
                'deskripsi' => $postData['deskripsi'] ?? '',
                'foto' => $fotoName,
                'status' => $postData['status'] ?? 'aktif'
            ];


            log_message('debug', 'Data to update: ' . json_encode($data));


            $this->layananModel->skipValidation();
            if ($this->layananModel->update($kode, $data)) {
                $response['status'] = true;
                $response['message'] = 'Layanan berhasil diperbarui';
                $response['data'] = $data;
            } else {
                $errors = $this->layananModel->errors();
                log_message('debug', 'Model errors: ' . json_encode($errors));
                if (!empty($errors)) {
                    $response['message'] = 'Validasi model gagal';
                    $response['errors'] = $errors;
                } else {
                    $response['message'] = 'Gagal memperbarui data ke database. Periksa log untuk detail.';
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception in updateAjax: ' . $e->getMessage());
            $response['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        return $this->response->setJSON($response);
    }

    public function delete($kode = null)
    {

        if ($this->request->isAJAX() || $this->request->getMethod() === 'delete') {
            return $this->deleteAjax($kode);
        }

        $layanan = $this->layananModel->find($kode);

        if (!$layanan) {
            throw new PageNotFoundException('Layanan dengan kode ' . $kode . ' tidak ditemukan');
        }


        if ($layanan['foto'] && file_exists(FCPATH . 'uploads/layanan/' . $layanan['foto'])) {
            unlink(FCPATH . 'uploads/layanan/' . $layanan['foto']);
        }

        $this->layananModel->delete($kode);

        session()->setFlashdata('success', 'Layanan berhasil dihapus');
        return redirect()->to('/admin/layanan');
    }

    private function deleteAjax($kode = null)
    {
        $layanan = $this->layananModel->find($kode);

        if (!$layanan) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Layanan dengan kode ' . $kode . ' tidak ditemukan'
            ])->setStatusCode(404);
        }

        try {

            if ($layanan['foto'] && file_exists(FCPATH . 'uploads/layanan/' . $layanan['foto'])) {
                unlink(FCPATH . 'uploads/layanan/' . $layanan['foto']);
            }

            $this->layananModel->delete($kode);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Layanan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menghapus layanan: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function getLayananByJenis()
    {
        $jenisKendaraan = $this->request->getPost('jenis_kendaraan');
        $layanan = $this->layananModel->getLayananByJenisKendaraan($jenisKendaraan);

        return $this->response->setJSON($layanan);
    }

    public function foto($filename = null)
    {
        if (!$filename) {
            throw new PageNotFoundException('File tidak ditemukan');
        }

        $filepath = FCPATH . 'uploads/layanan/' . $filename;

        if (!file_exists($filepath)) {
            throw new PageNotFoundException('File tidak ditemukan');
        }

        $mime = mime_content_type($filepath);

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Length', filesize($filepath))
            ->setBody(file_get_contents($filepath));
    }
}
