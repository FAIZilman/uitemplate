<?php

namespace Tests;

use Illuminate\Support\Facades\File;
use Uitemplate\Laravel\UitemplateServiceProvider;

class InstallCommandTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            UitemplateServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('uitemplate', [
            'card' => [
                'type'             => 'folder',
                'stub'             => 'card',
                'children'         => ['card-header', 'content'],
                'default_children' => ['card-header'],
            ],
            'button' => [
                'type' => 'file',
                'stub' => 'button',
            ],
        ]);
    }


    protected function tearDown(): void
{
    \Illuminate\Support\Facades\File::deleteDirectory(
        resource_path('views/components/ui')
    );

    \Illuminate\Support\Facades\File::deleteDirectory(
        app_path('View/Components/Ui')
    );

    parent::tearDown();
}

    // =========================================================
    // HEADER & FOOTER
    // =========================================================

    /** @test */
    public function test_shows_header_and_footer()
    {
        $this->artisan('ui:add', ['name' => 'card'])
            ->expectsOutput('UI Template Installer')
            ->expectsOutput('---------------------')
            ->expectsOutput('Installation completed successfully!')
            ->assertExitCode(0);
    }

    // =========================================================
    // VALIDASI NAMA
    // =========================================================

    /** @test */
    public function test_asks_name_when_not_provided()
    {
        $this->artisan('ui:add')
            ->expectsQuestion('What name do you want to add?', 'card')
            ->expectsOutput('Installation completed successfully!')
            ->assertExitCode(0);
    }

    /** @test */
    public function test_shows_error_when_name_empty()
    {
        $this->artisan('ui:add', ['name' => ''])
            ->expectsOutput('Name Component not empty')
            ->assertExitCode(1);
    }

    /** @test */
    public function test_shows_error_when_component_not_found_in_config()
    {
        $this->artisan('ui:add', ['name' => 'unknown'])
            ->expectsOutput('✗ Component [unknown] not found.')
            ->assertExitCode(1);
    }

    // =========================================================
    // TYPE FILE — button
    // =========================================================

    /** @test */
    public function test_generates_blade_file_for_button()
    {
        $this->artisan('ui:add', ['name' => 'button'])
            ->expectsOutput('Installation completed successfully!')
            ->assertExitCode(0);

        $this->assertFileExists(
            resource_path('views/components/ui/button.blade.php')
        );
    }

    /** @test */
    public function test_generates_blade_and_class_for_button()
    {
        $this->artisan('ui:add', ['name' => 'button'])
            ->assertExitCode(0);

        $this->assertFileExists(resource_path('views/components/ui/button.blade.php'));
        $this->assertFileExists(app_path('View/Components/Ui/Button.php'));
    }

    /** @test */
    public function test_button_with_view_flag_generates_blade_only()
    {
        $this->artisan('ui:add', ['name' => 'button', '--view' => true])
            ->assertExitCode(0);

        $this->assertFileExists(resource_path('views/components/ui/button.blade.php'));
        $this->assertFileDoesNotExist(app_path('View/Components/Ui/Button.php'));
    }

    // =========================================================
    // TYPE FOLDER — card
    // =========================================================

    /** @test */
    public function test_generates_blade_and_class_for_card()
    {
        $this->artisan('ui:add', ['name' => 'card'])
            ->assertExitCode(0);

        // parent
        $this->assertFileExists(resource_path('views/components/ui/card.blade.php'));
        $this->assertFileExists(app_path('View/Components/Ui/Card.php'));

        // default child - blade only
        $this->assertFileExists(resource_path('views/components/ui/children/card-header.blade.php'));
        $this->assertFileDoesNotExist(app_path('View/Components/Ui/CardHeader.php'));
    }

    /** @test */
    public function test_card_with_view_flag_generates_blade_only()
    {
        $this->artisan('ui:add', ['name' => 'card', '--view' => true])
            ->assertExitCode(0);

        // parent blade only
        $this->assertFileExists(resource_path('views/components/ui/card.blade.php'));
        $this->assertFileDoesNotExist(app_path('View/Components/Ui/Card.php'));

        // child blade only
        $this->assertFileExists(resource_path('views/components/ui/children/card-header.blade.php'));
        $this->assertFileDoesNotExist(app_path('View/Components/Ui/CardHeader.php'));
    }

    /** @test */
    public function test_only_child_skips_parent()
    {
        $this->artisan('ui:add', ['name' => 'card', '--only-child' => true])
            ->assertExitCode(0);

        // parent tidak ada
        $this->assertFileDoesNotExist(resource_path('views/components/ui/card.blade.php'));
        $this->assertFileDoesNotExist(app_path('View/Components/Ui/Card.php'));

        // child tetap ada
        $this->assertFileExists(resource_path('views/components/ui/children/card-header.blade.php'));
    }

    /** @test */
    public function test_generates_specific_children()
    {
        $this->artisan('ui:add', ['name' => 'card', '--children' => ['content']])
            ->assertExitCode(0);

        $this->assertFileExists(resource_path('views/components/ui/children/content.blade.php'));
        $this->assertFileDoesNotExist(app_path('View/Components/Ui/Content.php'));
    }

    /** @test */
    public function test_generates_all_children()
    {
        $this->artisan('ui:add', ['name' => 'card', '--children' => ['all']])
            ->assertExitCode(0);

        $this->assertFileExists(resource_path('views/components/ui/children/card-header.blade.php'));
        $this->assertFileExists(resource_path('views/components/ui/children/content.blade.php'));
    }

    /** @test */
    public function test_children_with_comma_string()
    {
        $this->artisan('ui:add', ['name' => 'card', '--children' => ['card-header,content']])
            ->assertExitCode(0);

        $this->assertFileExists(resource_path('views/components/ui/children/card-header.blade.php'));
        $this->assertFileExists(resource_path('views/components/ui/children/content.blade.php'));
    }

    /** @test */
    public function test_shows_error_for_invalid_child()
    {
        $this->artisan('ui:add', ['name' => 'card', '--children' => ['invalidChild']])
            ->expectsOutput('✗ Invalid child [invalid-child] for [card].')
            ->assertExitCode(1);
    }

    // =========================================================
    // FORCE
    // =========================================================

    /** @test */
    public function test_shows_warning_when_nothing_generated()
    {
        // generate pertama
        $this->artisan('ui:add', ['name' => 'card']);

        // generate kedua tanpa --force
        $this->artisan('ui:add', ['name' => 'card'])
            ->expectsOutput('⚠  Nothing generated (maybe already exists.)')
            ->expectsOutput('Use --force to overwrite existing files.')
            ->assertExitCode(0);
    }

    /** @test */
    public function test_force_overwrites_existing_files()
    {
        // generate pertama
        $this->artisan('ui:add', ['name' => 'card']);

        // generate kedua dengan --force
        $this->artisan('ui:add', ['name' => 'card', '--force' => true])
            ->expectsOutput('Installation completed successfully!')
            ->assertExitCode(0);

        $this->assertFileExists(resource_path('views/components/ui/card.blade.php'));
    }

    // =========================================================
    // CAMEL CASE NAME
    // =========================================================

    /** @test */
    public function test_camel_case_name_converted_to_kebab_for_blade()
    {
        // Asumsikan ada config 'cardHeader'
        $this->app['config']->set('uitemplate.cardHeader', [
            'type' => 'file',
            'stub' => 'cardHeader',
        ]);

        $this->artisan('ui:add', ['name' => 'cardHeader'])
            ->assertExitCode(0);

            $this->assertFileExists(
                resource_path('views/components/ui/card-header.blade.php')
            );
            $this->assertFileExists(
                app_path('View/Components/Ui/CardHeader.php')
            );
    }

    // =========================================================
    // PUBLISH TAG
    // =========================================================

    /** @test */
    public function test_publishes_uitemplate_provider_tag()
    {
        $this->artisan('vendor:publish', ['--tag' => 'uitemplate-provider'])
            ->assertExitCode(0);
    }

    /** @test */
    public function test_publishes_uitemplate_config_tag()
    {
        $this->artisan('vendor:publish', ['--tag' => 'uitemplate-config'])
            ->assertExitCode(0);
    }
}