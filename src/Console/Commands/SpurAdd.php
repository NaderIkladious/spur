<?php

namespace Spur\Spur\Console\Commands;

use Illuminate\Console\Command;
use Spur\Spur\Spur;

class SpurAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spur:add {components*} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Spur Components';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $configPath = config_path('spur.php');
        // Check if the configuration file exists
        if (Spur::configIsNotPublished()) {
            $this->warn('Configuration file not found. Please run \'php artisan vendor:publish --tag=spur-config\'');
            return;
        }

        // Check if the 'components' key exists in the configuration
        if (is_null(Spur::components())) {
            $this->error("'components' key is not existing in `config/Spur.php`");
            return;
        }

        $this->info('Adding components');
        foreach ($this->argument('components') as $component) {
            if (Spur::isComponentAlreadyAdded($component) && !$this->option('force')) {
                $this->line('- '. $component .' '. '<error>Already added</error>');
                continue;
            }
            $result = Spur::downloadComponent($component, $this->option('force'));
            if ($result) {
                $this->line('- '. $component .' '. '<info>Fetched</info>');
                if (!Spur::isComponentAlreadyAdded($component)) {
                    Spur::addComponentsToConfig($component);
                }
            } else {
                $this->line('- '. $component .' '. '<error>Skipped</error>');
            }
        }
    }
}
