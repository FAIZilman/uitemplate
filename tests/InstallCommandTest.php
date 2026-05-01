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

   protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('uitemplate', [
            'card' => [
                'type' => 'folder',
                'stub' => 'card',
                'children' => ['card-header', 'content'],
                'default_children' => ['card-header'],
            ],
        ]);
    }

    public function test_install_command_runs()
    {
        $targetDir = resource_path('views/components/ui/card');

        // Bersihin sebelum test
        if (file_exists($targetDir)) {
            function deleteDir($targetDir) {
                if (!file_exists($targetDir)) return true;
                if (!is_dir($targetDir)) return unlink($targetDir);
                
                foreach (scandir($targetDir) as $item) {
                    if ($item == '.' || $item == '..') continue;
                    if (!deleteDir($dir . DIRECTORY_SEPARATOR . $item)) return false;
                }
                return rmdir($targetDir);
            }
        }
        // $destination = base_path('resources/views/components/ui/button.blade.php');
        // if (file_exists($destination)) {
        //     unlink($destination);
        // }
        $this->artisan('ui:add card --children=cardHeader --only-child')
            ->assertExitCode(0);


        // ✅ child harus ada
        $this->assertFileExists($targetDir . '/card-header.blade.php');

    }
}