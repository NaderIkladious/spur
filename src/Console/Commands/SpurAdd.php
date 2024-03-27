<?php

namespace Naderikladious\Spur\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Naderikladious\Spur\Spur;

class SpurAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spur:add {components*}';

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
        if (!Spur::components()) {
            $this->error("'components' key is not existing in `config/Spur.php`");
            return;
        }

        $this->info('Adding components');
        foreach ($this->argument('components') as $component) {
            if (Spur::isComponentAlreadyAdded($component)) {
                $this->line('- '. $component .' '. '<error>Already added</error>');
                continue;
            }
            Spur::addComponentsToConfig($component);
            $result = Spur::downloadComponent($component, false);
            if ($result) {
                $this->line('- '. $component .' '. '<info>Fetched</info>');
            } else {
                $this->line('- '. $component .' '. '<error>Skipped</error>');
            }
        }
    }
}
