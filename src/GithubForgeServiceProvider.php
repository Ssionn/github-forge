<?php

namespace Ssionn\GithubForgeLaravel;

use Illuminate\Support\ServiceProvider;

class GithubForgeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GithubClient::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/github-forge.php' => config_path('github-forge.php'),
        ], 'config');
    }

    public function provides(): array
    {
        return [GithubClient::class];
    }
}
