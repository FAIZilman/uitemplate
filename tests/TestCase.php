<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Uitemplate\Laravel\UiTemplateServiceProvider;

abstract class TestCase extends BaseTestCase
{
    // Daftarkan package service provider
    protected function getPackageProviders($app)
    {
        return [
            UiTemplateServiceProvider::class,
        ];
    }
}