<?php

/**
 * Cleanup script for invalid nomor_antrian records
 * Run this script once to fix existing bad data
 */

// Bootstrap CodeIgniter
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
require_once __DIR__ . '/vendor/autoload.php';

$app = \Config\Services::codeigniter();
$app->initialize();

// Get the AntrianModel
$antrianModel = new \App\Models\AntrianModel();

echo "Starting cleanup of invalid nomor_antrian records...\n";

// Clean up invalid records
$result = $antrianModel->cleanupInvalidRecords();

if ($result['success']) {
    echo "✅ " . $result['message'] . "\n";
} else {
    echo "❌ Error: " . $result['error'] . "\n";
}

// Show current records for verification
$db = \Config\Database::connect();
$query = "SELECT nomor_antrian, tanggal, LENGTH(nomor_antrian) as length 
          FROM antrian 
          WHERE DATE(tanggal) = CURDATE() 
          ORDER BY nomor_antrian";

$records = $db->query($query)->getResultArray();

echo "\nCurrent antrian records for today:\n";
echo "Nomor Antrian\t\tTanggal\t\tLength\n";
echo "============================================\n";

foreach ($records as $record) {
    $status = $record['length'] == 12 ? '✅' : '❌';
    echo "{$record['nomor_antrian']}\t\t{$record['tanggal']}\t{$record['length']} {$status}\n";
}

echo "\nCleanup completed!\n";
