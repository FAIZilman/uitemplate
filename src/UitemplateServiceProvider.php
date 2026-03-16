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
    }
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views/components/ui', 'uitemplate');
        $this->publishes([
            __DIR__ . '/../resources/views/components/ui' => resource_path('views/vendor/uitemplate/ui')
        ]);
        Blade::componentNamespace('Uitemplate\\Laravel\\Views\\Components\\Ui', 'uitemplate');
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }
    }
}