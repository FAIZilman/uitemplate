<?php

namespace Tests;

class ServiceProviderTest extends TestCase
{
    public function test_service_provider_loaded()
    {
        $this->assertTrue(
            $this->app->providerIsLoaded(
                \Uitemplate\Laravel\UiTemplateServiceProvider::class
            )
        );
    }
}