<?php

namespace mlusas\LaravelLoomUpdates\Tests\Feature;

use Orchestra\Testbench\TestCase;
use mlusas\LaravelLoomUpdates\LoomUpdatesServiceProvider;

class ListLoomUrlsTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [LoomUpdatesServiceProvider::class];
    }

    public function test_command_exists()
    {
        $this->artisan('loom:list')
             ->assertExitCode(0);
    }

    // Add more tests as needed
}