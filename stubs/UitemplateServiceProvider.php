<?php
namespace Uitemplate\Laravel\stubs;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class UitemplateServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::componentNamespace('App\View\Components\Ui', 'uitemplate');
    }
}