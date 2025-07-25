<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLayananTable extends Migration
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
            'kode_layanan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'unique'     => true,
                'comment'    => 'Kode unik layanan'
            ],
            'nama_layanan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'jenis_kendaraan' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'comment'    => 'motor, mobil, truk, dll'
            ],
            'harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'durasi_estimasi' => [
                'type'       => 'INT',
                'constraint' => 11,
                'comment'    => 'Durasi dalam menit'
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'include_layanan' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Daftar yang termasuk dalam layanan'
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
        $this->forge->createTable('layanan');
    }

    public function down()
    {
        $this->forge->dropTable('layanan');
    }
}