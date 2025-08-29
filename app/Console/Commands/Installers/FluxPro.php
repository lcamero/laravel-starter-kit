<?php

namespace App\Console\Commands\Installers;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class FluxPro extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'installers:flux-pro';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Flux Pro and configure authentication.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Installing Flux Pro...');

        $this->runProcess('php artisan flux:activate');
    }

    /**
     * Run a process.
     *
     * @param  string  $command
     * @return void
     */
    protected function runProcess($command)
    {
        Process::tty(true)->run($command);
    }
}
