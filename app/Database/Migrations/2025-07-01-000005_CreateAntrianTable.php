<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAntrianTable extends Migration
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
            'booking_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Foreign key ke tabel booking'
            ],
            'nomor_antrian' => [
                'type'       => 'INT',
                'constraint' => 11,
                'comment'    => 'Nomor urut antrian'
            ],
            'tanggal_antrian' => [
                'type' => 'DATE',
            ],
            'jam_mulai' => [
                'type' => 'TIME',
                'null' => true,
                'comment' => 'Jam mulai pencucian'
            ],
            'jam_selesai' => [
                'type' => 'TIME',
                'null' => true,
                'comment' => 'Jam selesai pencucian'
            ],
            'karyawan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Karyawan yang mengerjakan'
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'menunggu',
                'comment'    => 'menunggu, diproses, selesai, batal'
            ],
            'catatan_karyawan' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Catatan dari karyawan'
            ],
            'rating' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => true,
                'comment'    => 'Rating dari pelanggan (1-5)'
            ],
            'komentar' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Komentar dari pelanggan'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('booking_id', 'booking', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('karyawan_id', 'karyawan', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('antrian');
    }

    public function down()
    {
        $this->forge->dropTable('antrian');
    }
}