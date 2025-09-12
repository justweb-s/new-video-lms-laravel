<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportOldDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:old-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import data from the old SQL dump file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting data import...');
        // Logic to read and process the SQL file will go here.
        $this->info('Data import completed successfully.');
    }
}
