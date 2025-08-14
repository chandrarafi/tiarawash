<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PelangganModel;
use App\Models\OTPModel;
use CodeIgniter\Email\Email;

class Auth extends BaseController
{
    protected $userModel;
    protected $pelangganModel;
    protected $otpModel;
    protected $email;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->pelangganModel = new PelangganModel();
        $this->otpModel = new OTPModel();
        $this->email = \Config\Services::email();

        helper('cookie');
    }

    public function index()
    {

        if (session()->get('logged_in')) {
            $redirectUrl = session()->get('redirect_url');


            session()->remove('redirect_url');


            $role = session()->get('role');
            $defaultRedirect = match ($role) {
                'pelanggan' => 'pelanggan/dashboard',
                'admin', 'pimpinan' => 'admin',
                default => 'admin'
            };


            if ($redirectUrl && !str_contains($redirectUrl, '/auth')) {
                return redirect()->to($redirectUrl);
            }

            return redirect()->to($defaultRedirect);
        }

        return view('auth/login');
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember') == 'on';


        $user = $this->userModel->where('username', $username)
            ->orWhere('email', $username)
            ->first();

        if ($user) {

            log_message('info', 'User found for login: ' . $username . ', Status: ' . $user['status']);

            if ($user['status'] !== 'active') {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Akun Anda tidak aktif. Silakan hubungi administrator.'
                ]);
            }


            log_message('info', 'Attempting password verification for user: ' . $username);
            if (password_verify($password, $user['password'])) {

                $this->userModel->update($user['id'], [
                    'last_login' => date('Y-m-d H:i:s')
                ]);


                $sessionData = [
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'logged_in' => true
                ];
                session()->set($sessionData);


                if ($remember) {
                    $this->setRememberMeCookie($user['id']);
                }


                $redirectUrl = session()->get('redirect_url');
                session()->remove('redirect_url');


                $defaultRedirect = match ($user['role']) {
                    'pelanggan' => site_url('pelanggan/dashboard'),
                    'admin', 'pimpinan' => site_url('admin'),
                    default => site_url('admin')
                };


                $finalRedirect = ($redirectUrl && !str_contains($redirectUrl, '/auth')) ? $redirectUrl : $defaultRedirect;

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Login berhasil',
                    'redirect' => $finalRedirect
                ]);
            } else {
                log_message('error', 'Password verification failed for user: ' . $username);
            }
        } else {
            log_message('error', 'User not found for login: ' . $username);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Username/Email atau Password salah'
        ]);
    }

    public function logout()
    {

        if (get_cookie('remember_token')) {
            delete_cookie('remember_token');
            delete_cookie('user_id');
        }


        session()->destroy();

        return redirect()->to('auth')->with('message', 'Anda telah berhasil logout');
    }

    protected function setRememberMeCookie($userId)
    {
        $token = bin2hex(random_bytes(32));


        $this->userModel->update($userId, [
            'remember_token' => $token
        ]);


        $expires = 30 * 24 * 60 * 60; // 30 hari
        $secure = isset($_SERVER['HTTPS']); // Set secure hanya jika HTTPS


        set_cookie(
            'remember_token',
            $token,
            $expires,
            '',  // domain
            '/', // path
            '', // prefix
            $secure, // secure - hanya set true jika HTTPS
            true  // httponly
        );


        set_cookie(
            'user_id',
            $userId,
            $expires,
            '',
            '/',
            '',
            $secure,
            true
        );
    }



    public function register()
    {

        if (session()->get('logged_in')) {
            return redirect()->to('admin');
        }

        return view('auth/register');
    }

    public function registerProcess()
    {
        $rules = [
            'name'     => 'required|min_length[3]|max_length[100]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
            'phone'    => 'permit_empty|max_length[15]',
            'address'  => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $email = $this->request->getPost('email');


        $otpCode = $this->otpModel->generateOTP($email, 'registration');

        if (!$otpCode) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal membuat kode OTP. Silakan coba lagi.'
            ]);
        }


        if ($this->sendOTPEmail($email, $otpCode, 'registration')) {

            session()->set('registration_data', [
                'name'     => $this->request->getPost('name'),
                'email'    => $email,
                'username' => $this->request->getPost('username'),
                'password' => $this->request->getPost('password'), // Don't hash here, let UserModel handle it
                'role'     => 'pelanggan',
                'phone'    => $this->request->getPost('phone') ?? '',
                'address'  => $this->request->getPost('address') ?? 'Belum diisi'
            ]);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Kode OTP telah dikirim ke email Anda. Silakan periksa inbox dan folder spam.',
                'redirect' => base_url('auth/verify')
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengirim email OTP. Silakan coba lagi.'
            ]);
        }
    }

    public function verify()
    {

        if (!session()->has('registration_data')) {
            return redirect()->to('auth/register')->with('error', 'Data registrasi tidak ditemukan. Silakan daftar ulang.');
        }

        return view('auth/verify');
    }

    public function verifyOTP()
    {
        $otpCode = $this->request->getPost('otp_code');
        $registrationData = session()->get('registration_data');

        if (!$registrationData) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data registrasi tidak ditemukan. Silakan daftar ulang.'
            ]);
        }

        if (!$otpCode || strlen($otpCode) !== 6) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Kode OTP harus 6 digit.'
            ]);
        }


        if ($this->otpModel->verifyOTP($registrationData['email'], $otpCode, 'registration')) {

            $db = \Config\Database::connect();
            $db->transStart();

            try {

                $userData = $registrationData;
                $userData['status'] = 'active';
                $userData['email_verified_at'] = date('Y-m-d H:i:s');

                if ($this->userModel->insert($userData)) {
                    $userId = $this->userModel->getInsertID();


                    if ($userData['role'] === 'pelanggan') {
                        $pelangganData = [
                            'kode_pelanggan' => $this->pelangganModel->generateKode(),
                            'user_id' => $userId,
                            'nama_pelanggan' => $userData['name'],
                            'no_hp' => $userData['phone'], // Use session data
                            'alamat' => $userData['address'] // Use session data
                        ];

                        if (!$this->pelangganModel->insert($pelangganData)) {
                            log_message('error', 'Failed to create pelanggan record for user ID: ' . $userId);
                            log_message('error', 'Pelanggan model errors: ' . json_encode($this->pelangganModel->errors()));
                            throw new \Exception('Gagal membuat data pelanggan');
                        }

                        log_message('info', 'Pelanggan record created successfully with kode: ' . $pelangganData['kode_pelanggan']);
                    }


                    $db->transComplete();

                    if ($db->transStatus() === false) {
                        throw new \Exception('Database transaction failed');
                    }


                    session()->remove('registration_data');


                    session()->remove('redirect_url');


                    $user = $this->userModel->where('email', $userData['email'])->first();
                    $this->setUserSession($user);


                    $redirectUrl = match ($user['role']) {
                        'pelanggan' => base_url('pelanggan/dashboard'),
                        'admin', 'pimpinan' => base_url('admin'),
                        default => base_url('admin')
                    };

                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Registrasi berhasil! Selamat datang di TiaraWash.',
                        'redirect' => $redirectUrl
                    ]);
                } else {
                    log_message('error', 'Failed to create user account');
                    log_message('error', 'User model errors: ' . json_encode($this->userModel->errors()));
                    throw new \Exception('Gagal membuat akun pengguna');
                }
            } catch (\Exception $e) {

                $db->transRollback();
                log_message('error', 'Registration transaction failed: ' . $e->getMessage());

                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal membuat akun. ' . $e->getMessage()
                ]);
            }
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Kode OTP tidak valid atau sudah kadaluarsa.'
            ]);
        }
    }

    public function resendOTP()
    {
        $registrationData = session()->get('registration_data');

        if (!$registrationData) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data registrasi tidak ditemukan. Silakan daftar ulang.'
            ]);
        }


        $otpCode = $this->otpModel->generateOTP($registrationData['email'], 'registration');

        if (!$otpCode) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal membuat kode OTP. Silakan coba lagi.'
            ]);
        }


        if ($this->sendOTPEmail($registrationData['email'], $otpCode, 'registration')) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Kode OTP baru telah dikirim ke email Anda.'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengirim email OTP. Silakan coba lagi.'
            ]);
        }
    }



    private function sendOTPEmail($email, $otpCode, $purpose)
    {
        try {
            $this->email->setTo($email);
            $this->email->setSubject('Kode Verifikasi TiaraWash');

            $message = $this->getOTPEmailTemplate($otpCode, $purpose);
            $this->email->setMessage($message);

            return $this->email->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send OTP email: ' . $e->getMessage());
            return false;
        }
    }

    private function getOTPEmailTemplate($otpCode, $purpose)
    {
        $title = $purpose === 'registration' ? 'Verifikasi Registrasi' : 'Reset Password';
        $description = $purpose === 'registration'
            ? 'Terima kasih telah mendaftar di TiaraWash. Gunakan kode OTP berikut untuk menyelesaikan registrasi Anda:'
            : 'Gunakan kode OTP berikut untuk mereset password Anda:';

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1'>
            <title>$title - TiaraWash</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #00aaff, #0077b6); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fc; padding: 30px; border-radius: 0 0 10px 10px; }
                .otp-code { background: #0088cc; color: white; font-size: 32px; font-weight: bold; padding: 20px; text-align: center; border-radius: 10px; margin: 20px 0; letter-spacing: 5px; }
                .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🚗 TiaraWash</h1>
                    <h2>$title</h2>
                </div>
                <div class='content'>
                    <p>$description</p>
                    
                    <div class='otp-code'>$otpCode</div>
                    
                    <div class='warning'>
                        <strong>⚠️ Penting:</strong>
                        <ul>
                            <li>Kode ini hanya berlaku selama 5 menit</li>
                            <li>Jangan bagikan kode ini kepada siapapun</li>
                            <li>Jika Anda tidak melakukan registrasi, abaikan email ini</li>
                        </ul>
                    </div>
                    
                    <p>Jika Anda mengalami masalah, silakan hubungi customer service kami.</p>
                </div>
                <div class='footer'>
                    <p>&copy; 2025 TiaraWash. Semua hak dilindungi.</p>
                    <p>Email ini dikirim secara otomatis, mohon tidak membalas email ini.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    private function setUserSession($user)
    {
        $sessionData = [
            'user_id'    => $user['id'],
            'username'   => $user['username'],
            'name'       => $user['name'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'logged_in'  => true
        ];

        session()->set($sessionData);
    }
}
