<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ComprehensiveDataNormalization extends Migration
{
    public function up()
    {
        // =====================================================
        // PHASE 1: REMOVE REDUNDANT COLUMNS FROM BOOKING
        // =====================================================

        // 1. Remove jenis_kendaraan (redundant with layanan.jenis_kendaraan)
        $this->forge->dropColumn('booking', 'jenis_kendaraan');

        // Note: no_plat and merk_kendaraan akan dipertahankan di booking untuk sementara
        // karena sistem belum menggunakan tabel kendaraan secara optimal

        // =====================================================
        // PHASE 2: REMOVE REDUNDANT COLUMNS FROM TRANSAKSI
        // =====================================================

        // 1. Remove jenis_kendaraan (redundant with layanan.jenis_kendaraan)
        $this->forge->dropColumn('transaksi', 'jenis_kendaraan');

        // 2. Remove no_plat (redundant with booking.no_plat)
        $this->forge->dropColumn('transaksi', 'no_plat');

        // 3. Remove pelanggan_id (redundant with booking.pelanggan_id)
        $this->forge->dropColumn('transaksi', 'pelanggan_id');

        // Note: layanan_id dipertahankan karena bisa berbeda dengan booking.layanan_id
        // dalam kasus multi-service booking

        // =====================================================
        // PHASE 3: ADD PROPER INDEXES FOR PERFORMANCE
        // =====================================================

        // Add indexes for common JOIN operations
        $this->forge->addKey(['booking_id'], false, false, 'transaksi');
        $this->forge->addKey(['layanan_id'], false, false, 'transaksi');
        $this->forge->addKey(['pelanggan_id'], false, false, 'booking');
        $this->forge->addKey(['layanan_id'], false, false, 'booking');
    }

    public function down()
    {
        // =====================================================
        // ROLLBACK: ADD BACK REMOVED COLUMNS
        // =====================================================

        // Add back to booking table
        $bookingFields = [
            'jenis_kendaraan' => [
                'type'       => 'ENUM',
                'constraint' => ['motor', 'mobil', 'lainnya'],
                'default'    => 'mobil',
                'after'      => 'no_plat'
            ],
        ];
        $this->forge->addColumn('booking', $bookingFields);

        // Add back to transaksi table
        $transaksiFields = [
            'jenis_kendaraan' => [
                'type'       => 'ENUM',
                'constraint' => ['motor', 'mobil', 'lainnya'],
                'default'    => 'mobil',
                'after'      => 'layanan_id'
            ],
            'no_plat' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'after'      => 'layanan_id'
            ],
            'pelanggan_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'booking_id'
            ],
        ];
        $this->forge->addColumn('transaksi', $transaksiFields);

        // Remove indexes (CodeIgniter will handle this automatically)
    }
}
