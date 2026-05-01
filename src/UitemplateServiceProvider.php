<?php

namespace Uitemplate\Laravel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Uitemplate\Laravel\Console\Commands\InstallCommand;
class UitemplateServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            InstallCommand::class,
        ]);
        
          $configPath = __DIR__ . '/../config/uitemplate.php';

    if (file_exists($configPath)) {
        $this->mergeConfigFrom($configPath, 'uitemplate');
    }
    }
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views/components/ui', 'uitemplate');
        $this->publishes([
            __DIR__ . '/../resources/views/components/ui' => resource_path('views/vendor/uitemplate/ui')
        ]);
            $this->publishes([
            __DIR__ . '/../config/uitemplate.php' => config_path('uitemplate.php'),
        ], 'uitemplate-config');
        Blade::componentNamespace('Uitemplate\\Laravel\\Views\\Components\\Ui', 'uitemplate');
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}