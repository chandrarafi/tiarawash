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
            'kode_transaksi' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'unique'     => true,
                'comment'    => 'Kode unik transaksi'
            ],
            'antrian_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Foreign key ke tabel antrian'
            ],
            'pelanggan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Foreign key ke tabel pelanggan'
            ],
            'tanggal_transaksi' => [
                'type' => 'DATETIME',
            ],
            'total_layanan' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'diskon' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'pajak' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'total_bayar' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'metode_pembayaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'comment'    => 'cash, transfer, qris, dll'
            ],
            'status_pembayaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pending',
                'comment'    => 'pending, lunas, batal'
            ],
            'kasir_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Karyawan yang melayani kasir'
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addForeignKey('antrian_id', 'antrian', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('pelanggan_id', 'pelanggan', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('kasir_id', 'karyawan', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('transaksi');
    }

    public function down()
    {
        $this->forge->dropTable('transaksi');
    }
}