<?php

declare(strict_types=1);

namespace Ssionn\GithubForgeLaravel\Traits;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Ssionn\GithubForgeLaravel\Constants\Constants;

trait ApiActionTrait
{
    protected string $token;

    public function __construct()
    {
        $this->token = config('github_forge.token');
    }

    /**
     *
     * @param array<string, string> $headers
     * @return array<string, string>
     */
    public function setHeaders(array $headers): array
    {
        return [
            'Accept' => $headers['Accept'] ?? Constants::APPLICATION_TYPE,
            'Authorization' => 'Bearer ' . ($headers['Authorization'] ?? $this->token),
            'X-GitHub-Api-Version' => $headers['X-GitHub-Api-Version'] ?? Constants::API_VERSION,
        ];
    }

    /**
     * Get a response from GitHub API. Base url is already provided, so you only need to provide the route and any additional headers or query parameters.
     *
     * @param string $route
     * @param array<string, string> $headers
     * @param array<string, string|int> $queryParams
     *
     * @return array|null
     * @throws ConnectionException
     */
    public function getResponse(string $route = '', array $queryParams = [], array $headers = []): ?array
    {
        $response = Http::withHeaders(
            $this->setHeaders($headers)
        )->get(Constants::BASE_URL . $route, $queryParams);

        if ($response->failed()) {
            throw new ConnectionException(
                "Failed to connect to the Github API: {$response->status()} - {$response->body()}"
            );
        }

        $newCollection = new Collection($response->json());

        return $newCollection->isEmpty() ? null : $newCollection->toArray();
    }

    /**
     * Get a paginated response from GitHub API.
     *
     * @param string $route
     * @param array<string, string> $headers
     * @param array<string, string> $queryParams
     *
     * @return Collection
     * @throws ConnectionException
     */
    public function getPaginatedResponse(string $route = '', array $queryParams = [],  array $headers = []): Collection
    {
        $results = new Collection;
        $page = 1;

        do {
            $queryParams['page'] = $page;
            $response = $this->getResponse($route, $queryParams, $headers);

            if (empty($response)) {
                break;
            }

            $results = $results->merge($response);
            $page++;
        } while (count($response) > 0);

        return $results;
    }
}
