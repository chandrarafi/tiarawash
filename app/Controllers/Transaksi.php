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

    /**
     * Laporan Transaksi Cuci Pertanggal
     */
    public function laporanPertanggal()
    {
        // Check admin/pimpinan permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get filter parameters
        $tanggal = $this->request->getGet('tanggal') ?? date('d/m/Y');

        // Convert tanggal format if needed
        $tanggalFilter = date('Y-m-d', strtotime(str_replace('/', '-', $tanggal)));

        // Build query untuk laporan transaksi dengan join
        $builder = $this->db->table('transaksi t');
        $builder->select('
            t.no_transaksi as kode_transaksi,
            p.nama_pelanggan,
            "" as nama_karyawan,
            b.jenis_kendaraan,
            b.no_plat,
            "Cuci Mobil" as jenis_jasa,
            t.total_harga as harga
        ');
        $builder->join('pelanggan p', 't.pelanggan_id = p.kode_pelanggan', 'LEFT');

        $builder->join('booking b', 't.booking_id = b.id', 'LEFT');
        $builder->where('t.tanggal', $tanggalFilter);
        $builder->where('t.status_pembayaran', 'dibayar');
        $builder->orderBy('t.no_transaksi', 'ASC');

        $transaksi = $builder->get()->getResultArray();

        // Calculate total
        $total_harga = array_sum(array_column($transaksi, 'harga'));

        // Prepare data for view
        $data = [
            'title' => 'Laporan Transaksi Cuci Pertanggal',
            'active' => 'laporan-transaksi-pertanggal',
            'transaksi' => $transaksi,
            'tanggal' => $tanggal,
            'total_harga' => $total_harga
        ];

        return view('admin/transaksi/laporan_pertanggal', $data);
    }

    /**
     * Laporan Transaksi Cuci Perbulan
     */
    public function laporanPerbulan()
    {
        // Check admin/pimpinan permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get filter parameters
        $periode = $this->request->getGet('periode') ?? date('m/Y');
        list($bulan, $tahun) = explode('/', $periode);

        // Build query untuk laporan transaksi perbulan
        $builder = $this->db->table('transaksi t');
        $builder->select('
            DATE(t.tanggal) as tanggal,
            t.no_transaksi as kode_transaksi,
            p.nama_pelanggan,
            "" as nama_karyawan,
            b.jenis_kendaraan,
            b.no_plat,
            "Cuci Mobil" as jenis_jasa,
            t.total_harga as harga
        ');
        $builder->join('pelanggan p', 't.pelanggan_id = p.kode_pelanggan', 'LEFT');
        $builder->join('booking b', 't.booking_id = b.id', 'LEFT');
        $builder->where('MONTH(t.tanggal)', $bulan);
        $builder->where('YEAR(t.tanggal)', $tahun);
        $builder->where('t.status_pembayaran', 'dibayar');
        $builder->orderBy('t.tanggal', 'ASC');
        $builder->orderBy('t.no_transaksi', 'ASC');

        $transaksi = $builder->get()->getResultArray();

        // Calculate total
        $total_harga = array_sum(array_column($transaksi, 'harga'));

        // Prepare data for view
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

    /**
     * Laporan Transaksi Cuci Pertahun
     */
    public function laporanPertahun()
    {
        // Check admin/pimpinan permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get filter parameters
        $periode = $this->request->getGet('periode') ?? date('Y');

        // Build query untuk laporan transaksi pertahun - group by month
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

        // Prepare data for view
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
        // Check admin/pimpinan permission
        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        // Get filter parameters
        $tanggal = $this->request->getGet('tanggal') ?? date('d/m/Y');

        // Convert tanggal format if needed
        $tanggalFilter = date('Y-m-d', strtotime(str_replace('/', '-', $tanggal)));

        // Build query untuk laporan transaksi dengan join
        $builder = $this->db->table('transaksi t');
        $builder->select('
            t.no_transaksi as kode_transaksi,
            p.nama_pelanggan,
            "" as nama_karyawan,
            b.jenis_kendaraan,
            b.no_plat,
            "Cuci Mobil" as jenis_jasa,
            t.total_harga as harga
        ');
        $builder->join('pelanggan p', 't.pelanggan_id = p.kode_pelanggan', 'LEFT');

        $builder->join('booking b', 't.booking_id = b.id', 'LEFT');
        $builder->where('t.tanggal', $tanggalFilter);
        $builder->where('t.status_pembayaran', 'dibayar');
        $builder->orderBy('t.no_transaksi', 'ASC');

        $transaksi = $builder->get()->getResultArray();

        // Calculate total
        $total_harga = array_sum(array_column($transaksi, 'harga'));

        // Prepare data for PDF
        $data = [
            'transaksi' => $transaksi,
            'tanggal' => $tanggal,
            'total_harga' => $total_harga
        ];

        // Generate PDF
        require_once ROOTPATH . 'vendor/autoload.php';

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml(view('admin/transaksi/laporan_pertanggal_pdf', $data));
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Set filename
        $filename = 'Laporan_Transaksi_Cuci_Pertanggal_' . str_replace('/', '-', $tanggal) . '.pdf';

        // Output PDF
        $dompdf->stream($filename, array('Attachment' => false));
    }

    public function exportPerbulanPDF()
    {
        return redirect()->to('admin/transaksi/laporan-perbulan');
    }

    public function exportPertahunPDF()
    {
        return redirect()->to('admin/transaksi/laporan-pertahun');
    }
}
