<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CleanupAntrian extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Database';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'cleanup:antrian';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Clean up invalid nomor_antrian records in the antrian table';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'cleanup:antrian';

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('Starting cleanup of invalid nomor_antrian records...', 'yellow');

        $antrianModel = new \App\Models\AntrianModel();

        // Clean up invalid records
        $result = $antrianModel->cleanupInvalidRecords();

        if ($result['success']) {
            CLI::write('✅ ' . $result['message'], 'green');
        } else {
            CLI::write('❌ Error: ' . $result['error'], 'red');
            return;
        }

        // Show current records for verification
        $db = \Config\Database::connect();
        $query = "SELECT nomor_antrian, tanggal, LENGTH(nomor_antrian) as length 
                  FROM antrian 
                  WHERE DATE(tanggal) = CURDATE() 
                  ORDER BY nomor_antrian";

        $records = $db->query($query)->getResultArray();

        CLI::newLine();
        CLI::write('Current antrian records for today:', 'yellow');
        CLI::write('Nomor Antrian    | Tanggal    | Length | Status');
        CLI::write('=====================================');

        foreach ($records as $record) {
            $status = $record['length'] == 12 ? '✅' : '❌';
            $line = sprintf(
                "%-15s | %-10s | %-6s | %s",
                $record['nomor_antrian'],
                $record['tanggal'],
                $record['length'],
                $status
            );
            CLI::write($line);
        }

        CLI::newLine();
        CLI::write('Cleanup completed!', 'green');
    }
}
