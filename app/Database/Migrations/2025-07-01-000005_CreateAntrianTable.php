<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Antrian2025_07_01_000005 extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nomor_antrian' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'unique'     => true,
            ],
            'booking_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'tanggal' => [
                'type'       => 'DATE',
            ],
            'jam_mulai' => [
                'type'       => 'TIME',
                'null'       => true,
            ],
            'jam_selesai' => [
                'type'       => 'TIME',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu', 'diproses', 'selesai', 'batal'],
                'default'    => 'menunggu',
            ],
            'karyawan_id' => [
                'type'       => 'CHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('booking_id', 'booking', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('karyawan_id', 'karyawan', 'idkaryawan', 'CASCADE', 'SET NULL');
        $this->forge->createTable('antrian');
    }

    public function down()
    {
        $this->forge->dropTable('antrian');
    }
}
