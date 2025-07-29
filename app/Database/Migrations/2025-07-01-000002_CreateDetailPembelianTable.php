<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DetailPembelian2025_07_01_000002 extends Migration
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
            'no_faktur' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'perlengkapan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jumlah' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'harga_satuan' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
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
        $this->forge->addForeignKey('no_faktur', 'pembelian', 'no_faktur', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('perlengkapan_id', 'perlengkapan', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_pembelian');
    }

    public function down()
    {
        $this->forge->dropTable('detail_pembelian');
    }
}
