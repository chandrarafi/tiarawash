<?php
require_once 'vendor/autoload.php';

$config = new \Config\Database();
$db = \Config\Database::connect();

try {
    // Check if column already exists
    $columns = $db->query("DESCRIBE booking")->getResult();
    $hasIdKaryawan = false;
    
    foreach ($columns as $column) {
        if ($column->Field === 'id_karyawan') {
            $hasIdKaryawan = true;
            break;
        }
    }
    
    if (!$hasIdKaryawan) {
        echo "Adding id_karyawan column to booking table...\n";
        
        $sql = "ALTER TABLE booking ADD COLUMN id_karyawan VARCHAR(20) NULL AFTER layanan_id";
        $db->query($sql);
        
        echo "Column added successfully!\n";
    } else {
        echo "Column id_karyawan already exists!\n";
    }
    
    // Show current booking table structure
    echo "\nCurrent booking table structure:\n";
    $columns = $db->query("DESCRIBE booking")->getResult();
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type}) {$column->Null} {$column->Key}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>