<?php

namespace Ssionn\GithubForgeLaravel\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @method static array|null getUser(string $username)
 * @method static Collection|null getRepositories(string $username, string $type = 'all', string $sort = 'full_name', string $direction = 'asc', int $perPage = 30, int $page = 1)
 * @method static array|null getRepository(string $owner, string $repo)
 * @method static Collection|null getCommitsFromRepository(string $owner, string $repo, ?string $sha = null, ?string $path = null, ?string $author = null, ?string $since = null, ?string $until = null, int $perPage = 30, int $page = 1)
 * @method static Collection|null getIssues(string $owner, string $repo, string $state = 'open', int $perPage = 30, int $page = 1)
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
