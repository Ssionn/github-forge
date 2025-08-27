<?php

declare(strict_types=1);

namespace Ssionn\GithubForgeLaravel;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Ssionn\GithubForgeLaravel\Contracts\GithubClientInterface;
use Ssionn\GithubForgeLaravel\Traits\ApiActionTrait;

/**
 * GitHub API Client for Laravel applications.
 */
class GithubClient implements GithubClientInterface
{
    use ApiActionTrait;

    public function __construct(protected string $token)
    {
    }

    /**
     * Get a user's profile by username.
     *
     * @param string $username
     *
     * @return array|null
     * @throws ConnectionException
     */
    public function getUser(string $username): ?array
    {
        return $this->getResponse("/users/{$username}");
    }

    /**
     * Get repositories for a GitHub user.
     *
     * @param string $username The username of the GitHub user.
     * @param array<string, mixed> $queryParams Optional query parameters.
     * e.g., ['type' => 'owner', 'sort' => 'updated', 'per_page' => 50]
     *
     * @return Collection
     * @throws ConnectionException
     */
    public function getRepositoriesByUsername(
        string $username,
        array $queryParams = []
    ): Collection {
        return $this->getPaginatedResponse(
            "/users/{$username}/repos",
            $queryParams
        );
    }

    /**
     * Get repositories for a GitHub user by token. This also returns private repositories and repositories you've contributed to.
     *
     * @param array<string, mixed> $queryParams Optional query parameters.
     * e.g., ['type' => 'owner', 'sort' => 'updated', 'per_page' => 50]
     *
     * @return Collection
     * @throws ConnectionException
     */
    public function getRepositoriesByToken(
        array $queryParams = []
    ): Collection {
        return $this->getPaginatedResponse(
            "/user/repos",
            $queryParams
        );
    }

    /**
     * Get information about a specific repository.
     *
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     *
     * @return array|null
     * @throws ConnectionException
     */
    public function getRepository(string $owner, string $repo): ?array
    {
        return $this->getResponse("/repos/{$owner}/{$repo}");
    }

    /**
     * Get commits from a repository.
     *
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param array<string, mixed> $queryParams Optional query parameters.
     * e.g., ['sha' => 'main', 'path' => 'src/']
     *
     * @return Collection
     * @throws ConnectionException
     */
    public function getCommitsFromRepository(
        string $owner,
        string $repo,
        array $queryParams = []
    ): Collection {
        return $this->getPaginatedResponse(
            "/repos/{$owner}/{$repo}/commits",
            $queryParams
        );
    }

    /**
     * Get all contributors from a repository.
     *
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     *
     * @return array|null
     * @throws ConnectionException
     */
    public function getContributors(string $owner, string $repo): ?array
    {
        return $this->getResponse("/repos/{$owner}/{$repo}/contributors");
    }

    /**
     * Get issues from a repository.
     *
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param array<string, mixed> $queryParams Optional query parameters.
     * e.g., ['state' => 'closed', 'labels' => 'bug']
     *
     * @return Collection
     * @throws ConnectionException
     */
    public function getIssues(
        string $owner,
        string $repo,
        array $queryParams = []
    ): Collection {
        return $this->getPaginatedResponse(
            "/repos/{$owner}/{$repo}/issues",
            $queryParams
        );
    }

    /**
     * Get pull requests from a repository.
     *
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param array<string, mixed> $queryParams Optional query parameters.
     * e.g., ['state' => 'all', 'sort' => 'created']
     *
     * @return Collection
     * @throws ConnectionException
     */
    public function getPullRequests(
        string $owner,
        string $repo,
        array $queryParams = []
    ): Collection {
        return $this->getPaginatedResponse(
            "/repos/{$owner}/{$repo}/pulls",
            $queryParams
        );
    }

    /**
     * Sets the GitHub API token.
     *
     * @param string $token The GitHub API token.
     *
     * @returns GithubClientInterface
     */
    public function withToken(string $token): GithubClientInterface
    {
        return new self($token);
    }
}
