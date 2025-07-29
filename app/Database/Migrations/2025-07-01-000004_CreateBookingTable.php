<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBookingTable extends Migration
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
            'kode_booking' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'unique'     => true,
            ],
            'pelanggan_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'tanggal' => [
                'type'       => 'DATE',
            ],
            'jam' => [
                'type'       => 'TIME',
            ],
            'no_plat' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'jenis_kendaraan' => [
                'type'       => 'ENUM',
                'constraint' => ['motor', 'mobil', 'lainnya'],
                'default'    => 'mobil',
            ],
            'merk_kendaraan' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'layanan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['menunggu', 'diproses', 'selesai', 'batal'],
                'default'    => 'menunggu',
            ],
            'catatan' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        $this->forge->addForeignKey('pelanggan_id', 'pelanggan', 'kode_pelanggan', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('layanan_id', 'layanan', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('booking');
    }

    public function down()
    {
        $this->forge->dropTable('booking');
    }
}
