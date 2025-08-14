<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TransaksiModel;

class Transaksi extends BaseController
{
    protected $transaksiModel;
    protected $db;

    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
        $this->db = \Config\Database::connect();
    }

    public function laporanPertanggal()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $tanggal = $this->request->getGet('tanggal') ?? date('d/m/Y');


        $tanggalFilter = date('Y-m-d', strtotime(str_replace('/', '-', $tanggal)));


        $builder = $this->db->table('transaksi t');
        $builder->select('
            t.no_transaksi as kode_transaksi,
            p.nama_pelanggan,
            COALESCE(k.namakaryawan, "-") as nama_karyawan,
            l.jenis_kendaraan,
            b.no_plat,
            "Cuci Mobil" as jenis_jasa,
            t.total_harga as harga
        ');
        $builder->join('booking b', 't.booking_id = b.id', 'LEFT');
        $builder->join('pelanggan p', 'b.pelanggan_id = p.kode_pelanggan', 'LEFT');
        $builder->join('karyawan k', 'k.idkaryawan = b.id_karyawan', 'LEFT');
        $builder->join('layanan l', 'l.kode_layanan = b.layanan_id', 'LEFT');
        $builder->where('t.tanggal', $tanggalFilter);
        $builder->where('t.status_pembayaran', 'dibayar');
        $builder->orderBy('t.no_transaksi', 'ASC');

        $transaksi = $builder->get()->getResultArray();


        $total_harga = array_sum(array_column($transaksi, 'harga'));


        $data = [
            'title' => 'Laporan Transaksi Cuci Pertanggal',
            'active' => 'laporan-transaksi-pertanggal',
            'transaksi' => $transaksi,
            'tanggal' => $tanggal,
            'total_harga' => $total_harga
        ];

        return view('admin/transaksi/laporan_pertanggal', $data);
    }

    public function laporanPerbulan()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $periode = $this->request->getGet('periode') ?? date('m/Y');
        list($bulan, $tahun) = explode('/', $periode);


        $builder = $this->db->table('transaksi t');
        $builder->select('
            DATE(t.tanggal) as tanggal,
            t.no_transaksi as kode_transaksi,
            p.nama_pelanggan,
            COALESCE(k.namakaryawan, "-") as nama_karyawan,
            l.jenis_kendaraan,
            b.no_plat,
            "Cuci Mobil" as jenis_jasa,
            t.total_harga as harga
        ');
        $builder->join('booking b', 't.booking_id = b.id', 'LEFT');
        $builder->join('pelanggan p', 'b.pelanggan_id = p.kode_pelanggan', 'LEFT');
        $builder->join('karyawan k', 'k.idkaryawan = b.id_karyawan', 'LEFT');
        $builder->join('layanan l', 'l.kode_layanan = b.layanan_id', 'LEFT');
        $builder->where('MONTH(t.tanggal)', $bulan);
        $builder->where('YEAR(t.tanggal)', $tahun);
        $builder->where('t.status_pembayaran', 'dibayar');
        $builder->orderBy('t.tanggal', 'ASC');
        $builder->orderBy('t.no_transaksi', 'ASC');

        $transaksi = $builder->get()->getResultArray();


        $total_harga = array_sum(array_column($transaksi, 'harga'));


        $data = [
            'title' => 'Laporan Transaksi Cuci Perbulan',
            'active' => 'laporan-transaksi-perbulan',
            'transaksi' => $transaksi,
            'periode' => $periode,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'total_harga' => $total_harga,
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

        return view('admin/transaksi/laporan_perbulan', $data);
    }

    public function laporanPertahun()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $periode = $this->request->getGet('periode') ?? date('Y');


        $laporanDetail = [];
        $totalTransaksiTahun = 0;
        $totalHargaTahun = 0;

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $builder = $this->db->table('transaksi t');
            $builder->selectSum('t.total_harga', 'total_harga');
            $builder->selectCount('t.id', 'jumlah_transaksi');
            $builder->where('MONTH(t.tanggal)', sprintf('%02d', $bulan));
            $builder->where('YEAR(t.tanggal)', $periode);
            $builder->where('t.status_pembayaran', 'dibayar');

            $result = $builder->get()->getRowArray();

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
                'jumlah_transaksi' => $result['jumlah_transaksi'] ?? 0,
                'total' => $result['total_harga'] ?? 0
            ];

            $totalTransaksiTahun += ($result['jumlah_transaksi'] ?? 0);
            $totalHargaTahun += ($result['total_harga'] ?? 0);
        }


        $data = [
            'title' => 'Laporan Transaksi Cuci Pertahun',
            'active' => 'laporan-transaksi-pertahun',
            'laporan_detail' => $laporanDetail,
            'periode' => $periode,
            'total_transaksi' => $totalTransaksiTahun,
            'total_harga' => $totalHargaTahun
        ];

        return view('admin/transaksi/laporan_pertahun', $data);
    }

    public function exportPertanggalPDF()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $tanggal = $this->request->getGet('tanggal') ?? date('d/m/Y');


        $tanggalFilter = date('Y-m-d', strtotime(str_replace('/', '-', $tanggal)));


        $builder = $this->db->table('transaksi t');
        $builder->select('
            t.no_transaksi as kode_transaksi,
            p.nama_pelanggan,
            COALESCE(k.namakaryawan, "-") as nama_karyawan,
            l.jenis_kendaraan,
            b.no_plat,
            "Cuci Mobil" as jenis_jasa,
            t.total_harga as harga
        ');
        $builder->join('booking b', 't.booking_id = b.id', 'LEFT');
        $builder->join('pelanggan p', 'b.pelanggan_id = p.kode_pelanggan', 'LEFT');
        $builder->join('karyawan k', 'k.idkaryawan = b.id_karyawan', 'LEFT');
        $builder->join('layanan l', 'l.kode_layanan = b.layanan_id', 'LEFT');
        $builder->where('t.tanggal', $tanggalFilter);
        $builder->where('t.status_pembayaran', 'dibayar');
        $builder->orderBy('t.no_transaksi', 'ASC');

        $transaksi = $builder->get()->getResultArray();


        $total_harga = array_sum(array_column($transaksi, 'harga'));


        $data = [
            'transaksi' => $transaksi,
            'tanggal' => $tanggal,
            'total_harga' => $total_harga
        ];


        require_once ROOTPATH . 'vendor/autoload.php';
        require_once APPPATH . 'Helpers/PdfHelper.php';

        $html = view('admin/transaksi/laporan_pertanggal_pdf', $data);
        $filename = 'Laporan_Transaksi_Cuci_Pertanggal_' . str_replace('/', '-', $tanggal);

        $pdfResult = \App\Helpers\PdfHelper::generatePdf($html, $filename, 'A4', 'landscape');

        return \App\Helpers\PdfHelper::streamPdf($pdfResult, false);
    }

    public function exportPerbulanPDF()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $periode = $this->request->getGet('periode') ?? date('m/Y');
        list($bulan, $tahun) = explode('/', $periode);


        $builder = $this->db->table('transaksi t');
        $builder->select('
            DATE(t.tanggal) as tanggal,
            t.no_transaksi as kode_transaksi,
            p.nama_pelanggan,
            COALESCE(k.namakaryawan, "-") as nama_karyawan,
            l.jenis_kendaraan,
            b.no_plat,
            "Cuci Mobil" as jenis_jasa,
            t.total_harga as harga
        ');
        $builder->join('booking b', 't.booking_id = b.id', 'LEFT');
        $builder->join('pelanggan p', 'b.pelanggan_id = p.kode_pelanggan', 'LEFT');
        $builder->join('karyawan k', 'k.idkaryawan = b.id_karyawan', 'LEFT');
        $builder->join('layanan l', 'l.kode_layanan = b.layanan_id', 'LEFT');
        $builder->where('MONTH(t.tanggal)', $bulan);
        $builder->where('YEAR(t.tanggal)', $tahun);
        $builder->where('t.status_pembayaran', 'dibayar');
        $builder->orderBy('t.tanggal', 'ASC');
        $builder->orderBy('t.no_transaksi', 'ASC');

        $transaksi = $builder->get()->getResultArray();


        $total_harga = array_sum(array_column($transaksi, 'harga'));


        $data = [
            'transaksi' => $transaksi,
            'periode' => $periode,
            'total_harga' => $total_harga
        ];


        require_once ROOTPATH . 'vendor/autoload.php';
        require_once APPPATH . 'Helpers/PdfHelper.php';

        $html = view('admin/transaksi/laporan_perbulan_pdf', $data);
        $filename = 'Laporan_Transaksi_Cuci_Perbulan_' . str_replace('/', '-', $periode);

        $pdfResult = \App\Helpers\PdfHelper::generatePdf($html, $filename, 'A4', 'landscape');

        return \App\Helpers\PdfHelper::streamPdf($pdfResult, false);
    }

    public function exportPertahunPDF()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $periode = $this->request->getGet('periode') ?? date('Y');


        $laporanDetail = [];
        $totalTransaksiTahun = 0;
        $totalHargaTahun = 0;

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $builder = $this->db->table('transaksi t');
            $builder->selectSum('t.total_harga', 'total_harga');
            $builder->selectCount('t.id', 'jumlah_transaksi');
            $builder->where('MONTH(t.tanggal)', sprintf('%02d', $bulan));
            $builder->where('YEAR(t.tanggal)', $periode);
            $builder->where('t.status_pembayaran', 'dibayar');

            $result = $builder->get()->getRowArray();

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
                'jumlah_transaksi' => $result['jumlah_transaksi'] ?? 0,
                'total' => $result['total_harga'] ?? 0
            ];

            $totalTransaksiTahun += ($result['jumlah_transaksi'] ?? 0);
            $totalHargaTahun += ($result['total_harga'] ?? 0);
        }


        $data = [
            'laporan_detail' => $laporanDetail,
            'periode' => $periode,
            'total_transaksi' => $totalTransaksiTahun,
            'total_harga' => $totalHargaTahun
        ];


        require_once ROOTPATH . 'vendor/autoload.php';
        require_once APPPATH . 'Helpers/PdfHelper.php';

        $html = view('admin/transaksi/laporan_pertahun_pdf', $data);
        $filename = 'Laporan_Transaksi_Cuci_Pertahun_' . $periode;

        $pdfResult = \App\Helpers\PdfHelper::generatePdf($html, $filename, 'A4', 'portrait');

        return \App\Helpers\PdfHelper::streamPdf($pdfResult, false);
    }
}
