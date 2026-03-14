<?php

namespace Tests;

use Uitemplate\Laravel\UiTemplateServiceProvider;

class InstallCommandTest extends TestCase
{
    // Daftarkan service provider package
    protected function getPackageProviders($app)
    {
        return [
            UiTemplateServiceProvider::class,
        ];
    }

    public function test_install_command_runs()
    {
        $destination = base_path('resources/views/components/ui/button.blade.php');
        if (file_exists($destination)) {
            unlink($destination);
        }
        $this->artisan('install:ui', ['component' => 'button'])
            ->assertExitCode(0);

    }
}