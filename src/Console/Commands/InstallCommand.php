<?php

namespace Uitemplate\Laravel\Console\Commands;
use Illuminate\Console\Command;

class InstallCommand extends Command
{

    protected $signature = 'install:ui {component}';
    protected $description = 'Installing Component....';

    public function handle()
    {
        $component = $this->argument('component');

        $resources = realpath(__DIR__ . '/../../Stubs/Button/button.blade.php');

        $destination = base_path('resources/views/components/ui/' . $component . '.blade.php');

        $folder = dirname($destination);
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }


        if (!file_exists($destination)) {
            copy($resources, $destination);
            $this->info("Component button kuuu!");
            return 0;
        } else {
            $this->error("Component already exists!");
        }
    }
}