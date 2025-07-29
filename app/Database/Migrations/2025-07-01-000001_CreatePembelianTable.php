<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePembelianTable2025_07_01_000001 extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'no_faktur' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'unique'     => true,
            ],
            'tanggal' => [
                'type'       => 'DATE',
            ],
            'supplier' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'total_harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => 0,
            ],
            'keterangan' => [
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

        $this->forge->addKey('no_faktur', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('pembelian');
    }

    public function down()
    {
        $this->forge->dropTable('pembelian');
    }
}
