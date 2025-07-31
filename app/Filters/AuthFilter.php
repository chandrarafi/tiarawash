<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Load helper cookie
        helper('cookie');

        if (!session()->get('logged_in')) {
            // Cek remember me cookie
            $userId = get_cookie('user_id');
            $rememberToken = get_cookie('remember_token');

            if ($userId && $rememberToken) {
                $userModel = new \App\Models\UserModel();
                $user = $userModel->find($userId);

                if ($user && $user['remember_token'] === $rememberToken) {
                    // Set session
                    $sessionData = [
                        'user_id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'name' => $user['name'],
                        'role' => $user['role'],
                        'logged_in' => true
                    ];
                    session()->set($sessionData);
                    return;
                }
            }

            // Simpan URL yang dicoba diakses hanya jika bukan halaman auth
            $currentUrl = current_url();
            if (!str_contains($currentUrl, '/auth')) {
                session()->set('redirect_url', $currentUrl);
            }

            return redirect()->to('auth');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
