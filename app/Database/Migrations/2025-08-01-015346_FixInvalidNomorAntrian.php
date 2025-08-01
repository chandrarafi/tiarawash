<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixInvalidNomorAntrian extends Migration
{
    public function up()
    {
        // Fix invalid nomor_antrian records by updating them to proper format
        $db = \Config\Database::connect();

        // Get invalid records
        $query = "SELECT id, nomor_antrian, tanggal 
                 FROM antrian 
                 WHERE LENGTH(nomor_antrian) < 12 
                 OR nomor_antrian NOT LIKE 'A%'";

        $invalidRecords = $db->query($query)->getResultArray();

        foreach ($invalidRecords as $record) {
            $tanggal = $record['tanggal'];
            $formattedDate = date('Ymd', strtotime($tanggal));
            $prefix = 'A' . $formattedDate;

            // Find the highest sequence for this date (excluding the current invalid record)
            $lastQuery = "SELECT nomor_antrian 
                         FROM antrian 
                         WHERE DATE(tanggal) = ? 
                         AND nomor_antrian LIKE ? 
                         AND LENGTH(nomor_antrian) = 12 
                         AND id != ?
                         ORDER BY nomor_antrian DESC 
                         LIMIT 1";

            $lastResult = $db->query($lastQuery, [
                date('Y-m-d', strtotime($tanggal)),
                $prefix . '%',
                $record['id']
            ])->getRowArray();

            $nextSequence = 1;
            if ($lastResult && !empty($lastResult['nomor_antrian'])) {
                $lastSequence = (int) substr($lastResult['nomor_antrian'], -3);
                $nextSequence = $lastSequence + 1;
            }

            if ($nextSequence > 999) $nextSequence = 1;

            $newNomor = $prefix . sprintf('%03d', $nextSequence);

            // Update the record
            $updateQuery = "UPDATE antrian SET nomor_antrian = ? WHERE id = ?";
            $db->query($updateQuery, [$newNomor, $record['id']]);

            echo "Updated record ID {$record['id']} from '{$record['nomor_antrian']}' to '{$newNomor}'\n";
        }

        echo "Fixed " . count($invalidRecords) . " invalid nomor_antrian records\n";
    }

    public function down()
    {
        // Cannot reverse this migration as we don't know the original invalid values
        echo "Cannot reverse this migration - invalid data was corrected\n";
    }
}
