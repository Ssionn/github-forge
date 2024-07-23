# GitHub Forge Laravel

A Laravel package to integrate GitHub API functionality.

## Installation

1. Require the package via Composer:
   ```sh
   composer require ssionn/github-forge-laravel
   ```

3. (If you are using Laravel 5.5 or less) Register the Service Provider:
   Add the following line to the `providers` array in `config/app.php`:
   ```php
   'providers' => [
       // Other Service Providers

       Ssionn\GithubForgeLaravel\GitHubForgeServiceProvider::class,
   ],
   ```

5. Publish the configuration file:
   ```sh
   php artisan vendor:publish --provider="Ssionn\GithubForgeLaravel\GithubForgeServiceProvider" --tag=config
   ```

6. Set your GitHub API token in the configuration file `config/github-forge.php`:
   ```php
   return [
       'token' => env('GITHUB_API_TOKEN', 'your-github-token-here'),
   ];
   ```

## Usage

You can use the `GithubClient` in your application directly, like this:

```php
use Ssionn\GithubForgeLaravel\GitHubClient;

$client = app('github-forge');
$response = $client->getUser($username);
```

Alternatively, you can use the provided facade for a more convenient and cleaner syntax:

```php
use Ssionn\GithubForgeLaravel\Facades\GithubForge;

$response = GithubForge::getUser($username);
```

The facade methods are as follows:

- `GithubForge::getUser(string $username)`
- `GithubForge::getRepositories(string $username, string $type = 'all', string $sort = 'full_name', string $direction = 'asc', int $perPage = 30, int $page = 1)`
- `GithubForge::getRepository(string $owner, string $repo)`
- `GithubForge::getCommitsFromRepository(string $owner, string $repo, ?string $sha = null, ?string $path = null, ?string $author = null, ?string $since = null, ?string $until = null, int $perPage = 30, int $page = 1)`
- `GithubForge::getIssues(string $owner, string $repo, string $state = 'open', int $perPage = 30, int $page = 1)`
- `GithubForge::fetchAll(string $endpoint, array $params = [])`
- `GithubForge::getAllRepositories(string $username, array $params = [])`
- `GithubForge::getPaginatedRepositories(string $username, int $perPage = 30, int $page = 1, array $params = [])`

Choose the method that best suits your needs and enjoy a streamlined integration with GitHub's API.

## License

This package is licensed under the MIT License.

