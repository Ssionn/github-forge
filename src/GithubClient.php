<?php

namespace Ssionn\GithubForgeLaravel;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * GitHub API Client for Laravel applications.
 */
class GithubClient
{
    private $token;
    private $baseUrl = 'https://api.github.com';

    public function __construct()
    {
        $this->token = config('github-forge.token');
    }

    /**
     * Get information about a GitHub user.
     *
     * @param string $username The username of the GitHub user
     * @return array|null User information
     * @throws Exception If the API request fails
     */
    public function getUser(string $username): ?array
    {
        $response = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json',
            'Authorization' => 'Bearer ' . $this->token,
            'X-GitHub-Api-Version' => '2022-11-28',
        ])->get("{$this->baseUrl}/users/{$username}");

        if ($response->failed()) {
            return null;
        }

        return Cache::remember('github_user', 3600, function ($response) {
            return $response->json();
        });
    }

    /**
     * Get repositories for a GitHub user.
     *
     * @param string $username The username of the GitHub user
     * @param string $type Type of repositories to return. Can be one of: all, owner, member. Default: all
     * @param string $sort Property to sort by. Can be one of: created, updated, pushed, full_name. Default: full_name
     * @param string $direction Direction to sort by. Either asc or desc. Default: asc when using full_name, otherwise desc
     * @param int $perPage Number of results per page. Default: 30
     * @param int $page Page number of the results to fetch. Default: 1
     * @return Collection|null Collection of repositories
     * @throws Exception If the API request fails
     */
    public function getRepositories(
        string $username,
        string $type = 'all',
        string $sort = 'full_name',
        string $direction = 'asc',
        int $perPage = 30,
        int $page = 1
    ): ?array {
        $response = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json',
            'Authorization' => 'Bearer ' . $this->token,
            'X-GitHub-Api-Version' => '2022-11-28',
        ])->get("{$this->baseUrl}/users/{$username}/repos", [
            'type' => $type,
            'sort' => $sort,
            'direction' => $direction,
            'per_page' => $perPage,
            'page' => $page,
        ]);

        if ($response->failed()) {
            return null;
        }

        return Cache::remember('github_repos_by_' . $username, 3600, function () use ($response) {
            return $response->json();
        });
    }

    /**
     * Get information about a specific repository.
     *
     * @param string $owner The owner of the repository
     * @param string $repo The name of the repository
     * @return array|null Repository information
     * @throws Exception If the API request fails
     */
    public function getRepository(string $owner, string $repo): ?array
    {
        $response = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json',
            'Authorization' => 'Bearer ' . $this->token,
            'X-GitHub-Api-Version' => '2022-11-28',
        ])->get("{$this->baseUrl}/repos/{$owner}/{$repo}");

        if ($response->failed()) {
            return null;
        }

        return Cache::remember('github_repo_' . $repo, 3600, function () use ($response) {
            return $response->json();
        });
    }

    /**
     * Get commits from a repository.
     *
     * @param string $owner The owner of the repository
     * @param string $repo The name of the repository
     * @param string|null $sha SHA or branch to start listing commits from
     * @param string|null $path Only commits containing this file path will be returned
     * @param string|null $author GitHub login or email address by which to filter by commit author
     * @param string|null $since Only commits after this date will be returned. This is a timestamp in ISO 8601 format: YYYY-MM-DDTHH:MM:SSZ
     * @param string|null $until Only commits before this date will be returned. This is a timestamp in ISO 8601 format: YYYY-MM-DDTHH:MM:SSZ
     * @param int $perPage Number of results per page. Default: 30
     * @param int $page Page number of the results to fetch. Default: 1
     * @return Collection|null Collection of commits
     */
    public function getCommitsFromRepository(
        string $owner,
        string $repo,
        ?string $sha = null,
        ?string $path = null,
        ?string $author = null,
        ?string $since = null,
        ?string $until = null,
        int $perPage = 50,
        int $page = 1
    ): ?array {
        $params = array_filter([
            'sha' => $sha,
            'path' => $path,
            'author' => $author,
            'since' => $since,
            'until' => $until,
            'per_page' => $perPage,
            'page' => $page,
        ]);

        $response = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json',
            'Authorization' => 'Bearer ' . $this->token,
            'X-GitHub-Api-Version' => '2022-11-28',
        ])->get("{$this->baseUrl}/repos/{$owner}/{$repo}/commits", $params);

        if ($response->failed()) {
            return null;
        }

        return Cache::remember('github_repo_' . $repo . '_commits', 3600, function () use ($response) {
            return $response->json();
        });
    }

    /**
     * Get issues from a repository.
     *
     * @param string $owner The owner of the repository
     * @param string $repo The name of the repository
     * @param string $state Indicates the state of the issues to return. Can be either open, closed, or all. Default: open
     * @param int $perPage Number of results per page. Default: 30
     * @param int $page Page number of the results to fetch. Default: 1
     * @return Collection|null Collection of issues
     */
    public function getIssues(
        string $owner,
        string $repo,
        string $state = 'open',
        int $perPage = 30,
        int $page = 1
    ): ?array {
        $response = Http::withHeaders([
            'Accept' => 'application/vnd.github.v3+json',
            'Authorization' => 'Bearer ' . $this->token,
            'X-GitHub-Api-Version' => '2022-11-28',
        ])->get("$this->baseUrl}/repos/{$owner}/{$repo}/issues", [
            'state' => $state,
            'per_page' => $perPage,
            'page' => $page,
        ]);

        if ($response->failed()) {
            return null;
        }

        return Cache::remember('github_repo_' . $repo . '_issues', 3600, function () use ($response) {
            return $response->json();
        });
    }
}
