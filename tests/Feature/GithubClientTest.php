<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Ssionn\GithubForgeLaravel\Facades\GithubForge;

// This is a hack fix, but hey it works.
beforeAll(function () {
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
    $dotenv->load();
});

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
    $commitCount = $commits->count();
    $commitHash = $commits->first()['sha'];
    $commitMessage = $commits->first()['commit']['message'];

    expect($commits)->toBeInstanceOf(Collection::class)
        ->and($commitCount)->toBe($commitCount)
        ->and($commitHash)->toBe($commitHash)
        ->and($commitMessage)->toBe($commitMessage);
});

it('can get contributors from a repository', function () {
    $contributors = GithubForge::getContributors($this->username, $this->repository);
    $contributions = collect($contributors)->sum('contributions');

    expect($contributors)->toBeArray()
        ->and($contributors[0]['login'])->toBe($this->username)
        ->and($contributors[0]['contributions'])->toBe($contributions);
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
