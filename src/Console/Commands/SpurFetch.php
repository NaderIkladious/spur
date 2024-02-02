<?php

namespace Naderikladious\Spur\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Naderikladious\Spur\Spur;

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
        $configPath = config_path('spur.php');
        // Check if the configuration file exists
        if (Spur::configIsNotPublished()) {
            $this->warn('Configuration file not found. Please run \'php artisan vendor:publish --tag=spur-config\'');
            return;
        }

        // Load the configuration file
        $config = include($configPath);

        // Check if the 'components' key exists in the configuration
        if (!Spur::components()) {
            $this->error('No components found in the configuration file.');
            return;
        }

        // Display the list of components
        $this->info('Updating components...' );
        foreach (Spur::components() as $component) {
            $this->downloadComponent($component);
        }
        $this->info('Components are updated' );
    }

    protected function downloadComponent(string $component)
    {
        list($name) = explode(',', trim($component, '()\''));
        $componentFilePath = explode('/', $name);
        array_pop($componentFilePath);
        $componentPath = implode('/', $componentFilePath);
        $directoryPath = resource_path("views/components/{$componentPath}");
        $filePath = resource_path("views/components/{$name}.blade.php");
        if (!File::exists($filePath) || $this->option('force')) {
            $this->line('- '. $component);
            $cdnUrl = 'http://localhost:8003/';
            File::makeDirectory($directoryPath, 0755, true, true);

            $template = file_get_contents($cdnUrl.$name.'.blade.php');
            file_put_contents($filePath, $template);
        }
    }
}
