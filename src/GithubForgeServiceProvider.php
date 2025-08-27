<?php

namespace Ssionn\GithubForgeLaravel;

use Illuminate\Support\ServiceProvider;
use Ssionn\GithubForgeLaravel\Contracts\GithubClientInterface;

class GithubForgeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/github-forge.php',
            'github-forge'
        );

        $this->app->singleton(
            'github-forge',
            fn ($app) => new GithubClient(
                $app['config']->get('github-forge.token')
            )
        );

        $this->app->alias(
            'github-forge',
            GithubClientInterface::class
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/github-forge.php'
            => config_path('github-forge.php'),
        ], 'config');
    }

    public function provides(): array
    {
        return [
            'github-forge',
            GithubClientInterface::class,
        ];
    }
}
