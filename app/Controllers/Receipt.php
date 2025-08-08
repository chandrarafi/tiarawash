<?php

namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\BookingModel;
use App\Models\LayananModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Receipt extends BaseController
{
    protected $transaksiModel;
    protected $bookingModel;
    protected $layananModel;

    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
        $this->bookingModel = new BookingModel();
        $this->layananModel = new LayananModel();
    }

    /**
     * Generate PDF receipt
     */
    public function pdf($noTransaksi)
    {
        try {
            // Get transaction data
            $transaksi = $this->transaksiModel
                ->select('transaksi.*, booking.pelanggan_id, booking.no_plat, booking.merk_kendaraan, pelanggan.nama_pelanggan, pelanggan.no_hp')
                ->join('booking', 'booking.id = transaksi.booking_id', 'left')
                ->join('pelanggan', 'pelanggan.kode_pelanggan = booking.pelanggan_id', 'left')
                ->where('transaksi.no_transaksi', $noTransaksi)
                ->first();

            if (!$transaksi) {
                throw new \Exception('Transaksi tidak ditemukan');
            }

            // Get booking details - use kode_booking from main booking
            $mainBooking = $this->bookingModel->find($transaksi['booking_id']);
            $bookingDetails = $this->bookingModel
                ->select('booking.*, layanan.nama_layanan, layanan.harga, layanan.durasi_menit, layanan.jenis_kendaraan')
                ->join('layanan', 'layanan.kode_layanan = booking.layanan_id')
                ->where('booking.kode_booking', $mainBooking['kode_booking'])
                ->orderBy('booking.jam', 'ASC')
                ->findAll();

            // Group vehicles
            $vehicles = [];
            foreach ($bookingDetails as $detail) {
                $vehicleKey = $detail['no_plat'];
                if (!isset($vehicles[$vehicleKey])) {
                    $vehicles[$vehicleKey] = [
                        'no_plat' => $detail['no_plat'],
                        'merk_kendaraan' => $detail['merk_kendaraan'] ?? '',
                        'services' => []
                    ];
                }
                $vehicles[$vehicleKey]['services'][] = $detail;
            }

            // Prepare data for PDF
            $data = [
                'transaksi' => $transaksi,
                'vehicles' => $vehicles,
                'bookingDetails' => $bookingDetails,
                'title' => 'Struk Pembayaran - ' . $noTransaksi
            ];

            // Generate PDF
            $html = view('receipt/pdf_template', $data);

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('defaultFont', 'Arial');

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Output PDF
            $filename = 'struk_' . $noTransaksi . '.pdf';
            return $this->response->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
                ->setBody($dompdf->output());
        } catch (\Exception $e) {
            log_message('error', 'Error generating PDF receipt: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            // Return simple HTML error instead of redirect
            return $this->response->setStatusCode(500)->setBody(
                '<html><body><h1>Error</h1><p>Gagal membuat struk PDF: ' . $e->getMessage() . '</p></body></html>'
            );
        }
    }

    /**
     * Download PDF receipt
     */
    public function download($noTransaksi)
    {
        try {
            // Get transaction data
            $transaksi = $this->transaksiModel
                ->select('transaksi.*, booking.pelanggan_id, booking.no_plat, booking.merk_kendaraan, pelanggan.nama_pelanggan, pelanggan.no_hp')
                ->join('booking', 'booking.id = transaksi.booking_id', 'left')
                ->join('pelanggan', 'pelanggan.kode_pelanggan = booking.pelanggan_id', 'left')
                ->where('transaksi.no_transaksi', $noTransaksi)
                ->first();

            if (!$transaksi) {
                throw new \Exception('Transaksi tidak ditemukan');
            }

            // Get booking details - use kode_booking from main booking
            $mainBooking = $this->bookingModel->find($transaksi['booking_id']);
            $bookingDetails = $this->bookingModel
                ->select('booking.*, layanan.nama_layanan, layanan.harga, layanan.durasi_menit, layanan.jenis_kendaraan')
                ->join('layanan', 'layanan.kode_layanan = booking.layanan_id')
                ->where('booking.kode_booking', $mainBooking['kode_booking'])
                ->orderBy('booking.jam', 'ASC')
                ->findAll();

            // Group vehicles
            $vehicles = [];
            foreach ($bookingDetails as $detail) {
                $vehicleKey = $detail['no_plat'];
                if (!isset($vehicles[$vehicleKey])) {
                    $vehicles[$vehicleKey] = [
                        'no_plat' => $detail['no_plat'],
                        'merk_kendaraan' => $detail['merk_kendaraan'] ?? '',
                        'services' => []
                    ];
                }
                $vehicles[$vehicleKey]['services'][] = $detail;
            }

            // Prepare data for PDF
            $data = [
                'transaksi' => $transaksi,
                'vehicles' => $vehicles,
                'bookingDetails' => $bookingDetails,
                'title' => 'Struk Pembayaran - ' . $noTransaksi
            ];

            // Generate PDF
            $html = view('receipt/pdf_template', $data);

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('defaultFont', 'Arial');

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Download PDF
            $filename = 'struk_' . $noTransaksi . '.pdf';
            return $this->response->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setBody($dompdf->output());
        } catch (\Exception $e) {
            log_message('error', 'Error downloading PDF receipt: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            // Return simple HTML error instead of redirect
            return $this->response->setStatusCode(500)->setBody(
                '<html><body><h1>Error</h1><p>Gagal download struk PDF: ' . $e->getMessage() . '</p></body></html>'
            );
        }
    }
}
