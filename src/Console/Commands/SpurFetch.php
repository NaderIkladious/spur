<?php

namespace Spur\Spur\Console\Commands;

use Illuminate\Console\Command;
use Spur\Spur\Spur;

class SpurFetch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spur:fetch {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch Registered Spur Components';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if the configuration file exists
        if (Spur::configIsNotPublished()) {
            $this->warn('Configuration file not found. Please run \'php artisan vendor:publish --tag=spur-config\'');

            return;
        }

        // Check if the 'components' key exists in the configuration
        if (! Spur::components()) {
            $this->error('No components found in the configuration file.');

            return;
        }

        // Display the list of components
        $this->info('Updating components...');
        foreach (Spur::components() as $component) {
            $result = Spur::downloadComponent($component, $this->option('force'));
            if ($result) {
                $this->line('- '.$component.' '.'<info>Fetched</info>');
            } else {
                $this->line('- '.$component.' '.'<error>Skipped</error>');
            }
        }
        $this->info('Components completed');
    }
}
