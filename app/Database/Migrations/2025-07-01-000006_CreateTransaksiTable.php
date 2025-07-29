<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransaksiTable extends Migration
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
            'no_transaksi' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'unique'     => true,
            ],
            'tanggal' => [
                'type'       => 'DATE',
            ],
            'booking_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'pelanggan_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'layanan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
            'total_harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'metode_pembayaran' => [
                'type'       => 'ENUM',
                'constraint' => ['tunai', 'kartu_kredit', 'kartu_debit', 'e-wallet', 'transfer'],
                'default'    => 'tunai',
            ],
            'status_pembayaran' => [
                'type'       => 'ENUM',
                'constraint' => ['belum_bayar', 'dibayar', 'batal'],
                'default'    => 'belum_bayar',
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
        $this->forge->addForeignKey('booking_id', 'booking', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('pelanggan_id', 'pelanggan', 'kode_pelanggan', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('layanan_id', 'layanan', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('transaksi');
    }

    public function down()
    {
        $this->forge->dropTable('transaksi');
    }
}
