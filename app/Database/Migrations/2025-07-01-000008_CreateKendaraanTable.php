<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKendaraanTable extends Migration
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
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Foreign key ke tabel pelanggan'
            ],
            'nomor_polisi' => [
                'type'       => 'VARCHAR',
                'constraint' => 15,
                'unique'     => true,
            ],
            'jenis_kendaraan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'comment'    => 'motor, mobil, truk, dll'
            ],
            'merk' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'model' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'tahun' => [
                'type'       => 'YEAR',
                'null'       => true,
            ],
            'warna' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Catatan khusus kendaraan'
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'aktif',
                'comment'    => 'aktif, nonaktif'
            ],
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
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
        $this->forge->createTable('kendaraan');
    }

    public function down()
    {
        $this->forge->dropTable('kendaraan');
    }
}