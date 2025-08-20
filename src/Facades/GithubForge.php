<?php

declare(strict_types=1);

namespace Ssionn\GithubForgeLaravel\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Ssionn\GithubForgeLaravel\Contracts\GithubClientInterface;

/**
 * @method static array|null getUser(string $username)
 * @method static Collection getRepositoriesByUsername(string $username, array $queryParams = [])
 * @method static Collection getRepositoriesByToken(array $queryParams = [])
 * @method static array|null getRepository(string $owner, string $repo)
 * @method static Collection getCommitsFromRepository(string $owner, string $repo, array $queryParams = [])
 * @method static array|null getContributors(string $owner, string $repo)
 * @method static Collection getIssues(string $owner, string $repo, array $queryParams = [])
 * @method static Collection getPullRequests(string $owner, string $repo, array $queryParams = [])
 * @method static void withToken(string $token);
 *
 * @see \Ssionn\GithubForgeLaravel\GithubClient
 */
class GithubForge extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * This is the facade accessor. It should return the binding key
     * you used in your service provider.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return GithubClientInterface::class;
    }
}
