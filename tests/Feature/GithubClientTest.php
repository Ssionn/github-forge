<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Ssionn\GithubForgeLaravel\Contracts\GithubClientInterface;
use Ssionn\GithubForgeLaravel\Facades\Github;
use Ssionn\GithubForgeLaravel\Facades\GithubForge;

it('can get a user profile', function () {
    $username = 'octocat';
    Http::fake([
        "https://api.github.com/users/{$username}" => Http::response(['login' => $username], 200),
    ]);

    $user = GithubForge::getUser($username);

    expect($user)->toBeArray()
        ->and($user['login'])->toBe($username);
});
