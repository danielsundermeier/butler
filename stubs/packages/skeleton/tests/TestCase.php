<?php

namespace {{ vendor_namespace }}\{{ name_namespace }}\Tests;

use {{ vendor_namespace }}\{{ name_namespace }}\{{ name_namespace }}ServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
    }

    protected function getPackageProviders($app)
    {
        return [
            {{ name_namespace }}ServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
