# GitHub Forge Laravel

A Laravel package for interacting with the GitHub REST API. Facades are the preferred usage pattern.

## Installation

1) Require the package:

- `composer require ssionn/github-forge-laravel`

2) If you don’t use package auto-discovery, register the provider (only if needed):

- `Ssionn\GithubForgeLaravel\GithubForgeServiceProvider::class,`

3) Publish config (optional, for customizing defaults):

- `php artisan vendor:publish \
  --provider="Ssionn\GithubForgeLaravel\GithubForgeServiceProvider" \
  --tag=config`

4) In your .env (or .env.testing) set your token:

- `GITHUB_FORGE_TOKEN=your_github_token_here`

The package’s `config/github-forge.php` reads this by default.

## Configuration

config/github-forge.php

- return [
-     'token' => env('GITHUB_FORGE_TOKEN', ''),
- ];

Publish and override as needed.

## Facade (preferred usage)

- Bindings keep a default app-wide token (singleton) for non-auth flows or defaults.
- Use the facade with per-request tokens when needed via `withToken()`.

Alias in config/app.php (if you’re not using auto-discovery):

- `'GithubForge' => Ssionn\GithubForgeLaravel\Facades\GithubForge::class,`

Usage examples (facade-first)

- Per-request token (recommended when a user has a unique token):

```
    use Ssionn\GithubForgeLaravel\Facades\GithubForge;

    // Resolve the per-request token (user token or default)
    $token = auth()->user()?->github_token ?? config('github-forge.token');

    // Per-request client, then call
    $repos = GithubForge::withToken($token)->getRepositories('octocat', ['per_page' => 50]);
```

- Simple usage with the default app token:

- `$user = GithubForge::getUser('octocat');`

- Other methods (examples)

- `$repo = GithubForge::getRepository('laravel', 'framework');`
- `$contributors = GithubForge::getContributors('laravel', 'framework');`
- `$commits = GithubForge::getCommitsFromRepository('laravel', 'framework', [
  'sha' => 'main',
  'per_page' => 20,
]);`
- `$issues = GithubForge::getIssues('laravel', 'framework', [
  'state'    => 'closed',
  'per_page' => 30,
]);`
- `$pulls = GithubForge::getPullRequests('laravel', 'framework', [
  'state'    => 'all',
  'per_page' => 10,
]);`

## Token storage and migrations (new)

If you store GitHub tokens in the database, use encrypted storage and a separate hash for uniqueness.

What to add in migrations

- Add a column github_secret_token and a unique github_token_hash.
- Encrypt the github_secret_token via Eloquent’s encrypted cast.
- Compute and store github_token_hash from the plaintext token.

Example migration (to adapt to your user/model table; adjust table name as needed, e.g., users, github_accounts, etc.):

```
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGithubTokenColumnsToUsersTable extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Store the secret token encrypted at rest
            $table->text('github_secret_token')->nullable();
            // Hash for uniqueness (e.g., to prevent duplicates)
            $table->string('github_token_hash')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['github_secret_token', 'github_token_hash']);
        });
    }
}
```


Model changes (to apply encryption and hash)

- Assuming you store tokens on the User model (adjust to your real model).

```
class User extends Authenticatable
{
    // Encrypt the github_secret_token in storage
    protected $casts = [
        'github_secret_token' => 'encrypted',
    ];

    // Optional: hide sensitive fields from arrays/json
    protected $hidden = [
      'github_secret_token',
      'github_token_hash',
    ];
 
    // Ensure a hash is stored whenever the secret token is set
    public function setGithubSecretTokenAttribute(?string $value)
    {
        // Hash for uniqueness
        $this->attributes['github_token_hash'] = $value ? hash('sha256', $value) : null;
        // Store the encrypted token (the cast handles encryption)
        $this->attributes['github_secret_token'] = $value;
    }
}
```


Notes:
- The github_secret_token is encrypted at rest using Laravel’s encryption. Accessing auth()->user()->github_secret_token will yield the plaintext token due to the encrypted cast.
- github_token_hash is unique to prevent storing multiple identical tokens for the same user (or globally, depending on your schema).
- If you need to search by token value, you’ll query by github_token_hash, not github_secret_token.
- Ensure APP_KEY is set in your environment for encryption to work.
- If you already have tokens in place, you may need a one-time migration to populate github_token_hash for existing records.

Usage reminder

- For per-request tokens, prefer GithubForge::withToken($token) (facade-first).
- For defaults, rely on the singleton binding that reads GITHUB_FORGE_TOKEN from config (don't use `withToken(string $token)`.
