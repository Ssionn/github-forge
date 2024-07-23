<?php

namespace Ssionn\GithubForgeLaravel;

use Illuminate\Support\ServiceProvider;

class GithubForgeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/github-forge.php', 'github-forge');

        $this->app->singleton('github-forge', function ($app) {
            return new GithubClient();
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/github-forge.php' => config_path('github-forge.php'),
            ], 'config');
        }
    }
}
