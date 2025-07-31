<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateBookingStatusEnum extends Migration
{
    public function up()
    {
        // Update the status enum to include the new values
        $this->db->query("ALTER TABLE booking MODIFY COLUMN status ENUM('menunggu_konfirmasi', 'dikonfirmasi', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'menunggu_konfirmasi'");
    }

    public function down()
    {
        // Revert to original enum values
        $this->db->query("ALTER TABLE booking MODIFY COLUMN status ENUM('menunggu', 'dikonfirmasi', 'selesai', 'dibatalkan') NOT NULL DEFAULT 'menunggu'");
    }
}
