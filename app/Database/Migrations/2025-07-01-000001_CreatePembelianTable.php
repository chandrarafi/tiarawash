<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePembelianTable extends Migration
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
            'kode_pembelian' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'unique'     => true,
                'comment'    => 'Kode unik pembelian'
            ],
            'supplier' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'tanggal_pembelian' => [
                'type' => 'DATE',
            ],
            'total_item' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'total_harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'status_pembayaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'belum_lunas',
                'comment'    => 'lunas, belum_lunas, cicilan'
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'ID user yang membuat pembelian'
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
        $this->forge->addForeignKey('created_by', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('pembelian');
    }

    public function down()
    {
        $this->forge->dropTable('pembelian');
    }
}