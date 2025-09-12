<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseBackupCommand extends Command
{
    protected $signature = 'db:backup {--tables=* : Specific tables to backup}';
    protected $description = 'Create a backup of the database before importing';

    public function handle()
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $backupFile = storage_path("app/backups/backup_before_import_{$timestamp}.sql");
        
        // Create backups directory if it doesn't exist
        $backupDir = dirname($backupFile);
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $tables = $this->option('tables');
        
        if (empty($tables)) {
            // Backup all tables
            $tables = $this->getAllTables();
        }

        $this->info('Creating database backup...');
        $this->createBackup($backupFile, $tables);
        $this->info("Backup created: {$backupFile}");

        return 0;
    }

    private function getAllTables()
    {
        $tables = DB::select('SHOW TABLES');
        $tableKey = 'Tables_in_' . config('database.connections.mysql.database');
        
        return collect($tables)->map(function($table) use ($tableKey) {
            return $table->$tableKey;
        })->toArray();
    }

    private function createBackup($backupFile, $tables)
    {
        $handle = fopen($backupFile, 'w');
        
        // Write header
        fwrite($handle, "-- Database Backup\n");
        fwrite($handle, "-- Created: " . now()->toDateTimeString() . "\n");
        fwrite($handle, "-- Laravel Application Database Backup\n\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

        foreach ($tables as $table) {
            $this->backupTable($handle, $table);
        }

        fwrite($handle, "\nSET FOREIGN_KEY_CHECKS = 1;\n");
        fclose($handle);
    }

    private function backupTable($handle, $table)
    {
        $this->line("Backing up table: {$table}");

        // Get table structure
        $createTable = DB::select("SHOW CREATE TABLE `{$table}`")[0];
        $createSql = $createTable->{'Create Table'};

        fwrite($handle, "-- Table: {$table}\n");
        fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");
        fwrite($handle, $createSql . ";\n\n");

        // Get table data
        $rows = DB::table($table)->get();
        
        if ($rows->count() > 0) {
            $columns = array_keys((array) $rows->first());
            $columnList = '`' . implode('`, `', $columns) . '`';
            
            fwrite($handle, "INSERT INTO `{$table}` ({$columnList}) VALUES\n");
            
            $values = [];
            foreach ($rows as $row) {
                $rowData = (array) $row;
                $escapedValues = array_map(function($value) {
                    if ($value === null) {
                        return 'NULL';
                    }
                    return "'" . addslashes($value) . "'";
                }, $rowData);
                
                $values[] = '(' . implode(', ', $escapedValues) . ')';
            }
            
            fwrite($handle, implode(",\n", $values) . ";\n\n");
        }
    }
}