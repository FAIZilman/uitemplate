<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Uitemplate\Laravel\Components\Button;

class ExampleTest extends TestCase
{
    public function test_package_loaded()
    {
        $this->assertEquals("library works", Button::test());
    }
}