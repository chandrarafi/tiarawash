<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOtpCodesTable20250729000001 extends Migration
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
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'otp_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '6',
            ],
            'expires_at' => [
                'type' => 'DATETIME',
            ],
            'is_used' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'purpose' => [
                'type'       => 'ENUM',
                'constraint' => ['registration', 'password_reset'],
                'default'    => 'registration',
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
        $this->forge->addKey('email');
        $this->forge->addKey('expires_at');
        $this->forge->createTable('otp_codes');
    }

    public function down()
    {
        $this->forge->dropTable('otp_codes');
    }
}
