<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TransaksiModel;
use App\Models\PembelianModel;

class Keuangan extends BaseController
{
    protected $transaksiModel;
    protected $pembelianModel;
    protected $db;

    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
        $this->pembelianModel = new PembelianModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Laporan Uang Masuk dan Keluar PerBulan
     */
    public function laporanPerbulan()
    {
        // Check admin/pimpinan permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get filter parameters
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Get uang masuk (dari transaksi)
        $builderMasuk = $this->transaksiModel->builder();
        $builderMasuk->selectSum('total_harga', 'total_masuk');
        $builderMasuk->where('MONTH(tanggal)', $bulan);
        $builderMasuk->where('YEAR(tanggal)', $tahun);
        $builderMasuk->where('status_pembayaran', 'dibayar');
        $uangMasuk = $builderMasuk->get()->getRowArray()['total_masuk'] ?? 0;

        // Get uang keluar (dari pembelian)
        $builderKeluar = $this->pembelianModel->builder();
        $builderKeluar->selectSum('total_harga', 'total_keluar');
        $builderKeluar->where('MONTH(tanggal)', $bulan);
        $builderKeluar->where('YEAR(tanggal)', $tahun);
        $uangKeluar = $builderKeluar->get()->getRowArray()['total_keluar'] ?? 0;

        // Get detail per tanggal untuk tabel
        $laporanDetail = [];

        // Get days in month
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $day);

            // Uang masuk per hari
            $masukHari = $this->transaksiModel->builder()
                ->selectSum('total_harga', 'total')
                ->where('tanggal', $tanggal)
                ->where('status_pembayaran', 'dibayar')
                ->get()->getRowArray()['total'] ?? 0;

            // Uang keluar per hari
            $keluarHari = $this->pembelianModel->builder()
                ->selectSum('total_harga', 'total')
                ->where('tanggal', $tanggal)
                ->get()->getRowArray()['total'] ?? 0;

            if ($masukHari > 0 || $keluarHari > 0) {
                $laporanDetail[] = [
                    'tanggal' => $tanggal,
                    'uang_masuk' => $masukHari,
                    'uang_keluar' => $keluarHari
                ];
            }
        }

        // Prepare data for view
        $data = [
            'title' => 'Laporan Uang Masuk dan Keluar PerBulan',
            'subtitle' => 'Laporan keuangan perbulan untuk admin dan pimpinan',
            'active' => 'laporan-keuangan-perbulan',
            'laporan_detail' => $laporanDetail,
            'total_masuk' => $uangMasuk,
            'total_keluar' => $uangKeluar,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'nama_bulan' => [
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember'
            ]
        ];

        return view('admin/keuangan/laporan_perbulan', $data);
    }

    /**
     * Laporan Uang Masuk dan Keluar PerTahun
     */
    public function laporanPertahun()
    {
        // Check admin/pimpinan permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get filter parameters
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Get laporan per bulan dalam tahun
        $laporanDetail = [];
        $totalMasukTahun = 0;
        $totalKeluarTahun = 0;

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            // Uang masuk per bulan
            $masukBulan = $this->transaksiModel->builder()
                ->selectSum('total_harga', 'total')
                ->where('MONTH(tanggal)', sprintf('%02d', $bulan))
                ->where('YEAR(tanggal)', $tahun)
                ->where('status_pembayaran', 'dibayar')
                ->get()->getRowArray()['total'] ?? 0;

            // Uang keluar per bulan
            $keluarBulan = $this->pembelianModel->builder()
                ->selectSum('total_harga', 'total')
                ->where('MONTH(tanggal)', sprintf('%02d', $bulan))
                ->where('YEAR(tanggal)', $tahun)
                ->get()->getRowArray()['total'] ?? 0;

            $laporanDetail[] = [
                'bulan' => sprintf('%02d', $bulan),
                'nama_bulan' => [
                    '01' => 'Januari',
                    '02' => 'Februari',
                    '03' => 'Maret',
                    '04' => 'April',
                    '05' => 'Mei',
                    '06' => 'Juni',
                    '07' => 'Juli',
                    '08' => 'Agustus',
                    '09' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember'
                ][sprintf('%02d', $bulan)],
                'uang_masuk' => $masukBulan,
                'uang_keluar' => $keluarBulan
            ];

            $totalMasukTahun += $masukBulan;
            $totalKeluarTahun += $keluarBulan;
        }

        // Prepare data for view
        $data = [
            'title' => 'Laporan Uang Masuk dan Keluar PerTahun',
            'subtitle' => 'Laporan keuangan pertahun untuk admin dan pimpinan',
            'active' => 'laporan-keuangan-pertahun',
            'laporan_detail' => $laporanDetail,
            'total_masuk' => $totalMasukTahun,
            'total_keluar' => $totalKeluarTahun,
            'tahun' => $tahun
        ];

        return view('admin/keuangan/laporan_pertahun', $data);
    }

    /**
     * Export Laporan PerBulan ke PDF
     */
    public function exportPerbulanPDF()
    {
        // Check admin/pimpinan permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get filter parameters
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Get uang masuk (dari transaksi)
        $builderMasuk = $this->transaksiModel->builder();
        $builderMasuk->selectSum('total_harga', 'total_masuk');
        $builderMasuk->where('MONTH(tanggal)', $bulan);
        $builderMasuk->where('YEAR(tanggal)', $tahun);
        $builderMasuk->where('status_pembayaran', 'dibayar');
        $uangMasuk = $builderMasuk->get()->getRowArray()['total_masuk'] ?? 0;

        // Get uang keluar (dari pembelian)
        $builderKeluar = $this->pembelianModel->builder();
        $builderKeluar->selectSum('total_harga', 'total_keluar');
        $builderKeluar->where('MONTH(tanggal)', $bulan);
        $builderKeluar->where('YEAR(tanggal)', $tahun);
        $uangKeluar = $builderKeluar->get()->getRowArray()['total_keluar'] ?? 0;

        // Get detail per tanggal untuk tabel
        $laporanDetail = [];

        // Get days in month
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $tanggal = sprintf('%04d-%02d-%02d', $tahun, $bulan, $day);

            // Uang masuk per hari
            $masukHari = $this->transaksiModel->builder()
                ->selectSum('total_harga', 'total')
                ->where('tanggal', $tanggal)
                ->where('status_pembayaran', 'dibayar')
                ->get()->getRowArray()['total'] ?? 0;

            // Uang keluar per hari
            $keluarHari = $this->pembelianModel->builder()
                ->selectSum('total_harga', 'total')
                ->where('tanggal', $tanggal)
                ->get()->getRowArray()['total'] ?? 0;

            if ($masukHari > 0 || $keluarHari > 0) {
                $laporanDetail[] = [
                    'tanggal' => $tanggal,
                    'uang_masuk' => $masukHari,
                    'uang_keluar' => $keluarHari
                ];
            }
        }

        // Prepare data for PDF
        $data = [
            'laporan_detail' => $laporanDetail,
            'total_masuk' => $uangMasuk,
            'total_keluar' => $uangKeluar,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'nama_bulan' => [
                '01' => 'Januari',
                '02' => 'Februari',
                '03' => 'Maret',
                '04' => 'April',
                '05' => 'Mei',
                '06' => 'Juni',
                '07' => 'Juli',
                '08' => 'Agustus',
                '09' => 'September',
                '10' => 'Oktober',
                '11' => 'November',
                '12' => 'Desember'
            ]
        ];

        // Generate PDF
        require_once ROOTPATH . 'vendor/autoload.php';
        require_once APPPATH . 'Helpers/PdfHelper.php';

        $html = view('admin/keuangan/laporan_perbulan_pdf', $data);
        $filename = 'Laporan_Keuangan_PerBulan_' . $data['nama_bulan'][$bulan] . '_' . $tahun;
        
        $pdfResult = \App\Helpers\PdfHelper::generatePdf($html, $filename, 'A4', 'portrait');
        
        return \App\Helpers\PdfHelper::streamPdf($pdfResult, false);
    }

    /**
     * Export Laporan PerTahun ke PDF
     */
    public function exportPertahunPDF()
    {
        // Check admin/pimpinan permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get filter parameters
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Get laporan per bulan dalam tahun
        $laporanDetail = [];
        $totalMasukTahun = 0;
        $totalKeluarTahun = 0;

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            // Uang masuk per bulan
            $masukBulan = $this->transaksiModel->builder()
                ->selectSum('total_harga', 'total')
                ->where('MONTH(tanggal)', sprintf('%02d', $bulan))
                ->where('YEAR(tanggal)', $tahun)
                ->where('status_pembayaran', 'dibayar')
                ->get()->getRowArray()['total'] ?? 0;

            // Uang keluar per bulan
            $keluarBulan = $this->pembelianModel->builder()
                ->selectSum('total_harga', 'total')
                ->where('MONTH(tanggal)', sprintf('%02d', $bulan))
                ->where('YEAR(tanggal)', $tahun)
                ->get()->getRowArray()['total'] ?? 0;

            $laporanDetail[] = [
                'bulan' => sprintf('%02d', $bulan),
                'nama_bulan' => [
                    '01' => 'Januari',
                    '02' => 'Februari',
                    '03' => 'Maret',
                    '04' => 'April',
                    '05' => 'Mei',
                    '06' => 'Juni',
                    '07' => 'Juli',
                    '08' => 'Agustus',
                    '09' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember'
                ][sprintf('%02d', $bulan)],
                'uang_masuk' => $masukBulan,
                'uang_keluar' => $keluarBulan
            ];

            $totalMasukTahun += $masukBulan;
            $totalKeluarTahun += $keluarBulan;
        }

        // Prepare data for PDF
        $data = [
            'laporan_detail' => $laporanDetail,
            'total_masuk' => $totalMasukTahun,
            'total_keluar' => $totalKeluarTahun,
            'tahun' => $tahun
        ];

        // Generate PDF
        require_once ROOTPATH . 'vendor/autoload.php';
        require_once APPPATH . 'Helpers/PdfHelper.php';

        $html = view('admin/keuangan/laporan_pertahun_pdf', $data);
        $filename = 'Laporan_Keuangan_PerTahun_' . $tahun;
        
        $pdfResult = \App\Helpers\PdfHelper::generatePdf($html, $filename, 'A4', 'portrait');
        
        return \App\Helpers\PdfHelper::streamPdf($pdfResult, false);
    }
}
