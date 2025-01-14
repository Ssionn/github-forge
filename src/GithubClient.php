<?php

namespace Ssionn\GithubForgeLaravel;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;

/**
 * GitHub API Client for Laravel applications.
 */
class GithubClient
{
    private $token;
    private $baseUrl = 'https://api.github.com';

    const APPLICATIONTYPE = 'application/vnd.github.v3+json';
    const APIVERSION = '2022-11-28';

    public function __construct()
    {
        $this->token = config('github-forge.token');
    }

    /**
     * Get information about a GitHub user.
     *
     * @param string $username The username of the GitHub user
     * @return array|null User information
     */
    public function getUser(string $username): ?array
    {
        $response = Http::withHeaders([
            'Accept' => self::APPLICATIONTYPE,
            'Authorization' => 'Bearer ' . $this->token,
            'X-GitHub-Api-Version' => self::APIVERSION,
        ])->get("{$this->baseUrl}/users/{$username}");

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    /**
     * Get repositories for a GitHub user.
     *
     * @param string $username The username of the GitHub user
     * @param string $type Type of repositories to return. Can be one of: all, owner, member. Default: all
     * @param string $sort Property to sort by. Can be one of: created, updated, pushed, full_name. Default: full_name
     * @param string $direction Direction to sort by. Either asc or desc. Default: asc when using full_name, otherwise desc
     * @param int $perPage Number of results per page. Default: 25
     * @param int $page Page number of the results to fetch. Default: 1
     * @return array|null Collection of repositories
     */
    public function getRepositories(
        string $username,
        string $type = 'all',
        string $sort = 'full_name',
        string $direction = 'asc',
        int $perPage = 25,
        int $page = 1
    ): ?array {
        $allRepos = [];

        do {
            $response = Http::withHeaders([
                'Accept' => self::APPLICATIONTYPE,
                'Authorization' => 'Bearer ' . $this->token,
                'X-GitHub-Api-Version' => self::APIVERSION,
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

            $repos = $response->json();
            $allRepos = array_merge($allRepos, $repos);

            $page++;
        } while (count($repos) === $perPage);

        return $allRepos;
    }

    /**
     * Get information about a specific repository.
     *
     * @param string $owner The owner of the repository
     * @param string $repo The name of the repository
     * @return array|null Repository information
     */
    public function getRepository(string $owner, string $repo): ?array
    {
        $response = Http::withHeaders([
            'Accept' => self::APPLICATIONTYPE,
            'Authorization' => 'Bearer ' . $this->token,
            'X-GitHub-Api-Version' => self::APIVERSION,
        ])->get("{$this->baseUrl}/repos/{$owner}/{$repo}");

        if ($response->failed()) {
            return null;
        }

        return $response->json();
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
     * @param int $perPage Number of results per page. Default: 25
     * @param int $page Page number of the results to fetch. Default: 1
     * @return array Collection of commits
     */
    public function getCommitsFromRepository(
        string $owner,
        string $repo,
        ?string $sha = null,
        ?string $path = null,
        ?string $author = null,
        ?string $since = null,
        ?string $until = null,
        int $perPage = 25,
        int $page = 1
    ): array {
        $allCommits = [];

        do {
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
                'Accept' => self::APPLICATIONTYPE,
                'Authorization' => 'Bearer ' . $this->token,
                'X-GitHub-Api-Version' => self::APIVERSION,
            ])->get("{$this->baseUrl}/repos/{$owner}/{$repo}/commits", $params);

            if ($response->failed()) {
                return [];
            }

            $commits = $response->json();
            $allCommits = array_merge($allCommits, $commits);

            $page++;
        } while (count($commits) === $perPage);

        return $allCommits;
    }

    /**
     * Get all contributors from a repository.
     *
     * @param string $owner The owner of the repository
     * @param string $repo The name of the repository
     * @return array|null Array of contributors
     */
    public function getContributors(string $owner, string $repo): ?array
    {
        $response = Http::withHeaders([
            'Accept' => self::APPLICATIONTYPE,
            'Authorization' => 'Bearer ' . $this->token,
            'X-GitHub-Api-Version' => self::APIVERSION,
        ])->get("{$this->baseUrl}/repos/{$owner}/{$repo}/contributors");

        if ($response->failed()) {
            return null;
        }

        return $response->json();
    }

    /**
     * Get issues from a repository.
     *
     * @param string $owner The owner of the repository
     * @param string $repo The name of the repository
     * @param string $state Indicates the state of the issues to return. Can be either open, closed, or all. Default: open
     * @param int $perPage Number of results per page. Default: 25
     * @param int $page Page number of the results to fetch. Default: 1
     * @return array|null Array of issues
     */
    public function getIssues(
        string $owner,
        string $repo,
        string $state = 'open',
        int $perPage = 25,
        int $page = 1
    ): ?array {
        $allIssues = [];

        do {
            $response = Http::withHeaders([
                'Accept' => self::APPLICATIONTYPE,
                'Authorization' => 'Bearer ' . $this->token,
                'X-GitHub-Api-Version' => self::APIVERSION,
            ])->get("{$this->baseUrl}/repos/{$owner}/{$repo}/issues", [
                'state' => $state,
                'per_page' => $perPage,
                'page' => $page,
            ]);

            if ($response->failed()) {
                return null;
            }

            $issues = $response->json();
            $allIssues = array_merge($allIssues, $issues);

            $page++;
        } while (count($issues) === $perPage);

        return $allIssues;
    }

    /**
     * Get pull requests from a repository.
     *
     * @param string $owner The owner of the repository
     * @param string $repo The name of the repository
     * @param int $perPage Number of results per page. Default: 25
     * @param int $page Page number of the results to fetch. Default: 1
     * @return array|null Array of pull requests
     */
    public function getPullRequests(
        string $repo,
        string $owner,
        int $perPage = 25,
        int $page = 1
    ): ?array {
        $allPullRequests = [];

        do {
            $response = Http::withHeaders([
                'Accept' => self::APPLICATIONTYPE,
                'Authorization' => 'Bearer ' . $this->token,
                'X-GitHub-Api-Version' => self::APIVERSION,
            ])->get("{$this->baseUrl}/repos/{$owner}/{$repo}/pulls", [
                'per_page' => $perPage,
                'page' => $page,
            ]);

            if ($response->failed()) {
                return [];
            }

            $pullRequests = $response->json();
            $allPullRequests = array_merge($allPullRequests, $pullRequests);

            $page++;
        } while (count($pullRequests) === $perPage);

        return $allPullRequests;
    }
}
