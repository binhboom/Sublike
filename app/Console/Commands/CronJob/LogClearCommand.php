<?php

namespace App\Console\Commands\CronJob;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class LogClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:clear';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dọn dẹp log file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Đang dọn dẹp log file...');
        $this->clearLog();
        $this->info('Dọn dẹp log file hoàn tất!');
    }

    /**
     * Clear log file
     */

    private function clearLog()
    {
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            File::put($logFile, '');
        }
    }
}
