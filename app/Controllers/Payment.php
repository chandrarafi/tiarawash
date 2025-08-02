<?php

namespace App\Controllers;

use App\Models\BookingModel;
use App\Models\PelangganModel;
use App\Models\LayananModel;
use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;
use CodeIgniter\HTTP\ResponseInterface;

class Payment extends BaseController
{
    protected $bookingModel;
    protected $pelangganModel;
    protected $layananModel;
    protected $transaksiModel;
    protected $detailTransaksiModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->pelangganModel = new PelangganModel();
        $this->layananModel = new LayananModel();
        $this->transaksiModel = new TransaksiModel();
        $this->detailTransaksiModel = new DetailTransaksiModel();
    }

    /**
     * Display payment page for a booking
     */
    public function index($kodeBooking = null)
    {
        if (!$kodeBooking) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kode booking tidak ditemukan');
        }

        // Auto-cancel expired bookings first
        $this->bookingModel->cancelExpiredBookings();

        // Get all bookings with this kode_booking (multiple services)
        $bookings = $this->bookingModel->where('kode_booking', $kodeBooking)->findAll();

        if (empty($bookings)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Booking tidak ditemukan');
        }

        // Check if booking is expired
        $paymentInfo = $this->bookingModel->getPaymentInfo($kodeBooking);
        if ($paymentInfo && $paymentInfo['is_expired']) {
            return view('payment/expired', [
                'title' => 'Pembayaran Expired - TiaraWash',
                'kode_booking' => $kodeBooking,
                'expires_at' => $paymentInfo['expires_at']
            ]);
        }

        // Check if already paid
        $existingTransaksi = $this->transaksiModel->where('booking_id', $bookings[0]['id'])->first();
        if ($existingTransaksi && $existingTransaksi['status_pembayaran'] === 'dibayar') {
            session()->setFlashdata('error', 'Booking ini sudah dibayar');
            return redirect()->to('/');
        }

        // Get booking details with layanan info
        $bookingDetails = [];
        $totalHarga = 0;
        $pelangganInfo = null;

        foreach ($bookings as $booking) {
            $layanan = $this->layananModel->find($booking['layanan_id']);
            $bookingDetails[] = [
                'booking' => $booking,
                'layanan' => $layanan
            ];
            $totalHarga += (float)$layanan['harga'];

            // Get pelanggan info (same for all bookings)
            if (!$pelangganInfo) {
                $pelangganInfo = $this->pelangganModel->find($booking['pelanggan_id']);
            }
        }

        $data = [
            'title' => 'Pembayaran - TiaraWash',
            'kode_booking' => $kodeBooking,
            'booking_details' => $bookingDetails,
            'pelanggan' => $pelangganInfo,
            'total_harga' => $totalHarga,
            'payment_methods' => $this->getPaymentMethods(),
            'payment_info' => $paymentInfo
        ];

        return view('payment/index', $data);
    }

    /**
     * Process payment
     */
    public function process()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/');
        }

        $rules = [
            'kode_booking' => 'required',
            'metode_pembayaran' => 'required|in_list[transfer]'
        ];

        // Add bukti_pembayaran validation only if file is uploaded
        $buktiFile = $this->request->getFile('bukti_pembayaran');
        if ($buktiFile && $buktiFile->isValid() && !$buktiFile->hasMoved()) {
            $rules['bukti_pembayaran'] = 'uploaded[bukti_pembayaran]|max_size[bukti_pembayaran,2048]|ext_in[bukti_pembayaran,jpg,jpeg,png,pdf]';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak valid',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $kodeBooking = $this->request->getPost('kode_booking');
        $metodePembayaran = $this->request->getPost('metode_pembayaran');

        // Get all bookings with this kode_booking
        $bookings = $this->bookingModel->where('kode_booking', $kodeBooking)->findAll();

        if (empty($bookings)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking tidak ditemukan'
            ]);
        }

        // Check if already has transaction (pending or paid)
        $existingTransaksi = null;
        foreach ($bookings as $booking) {
            $transaksi = $this->transaksiModel->where('booking_id', $booking['id'])->first();
            if ($transaksi) {
                $existingTransaksi = $transaksi;
                break;
            }
        }

        if ($existingTransaksi) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Booking ini sudah memiliki transaksi pembayaran'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Calculate total amount
            $totalHarga = 0;
            $pelangganId = $bookings[0]['pelanggan_id'];
            $firstBooking = $bookings[0];

            foreach ($bookings as $booking) {
                $layanan = $this->layananModel->find($booking['layanan_id']);
                $totalHarga += (float)$layanan['harga'];
            }

            // Handle file upload for bukti pembayaran
            $buktiPembayaranPath = null;
            $buktiFile = $this->request->getFile('bukti_pembayaran');

            if ($buktiFile && $buktiFile->isValid() && !$buktiFile->hasMoved()) {
                // Create uploads directory if not exists
                $uploadPath = FCPATH . 'uploads/bukti_pembayaran/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Generate unique filename
                $newName = 'bukti_' . $kodeBooking . '_' . time() . '.' . $buktiFile->getExtension();

                if ($buktiFile->move($uploadPath, $newName)) {
                    $buktiPembayaranPath = 'uploads/bukti_pembayaran/' . $newName;
                    log_message('info', 'Bukti pembayaran uploaded: ' . $buktiPembayaranPath);
                } else {
                    log_message('error', 'Failed to upload bukti pembayaran for booking: ' . $kodeBooking);
                }
            }

            // Determine payment status based on whether bukti_pembayaran is uploaded
            $statusPembayaran = 'belum_bayar'; // Default status
            if ($buktiPembayaranPath) {
                $statusPembayaran = 'dibayar'; // Mark as paid if proof is uploaded
                log_message('info', 'Payment proof uploaded, setting status to dibayar');
            }

            // Create main transaction - normalized data structure
            $transaksiData = [
                'tanggal' => date('Y-m-d'),
                'booking_id' => $firstBooking['id'], // Reference to first booking
                // 'pelanggan_id' => $pelangganId, // REMOVED: redundant, use booking.pelanggan_id
                'layanan_id' => $firstBooking['layanan_id'], // Main service
                // 'no_plat' => $firstBooking['no_plat'], // REMOVED: redundant, use booking.no_plat
                // 'jenis_kendaraan' => $firstBooking['jenis_kendaraan'], // REMOVED: redundant, use layanan.jenis_kendaraan
                'total_harga' => $totalHarga,
                'metode_pembayaran' => $metodePembayaran,
                'status_pembayaran' => $statusPembayaran,
                'catatan' => 'Pembayaran untuk ' . count($bookings) . ' layanan',
                'bukti_pembayaran' => $buktiPembayaranPath,
                'user_id' => session()->get('user_id') ?? null
            ];

            // Debug log transaksi data
            log_message('info', 'Attempting to insert transaksi with data: ' . json_encode($transaksiData));

            if (!$this->transaksiModel->insert($transaksiData)) {
                $errors = $this->transaksiModel->errors();
                log_message('error', 'TransaksiModel insert failed with errors: ' . json_encode($errors));
                log_message('error', 'TransaksiModel validation rules: ' . json_encode($this->transaksiModel->getRawValidationRules()));
                throw new \Exception('Gagal menyimpan transaksi: ' . json_encode($errors));
            }

            $transaksiId = $this->transaksiModel->getInsertID();

            // Create detail transactions for each service
            foreach ($bookings as $booking) {
                $layanan = $this->layananModel->find($booking['layanan_id']);

                $detailData = [
                    'transaksi_id' => $transaksiId,
                    'jenis_item' => 'layanan',
                    'item_id' => $layanan['kode_layanan'],
                    'nama_item' => $layanan['nama_layanan'],
                    'harga' => $layanan['harga'],
                    'jumlah' => 1,
                    'subtotal' => $layanan['harga']
                ];

                log_message('info', 'Attempting to insert detail transaksi: ' . json_encode($detailData));

                if (!$this->detailTransaksiModel->insert($detailData)) {
                    $detailErrors = $this->detailTransaksiModel->errors();
                    log_message('error', 'DetailTransaksiModel insert failed with errors: ' . json_encode($detailErrors));
                    throw new \Exception('Gagal menyimpan detail transaksi: ' . json_encode($detailErrors));
                }

                // Update booking status to 'dikonfirmasi'
                $this->bookingModel->update($booking['id'], ['status' => 'diproses']);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            // Get the created transaction
            $transaksi = $this->transaksiModel->find($transaksiId);

            if (!$transaksi || !is_array($transaksi)) {
                throw new \Exception('Transaksi tidak ditemukan setelah dibuat');
            }

            $noTransaksi = $transaksi['no_transaksi'] ?? 'TRX-ERROR';

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pembayaran berhasil diproses!',
                'data' => [
                    'no_transaksi' => $noTransaksi,
                    'total_harga' => $totalHarga,
                    'metode_pembayaran' => $metodePembayaran,
                    'redirect_url' => site_url('payment/success/' . $noTransaksi)
                ]
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Payment processing failed: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Payment success page
     */
    public function success($noTransaksi = null)
    {
        if (!$noTransaksi) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Nomor transaksi tidak ditemukan');
        }

        log_message('info', 'Looking for transaksi with no_transaksi: ' . $noTransaksi);

        $transaksi = $this->transaksiModel->getTransaksiWithDetails($noTransaksi);

        log_message('info', 'Transaksi result: ' . json_encode($transaksi));

        if (!$transaksi) {
            // Fallback: Try basic lookup without JOIN
            log_message('info', 'Trying fallback lookup without JOIN');
            $basicTransaksi = $this->transaksiModel->where('no_transaksi', $noTransaksi)->first();

            if ($basicTransaksi) {
                log_message('info', 'Basic transaksi found, manually building data');

                // Get related data via normalized method
                $transaksiWithDetails = $this->transaksiModel->getTransaksiWithDetails($transaksi['id']);

                $pelanggan = null;
                $layanan = null;
                $user = null;

                // Extract data from normalized result
                if ($transaksiWithDetails) {
                    if ($transaksiWithDetails['nama_pelanggan']) {
                        $pelanggan = [
                            'kode_pelanggan' => $transaksiWithDetails['pelanggan_id'],
                            'nama_pelanggan' => $transaksiWithDetails['nama_pelanggan'],
                            'no_hp' => $transaksiWithDetails['pelanggan_hp'],
                            'alamat' => $transaksiWithDetails['pelanggan_alamat']
                        ];
                    }

                    if ($transaksiWithDetails['nama_layanan']) {
                        $layanan = [
                            'kode_layanan' => $basicTransaksi['booking_id'], // Use booking_id as reference
                            'nama_layanan' => $transaksiWithDetails['nama_layanan'],
                            'harga' => $transaksiWithDetails['layanan_harga'],
                            'jenis_kendaraan' => $transaksiWithDetails['jenis_kendaraan']
                        ];
                    }
                }

                if ($basicTransaksi['user_id']) {
                    $userModel = new \App\Models\UserModel();
                    $user = $userModel->find($basicTransaksi['user_id']);
                }

                // Build combined result
                $transaksi = $basicTransaksi;
                $transaksi['nama_pelanggan'] = $pelanggan ? $pelanggan['nama_pelanggan'] : null;
                $transaksi['nama_layanan'] = $layanan ? $layanan['nama_layanan'] : null;
                $transaksi['nama_kasir'] = $user ? $user['name'] : null;

                log_message('info', 'Fallback transaksi built successfully');
            } else {
                log_message('error', 'Transaksi not found even with basic lookup for: ' . $noTransaksi);
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Transaksi tidak ditemukan');
            }
        }

        // Get detail transactions
        $details = $this->detailTransaksiModel->getDetailByTransaksi($transaksi['id']);

        // Get karyawan name from booking
        $karyawanName = null;
        if (isset($transaksi['booking_id'])) {
            $bookingModel = new \App\Models\BookingModel();
            $booking = $bookingModel->find($transaksi['booking_id']);

            if ($booking && $booking['id_karyawan']) {
                $karyawanModel = new \App\Models\KaryawanModel();
                $karyawan = $karyawanModel->where('idkaryawan', $booking['id_karyawan'])->first();

                if ($karyawan) {
                    $karyawanName = $karyawan['namakaryawan'];
                    log_message('info', 'Found karyawan for receipt: ' . $karyawanName);
                }
            }
        }

        // Add karyawan name to transaksi data
        $transaksi['nama_karyawan'] = $karyawanName;

        // Get all booking details for multi-service support
        $bookingDetails = [];
        if (isset($transaksi['booking_id'])) {
            $bookingModel = new \App\Models\BookingModel();
            $booking = $bookingModel->find($transaksi['booking_id']);

            if ($booking && $booking['kode_booking']) {
                // Get all bookings with the same kode_booking
                $allBookings = $bookingModel->getBookingsByKodeBooking($booking['kode_booking']);

                foreach ($allBookings as $bookingItem) {
                    $layanan = $this->layananModel->find($bookingItem['layanan_id']);
                    if ($layanan) {
                        $bookingDetails[] = [
                            'booking' => $bookingItem,
                            'layanan' => $layanan
                        ];
                    }
                }
            }
        }

        // Generate QR Code for payment info
        $qrData = $this->generatePaymentQRData($transaksi);
        $qrCodeImage = $this->generateQRCode($qrData);

        $data = [
            'title' => 'Pembayaran Berhasil - TiaraWash',
            'transaksi' => $transaksi,
            'details' => $details,
            'booking_details' => $bookingDetails,
            'qr_code' => $qrCodeImage
        ];

        return view('payment/success', $data);
    }

    /**
     * Get available payment methods
     */
    private function getPaymentMethods()
    {
        return [
            'transfer' => [
                'name' => 'Transfer Bank',
                'icon' => 'fas fa-university',
                'description' => 'Transfer ke rekening TiaraWash',
                'account_info' => [
                    'bank' => 'Bank Mandiri',
                    'account_number' => '1234567890',
                    'account_name' => 'TiaraWash Car & Motor Wash'
                ]
            ]
        ];
    }

    /**
     * Generate payment QR data
     */
    private function generatePaymentQRData($transaksi)
    {
        return json_encode([
            'no_transaksi' => $transaksi['no_transaksi'],
            'tanggal' => $transaksi['tanggal'],
            'pelanggan' => $transaksi['nama_pelanggan'],
            'total' => $transaksi['total_harga'],
            'status' => $transaksi['status_pembayaran'],
            'bank' => 'Bank Mandiri',
            'rekening' => '1234567890',
            'nama_rekening' => 'TiaraWash Car & Motor Wash'
        ]);
    }

    /**
     * Generate QR Code image
     */
    private function generateQRCode($data)
    {
        try {
            // Try multiple QR code generation methods

            // Method 1: Try BaconQrCode directly
            if (class_exists('\BaconQrCode\Writer') && class_exists('\BaconQrCode\Renderer\ImageRenderer')) {
                try {
                    $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                        new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
                        new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
                    );
                    $writer = new \BaconQrCode\Writer($renderer);
                    $svg = $writer->writeString($data);

                    log_message('info', 'QR Code generated using BaconQrCode');
                    return 'data:image/svg+xml;base64,' . base64_encode($svg);
                } catch (\Exception $e) {
                    log_message('warning', 'BaconQrCode failed: ' . $e->getMessage());
                }
            }

            // Method 2: Try online QR service as backup (optional)
            // Note: Comment this out if you don't want external dependencies
            /*
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($data);
            $qrImage = @file_get_contents($qrUrl);
            if ($qrImage !== false) {
                log_message('info', 'QR Code generated using online service');
                return 'data:image/png;base64,' . base64_encode($qrImage);
            }
            */

            // Method 3: Always use our beautiful fallback
            log_message('info', 'Using QR Code fallback placeholder');
            return $this->generateSimpleQRFallback($data);
        } catch (\Exception $e) {
            log_message('error', 'All QR Code generation methods failed: ' . $e->getMessage());
            return $this->generateSimpleQRFallback($data);
        }
    }

    private function generateSimpleQRFallback($data)
    {
        // Simple fallback - create a professional placeholder with transaction info
        $lines = explode("\n", $data);
        $noTransaksi = '';
        $tanggal = '';
        $total = '';

        // Extract key info from data
        foreach ($lines as $line) {
            if (strpos($line, 'No. Transaksi:') !== false) {
                $noTransaksi = trim(str_replace('No. Transaksi:', '', $line));
            }
            if (strpos($line, 'Tanggal:') !== false) {
                $tanggal = trim(str_replace('Tanggal:', '', $line));
            }
            if (strpos($line, 'Total:') !== false) {
                $total = trim(str_replace('Total:', '', $line));
            }
        }

        $svg = '<?xml version="1.0" encoding="UTF-8"?>
        <svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
            <!-- Background -->
            <defs>
                <linearGradient id="bgGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
                </linearGradient>
            </defs>
            <rect width="200" height="200" fill="url(#bgGradient)" stroke="#333" stroke-width="2"/>
            
            <!-- QR Pattern mockup -->
            <rect x="20" y="20" width="160" height="160" fill="white" rx="10"/>
            <rect x="30" y="30" width="20" height="20" fill="black"/>
            <rect x="150" y="30" width="20" height="20" fill="black"/>
            <rect x="30" y="150" width="20" height="20" fill="black"/>
            
            <!-- Transaction Info -->
            <text x="100" y="75" text-anchor="middle" font-family="Arial" font-size="10" font-weight="bold" fill="black">INVOICE QR</text>
            <text x="100" y="95" text-anchor="middle" font-family="Arial" font-size="8" fill="black">' . esc($noTransaksi) . '</text>
            <text x="100" y="110" text-anchor="middle" font-family="Arial" font-size="7" fill="black">' . esc($tanggal) . '</text>
            <text x="100" y="125" text-anchor="middle" font-family="Arial" font-size="8" font-weight="bold" fill="black">' . esc($total) . '</text>
            <text x="100" y="145" text-anchor="middle" font-family="Arial" font-size="6" fill="gray">TiaraWash Car Wash</text>
        </svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
