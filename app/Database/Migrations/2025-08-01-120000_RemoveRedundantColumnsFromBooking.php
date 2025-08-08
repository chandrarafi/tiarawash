<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveRedundantColumnsFromBooking extends Migration
{
    public function up()
    {
        // Remove redundant jenis_kendaraan column from booking table
        // This data should be retrieved from the layanan table via JOIN
        $this->forge->dropColumn('booking', 'jenis_kendaraan');

        // Remove redundant jenis_kendaraan column from transaksi table
        // This data should be retrieved from the layanan table via JOIN
        $this->forge->dropColumn('transaksi', 'jenis_kendaraan');
    }

    public function down()
    {
        // Add back jenis_kendaraan column to booking table if rollback is needed
        $bookingFields = [
            'jenis_kendaraan' => [
                'type'       => 'ENUM',
                'constraint' => ['motor', 'mobil', 'lainnya'],
                'default'    => 'mobil',
                'after'      => 'no_plat'
            ],
        ];

        $this->forge->addColumn('booking', $bookingFields);

        // Add back jenis_kendaraan column to transaksi table if rollback is needed
        $transaksiFields = [
            'jenis_kendaraan' => [
                'type'       => 'ENUM',
                'constraint' => ['motor', 'mobil', 'lainnya'],
                'default'    => 'mobil',
                'after'      => 'no_plat'
            ],
        ];

        $this->forge->addColumn('transaksi', $transaksiFields);
    }
}
