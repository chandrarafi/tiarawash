<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixTransaksiLayananId20250730000001 extends Migration
{
    public function up()
    {
        // Change layanan_id from INT to VARCHAR to match layanan.kode_layanan
        $this->forge->modifyColumn('transaksi', [
            'layanan_id' => [
                'name' => 'layanan_id',
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false
            ]
        ]);

        // Drop old foreign key if exists
        $this->forge->dropForeignKey('transaksi', 'transaksi_layanan_id_foreign');

        // Add new foreign key
        $this->forge->addForeignKey('layanan_id', 'layanan', 'kode_layanan', 'CASCADE', 'RESTRICT');
    }

    public function down()
    {
        // Drop foreign key
        $this->forge->dropForeignKey('transaksi', 'transaksi_layanan_id_foreign');

        // Change back to INT
        $this->forge->modifyColumn('transaksi', [
            'layanan_id' => [
                'name' => 'layanan_id',
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false
            ]
        ]);
    }
}
