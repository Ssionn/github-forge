# GitHub Forge Laravel

A Laravel package for seamless interaction with the GitHub REST API.

## Installation

1. Require the package:

   ```bash
   composer require ssionn/github-forge-laravel
   ```

2. (If you don’t use package auto-discovery) add to `config/app.php` providers:

   ```php
   Ssionn\GithubForgeLaravel\GithubForgeServiceProvider::class,
   ```

3. Publish config:

   ```bash
   php artisan vendor:publish \
     --provider="Ssionn\GithubForgeLaravel\GithubForgeServiceProvider" \
     --tag=config
   ```

4. In your `.env` (or `.env.testing`) set your token:

   ```
   GITHUB_FORGE_TOKEN=your_github_token_here
   ```

   The package’s `config/github-forge.php` reads this by default.

## Usage

Resolve the client any way you like:

```php
use Ssionn\GithubForgeLaravel\Contracts\GithubClientInterface;
use Ssionn\GithubForgeLaravel\Facades\GithubForge;

// via container + interface
$client = app(GithubClientInterface::class);

// via container + alias
$client = app('github-forge');

// via Facade
$user = GithubForge::getUser('octocat');
```

All methods throw `\Illuminate\Http\Client\ConnectionException` on HTTP error.

### Methods

#### getUser

```php
public function getUser(string $username): ?array;
```

Fetch a user profile.

```php
$user = GithubForge::getUser('octocat');
```

Returns `null` if not found.

#### getRepositories

```php
public function getRepositories(
    string $username,
    array $queryParams = []
): \Illuminate\Support\Collection;
```

List a user’s repos. Supports any GitHub API query params:

- `type`: `all|owner|member`
- `sort`: `created|updated|pushed|full_name`
- `direction`: `asc|desc`
- `per_page`, etc.

```php
$repos = GithubForge::getRepositories('octocat', [
    'type'     => 'owner',
    'sort'     => 'updated',
    'per_page' => 50,
]);
```

#### getRepository

```php
public function getRepository(string $owner, string $repo): ?array;
```

Fetch a single repository.

```php
$repo = GithubForge::getRepository('laravel', 'framework');
```

#### getContributors

```php
public function getContributors(string $owner, string $repo): ?array;
```

List repository contributors.

```php
$contributors = GithubForge::getContributors('laravel', 'framework');
```

#### getCommitsFromRepository

```php
public function getCommitsFromRepository(
    string $owner,
    string $repo,
    array $queryParams = []
): \Illuminate\Support\Collection;
```

List commits. Supports `sha`, `path`, `author`, `since`, `until`, `per_page`, etc.

```php
$commits = GithubForge::getCommitsFromRepository('laravel', 'framework', [
    'sha'      => 'main',
    'per_page' => 20,
]);
```

#### getIssues

```php
public function getIssues(
    string $owner,
    string $repo,
    array $queryParams = []
): \Illuminate\Support\Collection;
```

List issues. Supports `state`, `labels`, `per_page`, etc.

```php
$issues = GithubForge::getIssues('laravel', 'framework', [
    'state'    => 'closed',
    'labels'   => 'bug,help wanted',
    'per_page' => 30,
]);
```

#### getPullRequests

```php
public function getPullRequests(
    string $owner,
    string $repo,
    array $queryParams = []
): \Illuminate\Support\Collection;
```

List pull requests. Supports `state`, `sort`, `per_page`, etc.

```php
$pulls = GithubForge::getPullRequests('laravel', 'framework', [
    'state'    => 'all',
    'per_page' => 10,
]);
```

## Configuration

`config/github-forge.php`:

```php
return [
    'token' => env('GITHUB_FORGE_TOKEN', ''),
];
```

Publish and override as needed.

## Facade

Alias in `config/app.php`:

```php
'aliases' => [
    // ...
    'GithubForge' => Ssionn\GithubForgeLaravel\Facades\GithubForge::class,
],
```

Use `GithubForge::…` statically anywhere.

## License

MIT.
