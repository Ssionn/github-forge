<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Ssionn\GithubForgeLaravel\Facades\GithubForge;

beforeEach(function () {
    $this->username = 'Ssionn';
    $this->repository = 'github-forge';
});

it('can get a user profile', function () {
    $user = GithubForge::getUser($this->username);

    expect($user)->toBeArray()
        ->and($user['login'])->toBe($this->username);
});

it('can get repositories for a user', function () {
    $repositories = GithubForge::getRepositories($this->username);

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

it('can get a specific repository', function () {
    $repository = GithubForge::getRepository($this->username, $this->repository);

    expect($repository)->toBeArray()
        ->and($repository['name'])->toBe($this->repository)
        ->and($repository['owner']['login'])->toBe($this->username);
});

it('can get commits from a repository', function () {
    $commits = GithubForge::getCommitsFromRepository($this->username, $this->repository);

    expect($commits)->toBeInstanceOf(Collection::class)
        ->and($commits->count())->toBe($commits->count())
        ->and($commits->first()['sha'])->toBe('2ee19c6294622c0a05bf0a51e3ebc7706de36b71')
        ->and($commits->first()['commit']['message'])->toBe('Array fix for pull requests');
});

it('can get contributors from a repository', function () {
    $contributors = GithubForge::getContributors($this->username, $this->repository);

    expect($contributors)->toBeArray()
        ->and($contributors[0]['login'])->toBe($this->username)
        ->and($contributors[0]['contributions'])->toBe(count($contributors));
});

it('can get issues from a repository', function () {
    $issues = GithubForge::getIssues($this->username, $this->repository);

    expect($issues)->toBeInstanceOf(Collection::class)
        ->and($issues->count())->toBe(0);
});

it('can get pull requests from a repository', function () {
    $pullRequests = GithubForge::getPullRequests($this->username, $this->repository);

    expect($pullRequests)->toBeInstanceOf(Collection::class)
        ->and($pullRequests->count())->toBe(0);
});
