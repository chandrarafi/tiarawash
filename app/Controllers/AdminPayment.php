<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TransaksiModel;
use App\Models\BookingModel;
use App\Models\PelangganModel;
use App\Models\LayananModel;
use App\Models\UserModel;

class AdminPayment extends BaseController
{
    protected $transaksiModel;
    protected $bookingModel;
    protected $pelangganModel;
    protected $layananModel;
    protected $userModel;

    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
        $this->bookingModel = new BookingModel();
        $this->pelangganModel = new PelangganModel();
        $this->layananModel = new LayananModel();
        $this->userModel = new UserModel();
    }

    /**
     * Display list of pending payments
     */
    public function index()
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get pending payments with related data
        $pendingPayments = $this->transaksiModel
            ->select('transaksi.*, pelanggan.nama_pelanggan, booking.kode_booking, booking.no_plat, booking.tanggal as booking_tanggal, booking.jam as booking_jam')
            ->join('pelanggan', 'pelanggan.kode_pelanggan = transaksi.pelanggan_id', 'left')
            ->join('booking', 'booking.id = transaksi.booking_id', 'left')
            ->where('transaksi.status_pembayaran', 'belum_bayar')
            ->where('transaksi.bukti_pembayaran IS NOT NULL')
            ->orderBy('transaksi.created_at', 'DESC')
            ->findAll();

        // Get total counts for dashboard
        $stats = [
            'pending' => $this->transaksiModel->where('status_pembayaran', 'belum_bayar')->where('bukti_pembayaran IS NOT NULL')->countAllResults(),
            'approved' => $this->transaksiModel->where('status_pembayaran', 'dibayar')->countAllResults(),
            'rejected' => $this->transaksiModel->where('status_pembayaran', 'batal')->countAllResults(),
            'total' => $this->transaksiModel->countAllResults()
        ];

        $data = [
            'title' => 'Konfirmasi Pembayaran',
            'subtitle' => 'Kelola konfirmasi pembayaran pelanggan',
            'payments' => $pendingPayments,
            'stats' => $stats
        ];

        return view('admin/payment/index', $data);
    }

    /**
     * Display payment detail for review
     */
    public function detail($transaksiId)
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get transaction with full details
        $transaksi = $this->transaksiModel->find($transaksiId);

        if (!$transaksi) {
            return redirect()->to('admin/payment')->with('error', 'Transaksi tidak ditemukan');
        }

        // Get related data
        $pelanggan = $this->pelangganModel->where('kode_pelanggan', $transaksi['pelanggan_id'])->first();
        $booking = $this->bookingModel->find($transaksi['booking_id']);

        // Get all bookings with same kode_booking (for multi-service)
        $allBookings = [];
        if ($booking && is_array($booking)) {
            $allBookings = $this->bookingModel
                ->select('booking.*, layanan.nama_layanan, layanan.harga, layanan.durasi_menit, karyawan.namakaryawan')
                ->join('layanan', 'layanan.kode_layanan = booking.layanan_id', 'left')
                ->join('karyawan', 'karyawan.idkaryawan = booking.id_karyawan', 'left')
                ->where('booking.kode_booking', $booking['kode_booking'])
                ->orderBy('booking.jam', 'ASC')
                ->findAll();
        }

        // Get user info if available
        $user = null;
        if ($transaksi['user_id']) {
            $user = $this->userModel->find($transaksi['user_id']);
        }

        $data = [
            'title' => 'Detail Pembayaran',
            'subtitle' => 'Review pembayaran pelanggan',
            'transaksi' => $transaksi,
            'pelanggan' => $pelanggan,
            'booking' => $booking,
            'allBookings' => $allBookings,
            'user' => $user
        ];

        return view('admin/payment/detail', $data);
    }

    /**
     * Approve payment
     */
    public function approve($transaksiId)
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akses ditolak'
            ]);
        }

        $transaksi = $this->transaksiModel->find($transaksiId);

        if (!$transaksi) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan'
            ]);
        }

        if ($transaksi['status_pembayaran'] !== 'belum_bayar') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaksi sudah diproses sebelumnya'
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update payment status to approved
            $this->transaksiModel->update($transaksiId, [
                'status_pembayaran' => 'dibayar',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Update related booking status to confirmed
            if ($transaksi['booking_id']) {
                $booking = $this->bookingModel->find($transaksi['booking_id']);
                if ($booking && is_array($booking)) {
                    // Update all bookings with same kode_booking
                    $this->bookingModel->where('kode_booking', $booking['kode_booking'])
                        ->set(['status' => 'dikonfirmasi'])
                        ->update();

                    log_message('info', "Admin approved payment {$transaksi['no_transaksi']}, booking {$booking['kode_booking']} confirmed");
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pembayaran berhasil dikonfirmasi'
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Payment approval failed: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal mengkonfirmasi pembayaran: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reject payment
     */
    public function reject($transaksiId)
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akses ditolak'
            ]);
        }

        $transaksi = $this->transaksiModel->find($transaksiId);

        if (!$transaksi) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaksi tidak ditemukan'
            ]);
        }

        if ($transaksi['status_pembayaran'] !== 'belum_bayar') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Transaksi sudah diproses sebelumnya'
            ]);
        }

        $alasan = $this->request->getPost('alasan') ?? 'Bukti pembayaran tidak valid';

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update payment status to rejected
            $this->transaksiModel->update($transaksiId, [
                'status_pembayaran' => 'batal',
                'catatan' => $alasan,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Update related booking status to cancelled
            if ($transaksi['booking_id']) {
                $booking = $this->bookingModel->find($transaksi['booking_id']);
                if ($booking && is_array($booking)) {
                    // Update all bookings with same kode_booking
                    $this->bookingModel->where('kode_booking', $booking['kode_booking'])
                        ->set(['status' => 'batal'])
                        ->update();

                    log_message('info', "Admin rejected payment {$transaksi['no_transaksi']}, booking {$booking['kode_booking']} cancelled. Reason: {$alasan}");
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Pembayaran berhasil ditolak'
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Payment rejection failed: ' . $e->getMessage());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Gagal menolak pembayaran: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get payment statistics
     */
    public function stats()
    {
        // Check admin permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Akses ditolak'
            ]);
        }

        $stats = [
            'pending' => $this->transaksiModel->where('status_pembayaran', 'belum_bayar')->where('bukti_pembayaran IS NOT NULL')->countAllResults(),
            'approved_today' => $this->transaksiModel->where('status_pembayaran', 'dibayar')->where('DATE(updated_at)', date('Y-m-d'))->countAllResults(),
            'rejected_today' => $this->transaksiModel->where('status_pembayaran', 'batal')->where('DATE(updated_at)', date('Y-m-d'))->countAllResults(),
            'total_revenue_today' => $this->transaksiModel->where('status_pembayaran', 'dibayar')->where('DATE(updated_at)', date('Y-m-d'))->selectSum('total_harga')->first()['total_harga'] ?? 0
        ];

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $stats
        ]);
    }
}
