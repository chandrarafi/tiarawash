<?php

namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\BookingModel;
use App\Models\LayananModel;

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

    public function pdf($noTransaksi)
    {
        try {

            $transaksi = $this->transaksiModel
                ->select('transaksi.*, booking.pelanggan_id, booking.no_plat, booking.merk_kendaraan, pelanggan.nama_pelanggan, pelanggan.no_hp')
                ->join('booking', 'booking.id = transaksi.booking_id', 'left')
                ->join('pelanggan', 'pelanggan.kode_pelanggan = booking.pelanggan_id', 'left')
                ->where('transaksi.no_transaksi', $noTransaksi)
                ->first();

            if (!$transaksi) {
                throw new \Exception('Transaksi tidak ditemukan');
            }


            $mainBooking = $this->bookingModel->find($transaksi['booking_id']);
            $bookingDetails = $this->bookingModel
                ->select('booking.*, layanan.nama_layanan, layanan.harga, layanan.durasi_menit, layanan.jenis_kendaraan')
                ->join('layanan', 'layanan.kode_layanan = booking.layanan_id')
                ->where('booking.kode_booking', $mainBooking['kode_booking'])
                ->orderBy('booking.jam', 'ASC')
                ->findAll();


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


            $data = [
                'transaksi' => $transaksi,
                'vehicles' => $vehicles,
                'bookingDetails' => $bookingDetails,
                'title' => 'Struk Pembayaran - ' . $noTransaksi
            ];


            $html = view('receipt/pdf_template', $data);

            require_once APPPATH . 'Helpers/PdfHelper.php';

            $filename = 'struk_' . $noTransaksi;
            $pdfResult = \App\Helpers\PdfHelper::generatePdf($html, $filename, 'A4', 'portrait');

            return \App\Helpers\PdfHelper::streamPdf($pdfResult, false);
        } catch (\Exception $e) {
            log_message('error', 'Error generating PDF receipt: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());


            return $this->response->setStatusCode(500)->setBody(
                '<html><body><h1>Error</h1><p>Gagal membuat struk PDF: ' . $e->getMessage() . '</p></body></html>'
            );
        }
    }

    public function download($noTransaksi)
    {
        try {
            // Get the same data as PDF method
            $transaksi = $this->transaksiModel
                ->select('transaksi.*, booking.pelanggan_id, booking.no_plat, booking.merk_kendaraan, pelanggan.nama_pelanggan, pelanggan.no_hp')
                ->join('booking', 'booking.id = transaksi.booking_id', 'left')
                ->join('pelanggan', 'pelanggan.kode_pelanggan = booking.pelanggan_id', 'left')
                ->where('transaksi.no_transaksi', $noTransaksi)
                ->first();

            if (!$transaksi) {
                throw new \Exception('Transaksi tidak ditemukan');
            }


            $mainBooking = $this->bookingModel->find($transaksi['booking_id']);
            $bookingDetails = $this->bookingModel
                ->select('booking.*, layanan.nama_layanan, layanan.harga, layanan.durasi_menit, layanan.jenis_kendaraan')
                ->join('layanan', 'layanan.kode_layanan = booking.layanan_id')
                ->where('booking.kode_booking', $mainBooking['kode_booking'])
                ->orderBy('booking.jam', 'ASC')
                ->findAll();


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


            $data = [
                'transaksi' => $transaksi,
                'vehicles' => $vehicles,
                'bookingDetails' => $bookingDetails,
                'title' => 'Struk Pembayaran - ' . $noTransaksi
            ];


            $html = view('receipt/pdf_template', $data);

            require_once APPPATH . 'Helpers/PdfHelper.php';

            $filename = 'struk_' . $noTransaksi;
            $pdfResult = \App\Helpers\PdfHelper::generatePdf($html, $filename, 'A4', 'portrait');

            return \App\Helpers\PdfHelper::streamPdf($pdfResult, true); // true for download
        } catch (\Exception $e) {
            log_message('error', 'Error downloading PDF receipt: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());


            return $this->response->setStatusCode(500)->setBody(
                '<html><body><h1>Error</h1><p>Gagal download struk PDF: ' . $e->getMessage() . '</p></body></html>'
            );
        }
    }
}
