<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDetailPembelianTable extends Migration
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
            'pembelian_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Foreign key ke tabel pembelian'
            ],
            'perlengkapan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Foreign key ke tabel perlengkapan'
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'harga_satuan' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
            ],
            'keterangan' => [
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
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('pembelian_id', 'pembelian', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('perlengkapan_id', 'perlengkapan', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('detail_pembelian');
    }

    public function down()
    {
        $this->forge->dropTable('detail_pembelian');
    }
}