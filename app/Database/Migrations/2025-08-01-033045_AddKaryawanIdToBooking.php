<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKaryawanIdToBooking extends Migration
{
    public function up()
    {
        // Add karyawan_id field to booking table
        $fields = [
            'karyawan_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'layanan_id'
            ],
        ];

        $this->forge->addColumn('booking', $fields);

        // Add foreign key constraint
        $this->forge->addForeignKey('karyawan_id', 'karyawan', 'idkaryawan', 'SET NULL', 'SET NULL', 'booking');
    }

    public function down()
    {
        // Drop foreign key first
        $this->forge->dropForeignKey('booking', 'booking_karyawan_id_foreign');

        // Drop the column
        $this->forge->dropColumn('booking', 'karyawan_id');
    }
}
