<?php

declare(strict_types=1);

namespace Ssionn\GithubForgeLaravel\Contracts;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;

interface GithubClientInterface
{
    /**
     * Get a user's profile.
     *
     * @param string $username
     *
     * @return array|null
     * @throws ConnectionException
     */
    public function getUser(string $username): ?array;

    /**
     * Get repositories for a GitHub user.
     *
     * @param string $username The username of the GitHub user.
     * @param array<string, mixed> $queryParams Optional query parameters.
     *
     * @return Collection
     * @throws ConnectionException
     */
    public function getRepositories(
        string $username,
        array $queryParams = []
    ): Collection;

    /**
     * Get information about a specific repository.
     *
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     *
     * @return array|null
     * @throws ConnectionException
     */
    public function getRepository(string $owner, string $repo): ?array;

    /**
     * Get commits from a repository.
     *
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param array<string, mixed> $queryParams Optional query parameters.
     *
     * @return Collection
     * @throws ConnectionException
     */
    public function getCommitsFromRepository(
        string $owner,
        string $repo,
        array $queryParams = []
    ): Collection;

    /**
     * Get all contributors from a repository.
     *
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     *
     * @return array|null
     * @throws ConnectionException
     */
    public function getContributors(string $owner, string $repo): ?array;

    /**
     * Get issues from a repository.
     *
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param array<string, mixed> $queryParams Optional query parameters.
     *
     * @return Collection
     * @throws ConnectionException
     */
    public function getIssues(
        string $owner,
        string $repo,
        array $queryParams = []
    ): Collection;

    /**
     * Get pull requests from a repository.
     *
     * @param string $owner The owner of the repository.
     * @param string $repo The name of the repository.
     * @param array<string, mixed> $queryParams Optional query parameters.
     *
     * @return Collection
     * @throws ConnectionException
     */
    public function getPullRequests(
        string $owner,
        string $repo,
        array $queryParams = []
    ): Collection;
}
