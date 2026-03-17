<?php

namespace Uitemplate\Laravel\Console\Commands;
use Illuminate\Console\Command;

class InstallCommand extends Command
{

    protected $signature = 'install:ui 
    {component? : Component name}
    {--view : Install only Views}';
    protected $description = 'Install Component UITemplate into your Laravel project';

    public function handle()
    {
        $component = $this->argument('component');
        if (!$component) {
            $component = $this->ask('What component do you want to install?');
        }
        $view = $this->option('view');
        $componentLower = strtolower($component);
        $componentFirstUpper = ucfirst(strtolower($component));
        if ($this->confirm("Apakah kamu yakin menginstall component {$component}?", true)) {
            $resources = realpath(__DIR__ . "/../../components/{$componentLower}/{$componentLower}.blade.php");
            $destination = base_path("resources/views/components/ui/{$componentLower}.blade.php");
            if (!file_exists($resources)) {
                $this->error("Component [{$component}] not found. Make sure the component exists.");
            } else {
                $folder = dirname($destination);
                if (!is_dir($folder)) {
                    mkdir($folder, 0755, true);
                }
                if ($view) {
                    if (!file_exists($destination)) {
                        copy($resources, $destination);
                        $this->line('');
                        $this->info("UI Template Installer");
                        $this->line('---------------------');
                        $this->line('');

                        $this->comment("Installing {$component} component...");
                        $this->line('');

                        sleep(2);
                        $this->line("✔ {$component} component installed");
                        sleep(1);
                        $this->line("✔ Tailwind styles published");

                        $this->line('');
                        $this->line('Installation completed successfully!');
                        $this->line('');

                        return 0;
                    } else {
                        $this->error("Component Blade already exists!");
                    }
                } else {
                    $resourcesClass = realpath(__DIR__ . "/../../components/{$componentLower}/{$componentFirstUpper}.php");
                    $destinationClass = base_path("app/View/Components/Ui/{$componentFirstUpper}.php");

                    $folderClass = dirname($destinationClass);
                    if (!is_dir($folderClass)) {
                        mkdir($folderClass, 0755, true);
                    }

                    if (!file_exists($destinationClass)) {

                        copy($resourcesClass, $destinationClass);
                        $this->line('');
                        $this->info("UI Template Installer");
                        $this->line('---------------------');
                        $this->line('');

                        $this->comment("Installing {$component} component...");
                        $this->line('');

                        sleep(2);
                        $this->line("✔ {$component} component installed");
                        sleep(1);
                        $this->line("✔ Tailwind styles published");
                        sleep(1);
                        $this->line("✔ Config file Created");

                        if (!file_exists($destination)) {
                            copy($resources, $destination);
                        } else {
                            $this->info("Component Blade already exists!");
                        }

                        $this->line('');
                        $this->line('Installation completed successfully!');
                        $this->line('');

                        return 0;
                    } else {
                        $this->error("Component already exists!");
                    }
                }
            }

        }
    }
}