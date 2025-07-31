<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBuktiPembayaranToTransaksi extends Migration
{
    public function up()
    {
        $this->forge->addColumn('transaksi', [
            'bukti_pembayaran' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'catatan'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('transaksi', 'bukti_pembayaran');
    }
}
