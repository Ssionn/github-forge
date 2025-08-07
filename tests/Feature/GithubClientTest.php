<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Ssionn\GithubForgeLaravel\Contracts\GithubClientInterface;
use Ssionn\GithubForgeLaravel\Facades\Github;
use Ssionn\GithubForgeLaravel\Facades\GithubForge;

it('can get a user profile', function () {
    $username = 'Ssionn';

    Http::fake([
        "https://api.github.com/users" => Http::response(['login' => $username], 200),
    ]);

    $user = GithubForge::getUser($username);

    expect($user)->toBeArray()
        ->and($user['login'])->toBe($username);
});

it('can get repositories for a user', function () {
    $username = 'ssionn';
    $request = Http::fake([
        "https://api.github.com/users/{$username}/repos" => Http::response([
            ['name' => '.dotfiles'],
            ['name' => 'Ssionn/BBB'],
        ], 200),
    ]);

    $repositories = GithubForge::getRepositories($username);

    $count = 0;

    foreach ($repositories as $repository) {
        $count++;
        expect($repository)->toBeArray()
            ->and($repository['name'])->not->toBeEmpty();
    }

    expect($repositories)->toBeInstanceOf(Collection::class)
        ->and($repositories->count())->toBe($count)
        ->and($repositories->first()['name'])->toBe('.dotfiles');
});
