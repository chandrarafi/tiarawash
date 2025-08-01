<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AddKaryawanFieldToBooking extends BaseCommand
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
    protected $name = 'add:karyawan-field';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Add id_karyawan field to booking table if it does not exist';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'add:karyawan-field';

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $db = \Config\Database::connect();

        try {
            // Check if column already exists
            $query = "SHOW COLUMNS FROM booking LIKE 'id_karyawan'";
            $result = $db->query($query);

            if ($result->getNumRows() == 0) {
                CLI::write('Adding id_karyawan column to booking table...', 'yellow');

                // Add the column
                $sql = "ALTER TABLE booking ADD COLUMN id_karyawan VARCHAR(20) NULL AFTER layanan_id";
                $db->query($sql);

                CLI::write('✅ Column id_karyawan added successfully!', 'green');
            } else {
                CLI::write('✅ Column id_karyawan already exists!', 'green');
            }

            // Show current booking table structure
            CLI::newLine();
            CLI::write('Current booking table structure:', 'yellow');
            $columns = $db->query("DESCRIBE booking")->getResultArray();

            foreach ($columns as $column) {
                $status = ($column['Field'] == 'id_karyawan') ? '✅' : '  ';
                CLI::write("{$status} {$column['Field']} ({$column['Type']}) {$column['Null']} {$column['Key']}");
            }
        } catch (\Exception $e) {
            CLI::write('❌ Error: ' . $e->getMessage(), 'red');
        }
    }
}
