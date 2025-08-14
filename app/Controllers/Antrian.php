<?php

namespace App\Controllers;

use App\Models\AntrianModel;
use App\Models\BookingModel;
use App\Models\KaryawanModel;
use App\Models\TransaksiModel;
use App\Models\LayananModel;
use App\Models\PelangganModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Antrian extends BaseController
{
    protected $antrianModel;
    protected $bookingModel;
    protected $karyawanModel;
    protected $transaksiModel;
    protected $layananModel;
    protected $pelangganModel;
    protected $validation;

    public function __construct()
    {
        $this->antrianModel = new AntrianModel();
        $this->bookingModel = new BookingModel();
        $this->karyawanModel = new KaryawanModel();
        $this->transaksiModel = new TransaksiModel();
        $this->layananModel = new LayananModel();
        $this->pelangganModel = new PelangganModel();
        $this->validation = \Config\Services::validation();
    }

    public function index()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');

        $data = [
            'title' => 'Dashboard Antrian',
            'subtitle' => 'Monitoring antrian real-time',
            'tanggal' => $tanggal,
            'antrian' => $this->antrianModel->getAntrianByDate($tanggal),
            'stats' => $this->antrianModel->getAntrianStats($tanggal),
            'karyawan' => $this->karyawanModel->findAll(),
            'workload' => $this->antrianModel->getKaryawanWorkload($tanggal),
            'nextQueue' => $this->antrianModel->getNextQueue($tanggal)
        ];

        return view('admin/antrian/index', $data);
    }

    public function create()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        $data = [
            'title' => 'Tambah Antrian Baru',
            'subtitle' => 'Antrian manual untuk customer walk-in',
            'booking' => $this->bookingModel
                ->select('booking.*, pelanggan.nama_pelanggan, layanan.nama_layanan')
                ->join('pelanggan', 'pelanggan.kode_pelanggan = booking.pelanggan_id', 'left')
                ->join('layanan', 'layanan.kode_layanan = booking.layanan_id', 'left')
                ->where('booking.status', 'dikonfirmasi')
                ->where('booking.tanggal', date('Y-m-d'))
                ->whereNotIn('booking.id', function ($builder) {
                    return $builder->select('booking_id')->from('antrian')->where('booking_id IS NOT NULL');
                })
                ->findAll(),
            'layanan' => $this->layananModel->findAll(),
            'validation' => $this->validation
        ];

        return view('admin/antrian/create', $data);
    }

    public function store()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ]);
        }

        $rules = [
            'booking_id' => 'permit_empty|integer',
            'tanggal' => 'required|valid_date',
            'layanan_id' => 'required_without[booking_id]',
            'customer_name' => 'required_without[booking_id]',
            'no_plat' => 'required_without[booking_id]',
            'jenis_kendaraan' => 'required_without[booking_id]'
        ];

        if (!$this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $bookingId = $this->request->getPost('booking_id');
            $tanggal = $this->request->getPost('tanggal');

            if ($bookingId) {

                $existingAntrian = $this->antrianModel->where('booking_id', $bookingId)->first();
                if ($existingAntrian) {
                    throw new \Exception('Booking ini sudah memiliki antrian');
                }


                $queueData = [
                    'booking_id' => $bookingId,
                    'tanggal' => $tanggal,
                    'status' => 'menunggu'
                ];
            } else {

                $pelangganData = [
                    'nama_pelanggan' => $this->request->getPost('customer_name'),
                    'no_hp' => $this->request->getPost('customer_phone'),
                    'alamat' => $this->request->getPost('customer_address')
                ];


                $this->pelangganModel->insert($pelangganData);
                $pelangganId = $this->pelangganModel->getInsertID();


                $bookingData = [
                    'kode_booking' => $this->bookingModel->generateNewKodeBooking(),
                    'pelanggan_id' => $pelangganId,
                    'layanan_id' => $this->request->getPost('layanan_id'),
                    'tanggal' => $tanggal,
                    'jam' => date('H:i'),
                    'no_plat' => $this->request->getPost('no_plat'),
                    'jenis_kendaraan' => $this->request->getPost('jenis_kendaraan'),
                    'merk_kendaraan' => $this->request->getPost('merk_kendaraan'),
                    'status' => 'dikonfirmasi'
                ];

                $this->bookingModel->insert($bookingData);
                $bookingId = $this->bookingModel->getInsertID();


                $queueData = [
                    'booking_id' => $bookingId,
                    'tanggal' => $tanggal,
                    'status' => 'menunggu'
                ];
            }


            $this->antrianModel->insert($queueData);
            $antrianId = $this->antrianModel->getInsertID();

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            $message = 'Antrian berhasil ditambahkan';

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => $message,
                    'antrian_id' => $antrianId
                ]);
            }

            session()->setFlashdata('success', $message);
            return redirect()->to('/admin/antrian');
        } catch (\Exception $e) {
            $db->transRollback();

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show($id = null)
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        $antrian = $this->antrianModel->getAntrianWithDetails($id);

        if (!$antrian) {
            throw new PageNotFoundException('Antrian dengan ID ' . $id . ' tidak ditemukan');
        }


        $relatedBookings = [];
        if ($antrian['kode_booking']) {
            $relatedBookings = $this->bookingModel->getBookingsByKodeBooking($antrian['kode_booking']);
        }

        $data = [
            'title' => 'Detail Antrian',
            'subtitle' => 'Antrian #' . $antrian['nomor_antrian'],
            'antrian' => $antrian,
            'relatedBookings' => $relatedBookings,
            'karyawan' => $this->karyawanModel->findAll(),
            'position' => $this->antrianModel->getQueuePosition($id),
            'estimatedWait' => $this->antrianModel->getEstimatedWaitTime($id)
        ];

        return view('admin/antrian/show', $data);
    }

    public function updateStatus($id = null)
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ]);
        }

        $antrian = $this->antrianModel->find($id);

        if (!$antrian) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Antrian tidak ditemukan'
            ]);
        }

        $status = $this->request->getPost('status');
        $karyawanId = $this->request->getPost('karyawan_id');
        $notes = $this->request->getPost('notes');

        $db = \Config\Database::connect();
        $db->transStart();

        try {

            $this->antrianModel->updateStatus($id, $status, $karyawanId, $notes);


            if ($antrian['booking_id']) {
                $newBookingStatus = $this->mapQueueStatusToBookingStatus($status);
                $this->bookingModel->update($antrian['booking_id'], ['status' => $newBookingStatus]);
            }


            if ($status == 'selesai' && $antrian['booking_id']) {
                $this->handleQueueCompletion($antrian['booking_id']);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Status antrian berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function assignKaryawan($id = null)
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ]);
        }

        $antrian = $this->antrianModel->find($id);

        if (!$antrian) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Antrian tidak ditemukan'
            ]);
        }

        $karyawanId = $this->request->getPost('karyawan_id');

        if (!$karyawanId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Pilih karyawan terlebih dahulu'
            ]);
        }

        try {

            $this->antrianModel->updateStatus($id, 'diproses', $karyawanId);


            if ($antrian['booking_id']) {
                $this->bookingModel->update($antrian['booking_id'], ['status' => 'diproses']);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Karyawan berhasil ditugaskan'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function autoAssign($id = null)
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ]);
        }

        try {
            $result = $this->antrianModel->autoAssignKaryawan($id);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Karyawan berhasil ditugaskan secara otomatis'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menugaskan karyawan'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getRealtimeData()
    {
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');

        return $this->response->setJSON([
            'antrian' => $this->antrianModel->getAntrianByDate($tanggal),
            'stats' => $this->antrianModel->getAntrianStats($tanggal),
            'workload' => $this->antrianModel->getKaryawanWorkload($tanggal),
            'nextQueue' => $this->antrianModel->getNextQueue($tanggal),
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }

    public function publicDisplay()
    {
        $tanggal = date('Y-m-d');

        $data = [
            'title' => 'Antrian TiaraWash',
            'tanggal' => $tanggal,
            'antrian' => $this->antrianModel->getAntrianByDate($tanggal),
            'stats' => $this->antrianModel->getAntrianStats($tanggal),
            'currentQueue' => $this->antrianModel->where(['tanggal' => $tanggal, 'status' => 'diproses'])->findAll()
        ];

        return view('public/antrian_display', $data);
    }

    public function karyawanDashboard()
    {
        $userRole = session()->get('role');
        if (!in_array($userRole, ['admin', 'pimpinan', 'karyawan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }

        $karyawanId = session()->get('karyawan_id') ?? $this->request->getGet('karyawan_id');
        $tanggal = date('Y-m-d');

        $data = [
            'title' => 'Dashboard Karyawan',
            'subtitle' => 'Antrian yang ditugaskan',
            'myQueue' => $this->antrianModel->getAntrianByKaryawan($karyawanId, null, $tanggal),
            'allKaryawan' => $this->karyawanModel->findAll(),
            'selectedKaryawan' => $karyawanId
        ];

        return view('admin/antrian/karyawan_dashboard', $data);
    }



    private function mapQueueStatusToBookingStatus($queueStatus)
    {
        $mapping = [
            'menunggu' => 'dikonfirmasi',
            'diproses' => 'diproses',
            'selesai' => 'selesai',
            'batal' => 'dibatalkan'
        ];

        return $mapping[$queueStatus] ?? 'dikonfirmasi';
    }

    private function handleQueueCompletion($bookingId)
    {

        $booking = $this->bookingModel->find($bookingId);

        if (!$booking) {
            return;
        }


        $allBookings = $this->bookingModel->where('kode_booking', $booking['kode_booking'])->findAll();

        foreach ($allBookings as $b) {
            $this->bookingModel->update($b['id'], ['status' => 'selesai']);
        }

        log_message('info', "Queue completed for booking group: {$booking['kode_booking']}. All bookings marked as completed.");
    }

    public function laporan()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $tanggal_cetak = $this->request->getGet('tanggal_cetak') ?? date('d/m/Y');


        $tanggal_filter = $tanggal_cetak;
        if ($tanggal_cetak && $tanggal_cetak !== date('d/m/Y')) {
            $date_parts = explode('/', $tanggal_cetak);
            if (count($date_parts) === 3) {
                $tanggal_filter = $date_parts[2] . '-' . sprintf('%02d', $date_parts[1]) . '-' . sprintf('%02d', $date_parts[0]);
            }
        } else {
            $tanggal_filter = date('Y-m-d');
        }


        $builder = $this->antrianModel->builder();
        $builder->select('antrian.*, b.kode_booking, b.jam, antrian.jam_mulai, antrian.jam_selesai, b.status as booking_status');
        $builder->join('booking b', 'antrian.booking_id = b.id', 'LEFT');


        $builder->where('antrian.tanggal', $tanggal_filter);

        $builder->orderBy('antrian.id', 'ASC');

        $antrian = $builder->get()->getResultArray();


        $data = [
            'title' => 'Laporan Antrian',
            'subtitle' => 'Laporan antrian untuk admin dan pimpinan',
            'active' => 'laporan-antrian',
            'antrian' => $antrian,
            'tanggal_cetak' => $tanggal_cetak,
            'total_antrian' => count($antrian)
        ];

        return view('admin/antrian/laporan', $data);
    }

    public function exportPDF()
    {

        if (!in_array(session()->get('role'), ['admin', 'pimpinan'])) {
            return redirect()->to('auth')->with('error', 'Akses ditolak');
        }


        $tanggal_cetak = $this->request->getGet('tanggal_cetak') ?? date('d/m/Y');


        $tanggal_filter = $tanggal_cetak;
        if ($tanggal_cetak && $tanggal_cetak !== date('d/m/Y')) {
            $date_parts = explode('/', $tanggal_cetak);
            if (count($date_parts) === 3) {
                $tanggal_filter = $date_parts[2] . '-' . sprintf('%02d', $date_parts[1]) . '-' . sprintf('%02d', $date_parts[0]);
            }
        } else {
            $tanggal_filter = date('Y-m-d');
        }


        $builder = $this->antrianModel->builder();
        $builder->select('antrian.*, b.kode_booking, b.tanggal, b.jam, antrian.jam_mulai, antrian.jam_selesai, b.status as booking_status');
        $builder->join('booking b', 'antrian.booking_id = b.id', 'LEFT');


        $builder->where('antrian.tanggal', $tanggal_filter);

        $builder->orderBy('antrian.id', 'ASC');

        $antrian = $builder->get()->getResultArray();


        $data = [
            'antrian' => $antrian,
            'tanggal_cetak' => $tanggal_cetak,
            'total_antrian' => count($antrian)
        ];


        require_once ROOTPATH . 'vendor/autoload.php';
        require_once APPPATH . 'Helpers/PdfHelper.php';

        $html = view('admin/antrian/laporan_pdf', $data);
        $filename = 'Laporan_Antrian_' . str_replace('/', '-', $tanggal_cetak);
        
        $pdfResult = \App\Helpers\PdfHelper::generatePdf($html, $filename, 'A4', 'landscape');
        
        return \App\Helpers\PdfHelper::streamPdf($pdfResult, false);
    }
}
