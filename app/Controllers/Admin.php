<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;

class Admin extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $title = 'Dashboard';
        return view('admin/dashboard', compact('title'));
    }

    // User Management
    public function users()
    {
        $title = 'User Management';
        return view('admin/users/index', compact('title'));
    }

    public function getUsers()
    {
        $request = $this->request;

        // Parameters for DataTables
        $start = $request->getGet('start') ?? 0;
        $length = $request->getGet('length') ?? 10;
        $search = $request->getGet('search')['value'] ?? '';
        $order = $request->getGet('order') ?? [];
        $roleFilter = $request->getGet('role') ?? '';
        $statusFilter = $request->getGet('status') ?? '';

        $orderColumn = $order[0]['column'] ?? 0;
        $orderDir = $order[0]['dir'] ?? 'asc';

        // Columns for ordering
        $columns = ['id', 'username', 'email', 'name', 'role', 'status', 'last_login'];
        $orderBy = $columns[$orderColumn] ?? 'id';

        // Build query
        $builder = $this->userModel->builder();

        // Filtering
        if (!empty($search)) {
            $builder->groupStart()
                ->like('username', $search)
                ->orLike('email', $search)
                ->orLike('name', $search)
                ->orLike('role', $search)
                ->groupEnd();
        }

        // Role filter
        if (!empty($roleFilter)) {
            $builder->where('role', $roleFilter);
        }

        // Status filter
        if (!empty($statusFilter)) {
            $builder->where('status', $statusFilter);
        }

        // Count total records (without filters)
        $totalRecords = $this->userModel->countAll();

        // Count filtered records
        $filteredRecords = $builder->countAllResults(false);

        // Get data with limit, offset, order
        $data = $builder->orderBy($orderBy, $orderDir)
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        // Prepare response for DataTables
        $response = [
            'draw' => $request->getGet('draw') ?? 1,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];

        return $this->response->setJSON($response);
    }

    protected function handleUserSave($data, $isNew = true)
    {
        if ($this->userModel->save($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => $isNew ? 'User berhasil ditambahkan' : 'User berhasil diperbarui'
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status' => 'error',
            'message' => $isNew ? 'Gagal menambahkan user' : 'Gagal memperbarui user',
            'errors' => $this->userModel->errors()
        ]);
    }

    public function addUser()
    {
        return $this->handleUserSave($this->request->getPost(), true);
    }

    public function createUser()
    {
        return $this->handleUserSave($this->request->getJSON(true), true);
    }

    public function getUser($id = null)
    {
        $data = $this->userModel->find($id);

        if ($data) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data
            ]);
        }

        return $this->response->setStatusCode(404)->setJSON([
            'status' => 'error',
            'message' => 'User tidak ditemukan'
        ]);
    }

    public function updateUser($id = null)
    {
        $data = $this->request->getPost();

        // Pastikan ID selalu diset dengan benar
        if (!empty($id)) {
            $data['id'] = $id;
        } elseif (!empty($data['id'])) {
            $id = $data['id'];
        }

        // Validasi ID
        if (empty($id)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'ID user tidak valid',
                'errors' => ['id' => 'ID user tidak ditemukan']
            ]);
        }

        // Cek apakah user exists
        $existingUser = $this->userModel->find($id);
        if (!$existingUser) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'User tidak ditemukan',
                'errors' => ['id' => 'User dengan ID tersebut tidak ditemukan']
            ]);
        }

        return $this->handleUserSave($data, false);
    }

    public function deleteUser($id = null)
    {
        if ($this->userModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User berhasil dihapus'
            ]);
        }

        return $this->response->setStatusCode(400)->setJSON([
            'status' => 'error',
            'message' => 'Gagal menghapus user'
        ]);
    }

    public function getRoles()
    {
        // Daftar role yang tersedia
        $roles = ['admin', 'manager', 'user'];

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $roles
        ]);
    }
}
