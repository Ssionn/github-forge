<?php

namespace Ssionn\GithubForgeLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array|null getUser(string $username)
 * @method static array|null getRepositories(string $username, string $type = 'all', string $sort = 'full_name', string $direction = 'asc', int $perPage = 30, int $page = 1)
 * @method static array|null getRepository(string $owner, string $repo)
 * @method static array|null getContributors(string $owner, string $repo)
 * @method static array|null getCommitsFromRepository(string $owner, string $repo, ?string $sha = null, ?string $path = null, ?string $author = null, ?string $since = null, ?string $until = null, int $perPage = 30, int $page = 1)
 * @method static array|null getIssues(string $owner, string $repo, string $state = 'open', int $perPage = 30, int $page = 1)
 *
 */
class GithubForge extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'github-forge';
    }
}
