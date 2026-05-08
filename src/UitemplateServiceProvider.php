<?php
namespace Uitemplate\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Uitemplate\Laravel\Console\Commands\InstallCommand;
class UitemplateServiceProvider extends ServiceProvider
{
    public function register()
    {
        $configPath = __DIR__ . '/../config/uitemplate.php';
        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'uitemplate');
        }
    }
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views/components/ui', 'uitemplate');
        $this->publishes([
        __DIR__ . '/../stubs/UitemplateServiceProvider.php'
            => app_path('Providers/UitemplateServiceProvider.php'),
        ], 'uitemplate-provider');
        $this->publishes([
            __DIR__ . '/../config/uitemplate.php' => config_path('uitemplate.php'),
        ], 'uitemplate-config');
        Blade::componentNamespace('App\View\Components\Ui', 'uitemplate');
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }

    }
}