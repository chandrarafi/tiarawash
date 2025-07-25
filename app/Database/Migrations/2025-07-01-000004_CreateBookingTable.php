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
                'constraint' => 20,
                'unique'     => true,
                'comment'    => 'Kode unik booking'
            ],
            'pelanggan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Foreign key ke tabel pelanggan'
            ],
            'kendaraan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Foreign key ke tabel kendaraan'
            ],
            'layanan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Foreign key ke tabel layanan'
            ],
            'tanggal_booking' => [
                'type' => 'DATE',
            ],
            'jam_booking' => [
                'type' => 'TIME',
            ],
            'estimasi_selesai' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Catatan tambahan dari pelanggan'
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pending',
                'comment'    => 'pending, confirmed, cancelled, completed'
            ],
            'harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'comment'    => 'Harga layanan saat booking'
            ],
            'metode_booking' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'online',
                'comment'    => 'online, walk_in'
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
        $this->forge->addForeignKey('pelanggan_id', 'pelanggan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kendaraan_id', 'kendaraan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('layanan_id', 'layanan', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('booking');
    }

    public function down()
    {
        $this->forge->dropTable('booking');
    }
}