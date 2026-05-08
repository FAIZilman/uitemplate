<?php

namespace Uitemplate\Laravel\Console\Commands;

use Illuminate\Console\Command;
use Uitemplate\Laravel\Generators\ComponentGenerator;

class InstallCommand extends Command
{
    protected $signature = 'ui:add
        {name? : Component name}
        {--children=* : Child Component (comma / multiple)}
        {--force : Overwrite File}
        {--only-child : Generate Child Only}
        {--view : Generate Blade Only (no class)}
    ';

    protected $description = 'Install Component UITemplate into your Laravel project';

    public function handle()
    {
        $name = $this->argument('name');

        if ($name === null) {
            $name = $this->ask('What name do you want to add?');
        }

        // ✅ cek empty setelah ask (pisah dari null check)
        if (empty(trim($name ?? ''))) {
            $this->error("Name Component not empty");
            return 1;
        }

        $children  = $this->option('children');
        $force     = $this->option('force');
        $onlyChild = $this->option('only-child');
        $viewOnly  = $this->option('view');

        // === HEADER ===
        $this->line('');
        $this->info('UI Template Installer');
        $this->line('---------------------');
        $this->line('');
        $this->comment("Installing [{$name}] component...");
        $this->line('');

        $generator = new ComponentGenerator();

        try {
            $files = $generator->generate(
                $name,
                $children,
                $force,
                $onlyChild,
                $viewOnly
            );

            // === TIDAK ADA FILE YANG DI-GENERATE ===
            if (empty($files)) {
                $this->warn('⚠  Nothing generated (maybe already exists.)');
                $this->line('');
                $this->line('Use --force to overwrite existing files.');
                $this->line('');
                return 0;
            }

            // === OUTPUT PER FILE ===
            foreach ($files as $file) {
                // Ambil path relatif agar lebih ringkas di terminal
                $relativePath = str_replace(base_path() . '/', '', $file);

                // Bedakan blade dan class dari ekstensi
                if (str_ends_with($file, '.blade.php')) {
                    $this->line("  <fg=green>✔</> <fg=cyan>{$relativePath}</> <fg=gray>(blade)</>"); 
                } else {
                    $this->line("  <fg=green>✔</> <fg=cyan>{$relativePath}</> <fg=gray>(class)</>");
                }
            }

        } catch (\Exception $e) {
            $this->line('');
            $this->error($e->getMessage());
            $this->line('');
            return 1;
        }

        // === FOOTER ===
        $this->line('');
        $this->info('Installation completed successfully!');
        $this->line('');

        return 0;
    }
}