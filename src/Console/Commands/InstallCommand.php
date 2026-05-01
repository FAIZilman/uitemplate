<?php

namespace Uitemplate\Laravel\Console\Commands;
use Illuminate\Console\Command;
use Uitemplate\Laravel\Generators\ComponentGenerator;

class InstallCommand extends Command
{

    protected $signature = 'ui:add
    {name? : Component name}
    {--children=* : Child Component (comma / multiple)}
    {--force : Overwite File}
    {--only-child : Generate Child Only}
    ';

    protected $description = 'Install Component UITemplate into your Laravel project';

    public function handle()
    {
        $name = $this->argument('name');

        if (!$name) {
            $name = $this->ask('What name do you want to add?');
        }

        $children = $this->option('children');
        $force = $this->option('force');
        $onlyChild = $this->option('only-child');

        $generator = new ComponentGenerator();

            $files = $generator->generate(
                $name,
                $children,
                $force,
                $onlyChild
            );

            if (empty($files)) {
                $this->warn('Nothing generated (maybe already exists).');
                return Command::SUCCESS;
            }



        // $child = $this->option("child");
        // if ($child === "all") {
        //     var_dump("this is all");
        // } else {
        //     $childExplode = explode(',', $child);
        //     $changeText = function ($text) {
        //         return strtolower(rtrim(ltrim(implode('-', explode('/', trim(preg_replace('/([A-Z])/', '/$1', $text)))), "-"), "-"));
        //     };
        //     $map = array_map($changeText, $childExplode);
        //     var_dump($map);

        // }

        // var_dump($map);
        // foreach ($map as $a) {
        // var_dump($a);
        // }
        // $child = implode('-', explode(' ', trim(preg_replace('/([A-Z])/', ' $1', $child))));

        // $view = $this->option('view');
        // $componentName = implode('-', explode(' ', trim(preg_replace('/([A-Z])/', ' $1', $component))));
        // if ($this->confirm("Apakah kamu yakin menginstall component {$component}?", true)) {
        //     $resources = realpath(__DIR__ . "/../../components/{$componentName}/{$componentName}.blade.php");
        //     $destination = base_path("resources/views/components/ui/{$componentName}.blade.php");
        //     if (!file_exists($resources)) {
        //         $this->error("Component [{$component}] not found. Make sure the component exists.");
        //     } else {
        //         $folder = dirname($destination);
        //         if (!is_dir($folder)) {
        //             mkdir($folder, 0755, true);
        //         }
        //         if ($view) {
        //             if (!file_exists($destination)) {
        //                 copy($resources, $destination);
        //                 $this->line('');
        //                 $this->info("UI Template Installer");
        //                 $this->line('---------------------');
        //                 $this->line('');

        //                 $this->comment("Installing {$component} component...");
        //                 $this->line('');

        //                 sleep(2);
        //                 $this->line("✔ {$component} component installed");
        //                 sleep(1);
        //                 $this->line("✔ Tailwind styles published");

        //                 $this->line('');
        //                 $this->line('Installation completed successfully!');
        //                 $this->line('');

        //                 return 0;
        //             } else {
        //                 $this->error("Component Blade already exists!");
        //             }
        //         } else {
        //             $className = str_replace('-', '', ucwords($component, '-'));
        //             $resourcesClass = realpath(__DIR__ . "/../../components/{$componentName}/{$$className}.php");
        //             $destinationClass = base_path("app/View/Components/Ui/{$className}.php");

        //             $folderClass = dirname($destinationClass);
        //             if (!is_dir($folderClass)) {
        //                 mkdir($folderClass, 0755, true);
        //             }

        //             if (!file_exists($destinationClass)) {

        //                 copy($resourcesClass, $destinationClass);
        //                 $this->line('');
        //                 $this->info("UI Template Installer");
        //                 $this->line('---------------------');
        //                 $this->line('');

        //                 $this->comment("Installing {$component} component...");
        //                 $this->line('');

        //                 sleep(2);
        //                 $this->line("✔ {$component} component installed");
        //                 sleep(1);
        //                 $this->line("✔ Tailwind styles published");
        //                 sleep(1);
        //                 $this->line("✔ Config file Created");

        //                 if (!file_exists($destination)) {
        //                     copy($resources, $destination);
        //                 } else {
        //                     $this->info("Component Blade already exists!");
        //                 }

        //                 $this->line('');
        //                 $this->line('Installation completed successfully!');
        //                 $this->line('');

        //                 return 0;
        //             } else {
        //                 $this->error("Component already exists!");
        //             }
        //         }
        //     }

        // }
    }
}