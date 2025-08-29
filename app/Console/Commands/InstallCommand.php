<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Throwable;
use function Laravel\Prompts\confirm;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all the installers configured by the starter kit for the application.';

    /**
     * The installers to run.
     *
     * @var array
     */
    protected $installers = [
        'Flux Pro' => 'installers:flux-pro',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach ($this->installers as $name => $command) {
            if (confirm("Would you like to install {$name}?")) {
                try {
                    Artisan::call($command, [], $this->getOutput());
                } catch (Throwable $e) {
                    $this->error("Failed to install {$name}: {$e->getMessage()}");
                }
            }
        }
    }
}
