<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Ssionn\GithubForgeLaravel\Facades\Github;
use Ssionn\GithubForgeLaravel\Facades\GithubForge;
use Ssionn\GithubForgeLaravel\GithubForgeServiceProvider;

abstract class TestCase extends BaseTestCase
{
    /**
     * Get package providers.
     *
     * @param  Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            GithubForgeServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('github-forge.token', env('GITHUB_API_TOKEN'));

        $app['config']->set('app.aliases', [
            'GithubForge' => GithubForge::class,
        ]);
    }
}
