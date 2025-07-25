<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRememberToken extends Migration
{
    public function up()
    {
        // Cek apakah kolom remember_token sudah ada
        if (!$this->db->fieldExists('remember_token', 'users')) {
            $this->forge->addColumn('users', [
                'remember_token' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'after' => 'last_login'
                ]
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('remember_token', 'users')) {
            $this->forge->dropColumn('users', 'remember_token');
        }
    }
}