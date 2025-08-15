<?php

declare(strict_types=1);

namespace Ssionn\GithubForgeLaravel\Traits;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Ssionn\GithubForgeLaravel\Constants\Constants;
use Ssionn\GithubForgeLaravel\Enums\TokenType;

trait ApiActionTrait
{
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
        } while (count($response));

        return $results;
    }

    /**
     * Build the authorization header for the API request.
     *
     * @param string $token
     *
     * @return string
     */
    public function buildAuthorizationHeader(string $token): string
    {
        foreach (TokenType::cases() as $tokenType) {
            if (str_starts_with($token, $tokenType->value)) {
                return 'token ' . $token;
            }
        }

        return 'Bearer ' . $token;
    }

    /**
     *
     * @param array<string, string> $headers
     * @return array<string, string>
     */
    public function setHeaders(array $headers = []): array
    {
        $tokenOrOverride = $this->token ?? $headers['Authorization'];

        $authHeader = $this->buildAuthorizationHeader($tokenOrOverride);

        return [
            'Accept' => $headers['Accept'] ?? Constants::APPLICATION_TYPE,
            'Authorization' => $authHeader,
            'X-GitHub-Api-Version' => $headers['X-GitHub-Api-Version'] ?? Constants::API_VERSION,
        ];
    }

    /**
     * Get the headers for the API request.
     *
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->setHeaders();
    }
}
