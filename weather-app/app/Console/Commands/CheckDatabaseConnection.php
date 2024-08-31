<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;

class CheckDatabaseConnection extends OutputCommand
{
    protected $signature = 'db:check:connection';
    protected $description = 'Check the database connection';

    public function handle(): int
    {
        try {
            DB::connection()->getPDO();
            $this->output('Connected successfully.', self::MSG_SUCCESS);
        }
        catch (\Exception $e){
            $this->output('Cannot connect to database.', self::MSG_ERROR);
            $this->output($e->getMessage());
        }
        return 0;
    }
}
