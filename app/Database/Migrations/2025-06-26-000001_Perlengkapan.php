<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Perlengkapan extends Migration
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
            'kode_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'unique'     => true,
                'comment'    => 'Kode unik barang'
            ],
            'nama_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'kategori' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'sabun, shampo, wax, alat, dll'
            ],
            'merk' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'comment'    => 'liter, kg, pcs, dll'
            ],
            'harga_beli' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'harga_jual' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'stok' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'stok_minimum' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'comment'    => 'Batas minimum stok untuk notifikasi'
            ],
            'lokasi_penyimpanan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->createTable('perlengkapan');
    }

    public function down()
    {
        $this->forge->dropTable('perlengkapan');
    }
}