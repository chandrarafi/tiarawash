<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Kendaraan2025_07_01_000008 extends Migration
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
            'pelanggan_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
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
            'merk' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'model' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'warna' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
                'null'       => true,
            ],
            'tahun' => [
                'type'       => 'YEAR',
                'null'       => true,
            ],
            'catatan' => [
                'type'       => 'TEXT',
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
        $this->forge->addKey(['pelanggan_id', 'no_plat'], false, true); // Unique key
        $this->forge->addForeignKey('pelanggan_id', 'pelanggan', 'kode_pelanggan', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kendaraan');
    }

    public function down()
    {
        $this->forge->dropTable('kendaraan');
    }
}
