<?php

namespace App\Models;

use CodeIgniter\Model;

class AntrianModel extends Model
{
    protected $table            = 'antrian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_antrian',
        'booking_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'status',
        'karyawan_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'tanggal' => 'required|valid_date',
    ];

    protected $validationMessages = [
        'tanggal' => [
            'required' => 'Tanggal antrian harus diisi',
            'valid_date' => 'Format tanggal tidak valid',
        ],
    ];

    // Callbacks
    protected $beforeInsert = ['generateNomorAntrian'];
    protected $beforeUpdate = [];
    protected $afterInsert  = [];
    protected $afterUpdate  = [];

    protected function generateNomorAntrian(array $data)
    {
        // Always generate if nomor_antrian is empty
        if (empty($data['data']['nomor_antrian'])) {
            $tanggal = isset($data['data']['tanggal']) ? $data['data']['tanggal'] : date('Y-m-d');
            $formattedDate = date('Ymd', strtotime($tanggal));

            // Format: A + YYYYMMDD + 3-digit sequence (001, 002, 003, etc.)
            $prefix = 'A' . $formattedDate;

            // Generate using a more direct approach with database locking
            $newNomorAntrian = $this->generateUniqueNomorAntrian($prefix, $tanggal);
            $data['data']['nomor_antrian'] = $newNomorAntrian;

            log_message('info', "Generated nomor_antrian: {$newNomorAntrian} for date: {$tanggal}");
        }

        return $data;
    }

    /**
     * Generate unique nomor_antrian with database locking for concurrency safety
     */
    private function generateUniqueNomorAntrian($prefix, $tanggal)
    {
        $db = \Config\Database::connect();

        // Use database transaction with locking to prevent race conditions
        $db->transStart();

        try {
            // Lock the table for reading to prevent concurrent inserts
            $query = "SELECT nomor_antrian FROM {$this->table} 
                     WHERE DATE(tanggal) = ? 
                     AND nomor_antrian LIKE ? 
                     AND LENGTH(nomor_antrian) = 12 
                     ORDER BY nomor_antrian DESC 
                     LIMIT 1 
                     FOR UPDATE";

            $result = $db->query($query, [
                date('Y-m-d', strtotime($tanggal)),
                $prefix . '%'
            ]);

            $lastRecord = $result->getRowArray();

            $nextSequence = 1; // Start from 001

            if ($lastRecord && !empty($lastRecord['nomor_antrian'])) {
                $nomorAntrian = $lastRecord['nomor_antrian'];
                // Extract the last 3 digits for sequence
                if (strlen($nomorAntrian) === 12 && substr($nomorAntrian, 0, 9) === $prefix) {
                    $lastSequence = (int) substr($nomorAntrian, -3);
                    $nextSequence = $lastSequence + 1;
                }
            }

            // Ensure sequence doesn't exceed 999
            if ($nextSequence > 999) {
                $nextSequence = 1;
            }

            // Generate the nomor_antrian
            $newNomorAntrian = $prefix . sprintf('%03d', $nextSequence);

            // Double-check uniqueness within the same transaction
            $checkQuery = "SELECT COUNT(*) as count FROM {$this->table} WHERE nomor_antrian = ?";
            $checkResult = $db->query($checkQuery, [$newNomorAntrian]);
            $count = $checkResult->getRowArray()['count'];

            if ($count > 0) {
                // If somehow still exists, try a few more numbers
                for ($i = 1; $i <= 10; $i++) {
                    $testSequence = $nextSequence + $i;
                    if ($testSequence > 999) $testSequence = 1;

                    $testNomor = $prefix . sprintf('%03d', $testSequence);
                    $testResult = $db->query($checkQuery, [$testNomor]);
                    $testCount = $testResult->getRowArray()['count'];

                    if ($testCount === 0) {
                        $newNomorAntrian = $testNomor;
                        break;
                    }
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed while generating nomor_antrian');
            }

            return $newNomorAntrian;
        } catch (\Exception $e) {
            $db->transRollback();

            // Emergency fallback - use timestamp-based unique number
            $timestamp = time();
            $uniqueSequence = ($timestamp % 999) + 1;
            $fallbackNomor = $prefix . sprintf('%03d', $uniqueSequence);

            log_message('error', 'Error in generateUniqueNomorAntrian: ' . $e->getMessage() . '. Using fallback: ' . $fallbackNomor);

            return $fallbackNomor;
        }
    }

    /**
     * Clean up invalid nomor_antrian records
     */
    public function cleanupInvalidRecords()
    {
        try {
            $db = \Config\Database::connect();

            // Find records with invalid nomor_antrian format
            $query = "SELECT id, nomor_antrian, tanggal 
                     FROM antrian 
                     WHERE LENGTH(nomor_antrian) < 12 
                     OR nomor_antrian NOT LIKE 'A%'";

            $invalidRecords = $db->query($query)->getResultArray();

            $cleanedCount = 0;
            foreach ($invalidRecords as $record) {
                log_message('warning', "Found invalid nomor_antrian: {$record['nomor_antrian']} (ID: {$record['id']})");

                // Generate a proper nomor_antrian for this record using direct database query
                $tanggal = $record['tanggal'];
                $formattedDate = date('Ymd', strtotime($tanggal));
                $prefix = 'A' . $formattedDate;

                // Find the highest sequence for this date (excluding the current invalid record)
                $lastQuery = "SELECT nomor_antrian 
                             FROM antrian 
                             WHERE DATE(tanggal) = ? 
                             AND nomor_antrian LIKE ? 
                             AND LENGTH(nomor_antrian) = 12 
                             AND id != ?
                             ORDER BY nomor_antrian DESC 
                             LIMIT 1";

                $lastResult = $db->query($lastQuery, [
                    date('Y-m-d', strtotime($tanggal)),
                    $prefix . '%',
                    $record['id']
                ])->getRowArray();

                $nextSequence = 1;
                if ($lastResult && !empty($lastResult['nomor_antrian'])) {
                    $lastSequence = (int) substr($lastResult['nomor_antrian'], -3);
                    $nextSequence = $lastSequence + 1;
                }

                if ($nextSequence > 999) $nextSequence = 1;

                $newNomor = $prefix . sprintf('%03d', $nextSequence);

                // Directly update using raw SQL to avoid model callbacks
                $updateQuery = "UPDATE antrian SET nomor_antrian = ? WHERE id = ?";
                $db->query($updateQuery, [$newNomor, $record['id']]);

                log_message('info', "Updated invalid record ID {$record['id']} from '{$record['nomor_antrian']}' to '{$newNomor}'");
                $cleanedCount++;
            }

            return [
                'success' => true,
                'cleaned_count' => $cleanedCount,
                'message' => "Cleaned up {$cleanedCount} invalid records"
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error cleaning up invalid records: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get detailed queue information with all related data
     */
    public function getAntrianWithDetails($id = null)
    {
        $builder = $this->db->table('antrian a');
        $builder->select('a.*, b.kode_booking, b.pelanggan_id, b.no_plat, l.jenis_kendaraan, 
                          b.merk_kendaraan, b.tanggal as booking_tanggal, b.jam as booking_jam,
                          p.nama_pelanggan, p.no_hp, l.nama_layanan, l.harga, l.durasi_menit, 
                          k.namakaryawan, k.nohp as karyawan_hp, b.id_karyawan as karyawan_id');
        $builder->join('booking b', 'b.id = a.booking_id', 'left');
        $builder->join('pelanggan p', 'p.kode_pelanggan = b.pelanggan_id', 'left');
        $builder->join('layanan l', 'l.kode_layanan = b.layanan_id', 'left');
        $builder->join('karyawan k', 'k.idkaryawan = b.id_karyawan', 'left');

        if ($id !== null) {
            $builder->where('a.id', $id);
            return $builder->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get queue by date with comprehensive details
     */
    public function getAntrianByDate($date)
    {
        $builder = $this->db->table('antrian a');
        $builder->select('a.id, a.nomor_antrian, a.status, a.jam_mulai, a.jam_selesai, b.id_karyawan as karyawan_id,
                          b.kode_booking, b.pelanggan_id, b.no_plat, l.jenis_kendaraan, 
                          b.merk_kendaraan, b.tanggal as booking_tanggal, b.jam as booking_jam,
                          p.nama_pelanggan, l.nama_layanan, l.harga, l.durasi_menit, k.namakaryawan');
        $builder->join('booking b', 'b.id = a.booking_id', 'left');
        $builder->join('pelanggan p', 'p.kode_pelanggan = b.pelanggan_id', 'left');
        $builder->join('layanan l', 'l.kode_layanan = b.layanan_id', 'left');
        $builder->join('karyawan k', 'k.idkaryawan = b.id_karyawan', 'left');
        $builder->where('a.tanggal', $date);

        // Order by priority: diproses first, then menunggu, then others
        $builder->orderBy("FIELD(a.status, 'diproses', 'menunggu', 'selesai', 'batal')", '', false);
        $builder->orderBy('a.created_at', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get queue statistics for dashboard
     */
    public function getAntrianStats($date = null)
    {
        if (!$date) $date = date('Y-m-d');

        $stats = [
            'total' => $this->where('tanggal', $date)->countAllResults(),
            'menunggu' => $this->where(['tanggal' => $date, 'status' => 'menunggu'])->countAllResults(),
            'diproses' => $this->where(['tanggal' => $date, 'status' => 'diproses'])->countAllResults(),
            'selesai' => $this->where(['tanggal' => $date, 'status' => 'selesai'])->countAllResults(),
            'batal' => $this->where(['tanggal' => $date, 'status' => 'batal'])->countAllResults(),
        ];

        $stats['pending'] = $stats['menunggu'] + $stats['diproses'];
        $stats['completed'] = $stats['selesai'];

        return $stats;
    }

    /**
     * Get queue by employee with workload details
     */
    public function getAntrianByKaryawan($karyawanId, $status = null, $date = null)
    {
        $builder = $this->db->table('antrian a');
        $builder->select('a.*, b.kode_booking, b.no_plat, l.jenis_kendaraan, 
                          p.nama_pelanggan, l.nama_layanan, l.durasi_menit, b.id_karyawan as karyawan_id');
        $builder->join('booking b', 'b.id = a.booking_id', 'left');
        $builder->join('pelanggan p', 'p.kode_pelanggan = b.pelanggan_id', 'left');
        $builder->join('layanan l', 'l.kode_layanan = b.layanan_id', 'left');
        $builder->where('b.id_karyawan', $karyawanId);

        if ($status !== null) {
            $builder->where('a.status', $status);
        }

        if ($date !== null) {
            $builder->where('a.tanggal', $date);
        }

        $builder->orderBy('a.created_at', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Get current queue position
     */
    public function getQueuePosition($antrianId)
    {
        $antrian = $this->find($antrianId);
        if (!$antrian) return 0;

        // Count antrian yang lebih dulu dan masih aktif
        $position = $this->where('tanggal', $antrian['tanggal'])
            ->where('status !=', 'selesai')
            ->where('status !=', 'batal')
            ->where('created_at <', $antrian['created_at'])
            ->countAllResults() + 1;

        return $position;
    }

    /**
     * Get estimated waiting time
     */
    public function getEstimatedWaitTime($antrianId)
    {
        $antrian = $this->find($antrianId);
        if (!$antrian) return 0;

        // Get average service time
        $avgServiceTime = 60; // Default 60 minutes

        // Calculate from completed services today
        $completed = $this->select('TIME_TO_SEC(TIMEDIFF(jam_selesai, jam_mulai)) / 60 as duration')
            ->where('tanggal', $antrian['tanggal'])
            ->where('status', 'selesai')
            ->where('jam_mulai IS NOT NULL')
            ->where('jam_selesai IS NOT NULL')
            ->findAll();

        if (!empty($completed)) {
            $totalDuration = array_sum(array_column($completed, 'duration'));
            $avgServiceTime = $totalDuration / count($completed);
        }

        // Count queue ahead
        $queueAhead = $this->getQueuePosition($antrianId) - 1;

        return (int)($queueAhead * $avgServiceTime);
    }

    /**
     * Update status with comprehensive logging
     */
    public function updateStatus($id, $status, $karyawanId = null, $notes = null)
    {
        $data = ['status' => $status];

        if ($status == 'diproses' && $karyawanId) {
            // Update id_karyawan in booking table instead of antrian
            $antrian = $this->find($id);
            if ($antrian && $antrian['booking_id']) {
                $bookingModel = new \App\Models\BookingModel();
                $bookingModel->update($antrian['booking_id'], ['id_karyawan' => $karyawanId]);
            }
            $data['jam_mulai'] = date('H:i:s');
        }

        if ($status == 'selesai') {
            $data['jam_selesai'] = date('H:i:s');
        }

        if ($status == 'batal') {
            $data['jam_selesai'] = date('H:i:s');
        }

        $result = $this->update($id, $data);

        // Log status change
        if ($result) {
            log_message('info', "Antrian ID {$id} status changed to {$status}" .
                ($karyawanId ? " by karyawan {$karyawanId}" : ""));
        }

        return $result;
    }

    /**
     * Create execution queue from confirmed booking
     * NOTE: Booking dan pembayaran sudah selesai, ini hanya untuk execution workflow
     */
    public function createFromBooking($kodeBooking)
    {
        $bookingModel = new \App\Models\BookingModel();
        $bookings = $bookingModel->where('kode_booking', $kodeBooking)->findAll();

        if (empty($bookings)) {
            return false;
        }

        $mainBooking = $bookings[0];

        // Check if queue already exists
        $existingQueue = $this->where('booking_id', $mainBooking['id'])->first();
        if ($existingQueue) {
            return $existingQueue['id'];
        }

        // Pastikan booking sudah dikonfirmasi dan dibayar
        if ($mainBooking['status'] !== 'dikonfirmasi') {
            return false;
        }

        // Create execution queue (bukan payment queue)
        $queueData = [
            'booking_id' => $mainBooking['id'],
            'tanggal' => $mainBooking['tanggal'],
            'status' => 'menunggu'  // menunggu eksekusi, bukan menunggu pembayaran
        ];

        if ($this->insert($queueData)) {
            return $this->getInsertID();
        }

        return false;
    }

    /**
     * Get next queue to process
     */
    public function getNextQueue($date = null)
    {
        if (!$date) $date = date('Y-m-d');

        $nextItem = $this->select('id')
            ->where('tanggal', $date)
            ->where('status', 'menunggu')
            ->orderBy('created_at', 'ASC')
            ->first();

        if (!$nextItem) {
            return null;
        }

        return $this->getAntrianWithDetails($nextItem['id']);
    }

    /**
     * Get karyawan workload
     */
    public function getKaryawanWorkload($date = null)
    {
        if (!$date) $date = date('Y-m-d');

        $builder = $this->db->table('antrian a');
        $builder->select('k.idkaryawan, k.namakaryawan, 
                          COUNT(*) as total_antrian,
                          SUM(CASE WHEN a.status = "diproses" THEN 1 ELSE 0 END) as sedang_diproses,
                          SUM(CASE WHEN a.status = "selesai" THEN 1 ELSE 0 END) as selesai');
        $builder->join('booking b', 'b.id = a.booking_id', 'left');
        $builder->join('karyawan k', 'k.idkaryawan = b.id_karyawan', 'right');
        $builder->where('a.tanggal', $date);
        $builder->orWhere('a.tanggal IS NULL');
        $builder->groupBy('k.idkaryawan, k.namakaryawan');
        $builder->orderBy('total_antrian', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Auto assign to least busy employee
     */
    public function autoAssignKaryawan($antrianId)
    {
        $antrian = $this->find($antrianId);
        if (!$antrian) return false;

        $workload = $this->getKaryawanWorkload($antrian['tanggal']);

        if (!empty($workload)) {
            $leastBusy = $workload[0]; // Already ordered by workload ASC
            return $this->updateStatus($antrianId, 'diproses', $leastBusy['idkaryawan']);
        }

        return false;
    }
}
