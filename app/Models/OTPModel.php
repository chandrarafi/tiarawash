<?php

namespace App\Models;

use CodeIgniter\Model;

class OTPModel extends Model
{
    protected $table            = 'otp_codes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['email', 'otp_code', 'expires_at', 'is_used', 'purpose'];


    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    protected $validationRules = [
        'email'     => 'required|valid_email',
        'otp_code'  => 'required|exact_length[6]',
        'expires_at' => 'required',
        'purpose'   => 'required|in_list[registration,password_reset]'
    ];

    protected $validationMessages = [
        'email' => [
            'required'    => 'Email harus diisi',
            'valid_email' => 'Format email tidak valid',
        ],
        'otp_code' => [
            'required'     => 'Kode OTP harus diisi',
            'exact_length' => 'Kode OTP harus 6 digit',
        ],
        'expires_at' => [
            'required' => 'Waktu expired harus diisi',
        ],
        'purpose' => [
            'required' => 'Tujuan OTP harus diisi',
            'in_list'  => 'Tujuan OTP tidak valid',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;


    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Generate OTP code
     */
    public function generateOTP($email, $purpose = 'registration')
    {

        $this->where('email', $email)
            ->where('purpose', $purpose)
            ->where('is_used', 0)
            ->delete();


        $otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);


        $expiresAt = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $data = [
            'email'      => $email,
            'otp_code'   => $otpCode,
            'expires_at' => $expiresAt,
            'is_used'    => 0,
            'purpose'    => $purpose
        ];

        if ($this->insert($data)) {
            return $otpCode;
        }

        return false;
    }

    /**
     * Verify OTP code
     */
    public function verifyOTP($email, $otpCode, $purpose = 'registration')
    {
        $otp = $this->where('email', $email)
            ->where('otp_code', $otpCode)
            ->where('purpose', $purpose)
            ->where('is_used', 0)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->first();

        if ($otp) {

            $this->update($otp['id'], ['is_used' => 1]);
            return true;
        }

        return false;
    }

    /**
     * Clean expired OTP codes
     */
    public function cleanExpiredOTP()
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))
            ->orWhere('is_used', 1)
            ->delete();
    }
}
