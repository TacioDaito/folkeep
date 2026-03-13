<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Server\Tool;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ListRoutes extends Tool
{
    protected string $name = 'list_routes';

    protected string $title = 'List All Routes';

    protected string $description = 'Returns a list of all registered routes in the Laravel application with their metadata including method, URI, name, action, and middleware.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'page' => [
                    'type' => 'integer',
                    'minimum' => 1,
                    'description' => 'Page number for pagination (optional)',
                ],
                'limit' => [
                    'type' => 'integer',
                    'minimum' => 1,
                    'maximum' => $this->server->maxPaginationLength,
                    'description' => 'Number of routes to return per page (optional, default: 20)',
                ],
            ],
            'additionalProperties' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function outputSchema(JsonSchema $schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'routes' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'methods' => [
                                'type' => 'array',
                                'items' => ['type' => 'string'],
                                'description' => 'HTTP methods supported by this route',
                            ],
                            'uri' => [
                                'type' => 'string',
                                'description' => 'Route URI pattern',
                            ],
                            'name' => [
                                'type' => 'string',
                                'description' => 'Route name (if defined)',
                            ],
                            'action' => [
                                'type' => 'string',
                                'description' => 'Controller action or closure',
                            ],
                            'middleware' => [
                                'type' => 'array',
                                'items' => ['type' => 'string'],
                                'description' => 'Middleware applied to this route',
                            ],
                            'domain' => [
                                'type' => 'string',
                                'description' => 'Domain constraint (if any)',
                            ],
                            'namespace' => [
                                'type' => 'string',
                                'description' => 'Controller namespace (if any)',
                            ],
                            'parameters' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'name' => ['type' => 'string'],
                                        'required' => ['type' => 'boolean'],
                                        'default' => ['type' => ['string', 'null']],
                                    ],
                                ],
                                'description' => 'Route parameters with their constraints',
                            ],
                        ],
                        'required' => ['methods', 'uri', 'action'],
                    ],
                ],
                'total' => [
                    'type' => 'integer',
                    'description' => 'Total number of routes',
                ],
                'page' => [
                    'type' => 'integer',
                    'description' => 'Current page number',
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Number of routes per page',
                ],
                'has_more' => [
                    'type' => 'boolean',
                    'description' => 'Whether there are more pages available',
                ],
            ],
            'required' => ['routes', 'total', 'page', 'limit', 'has_more'],
        ];
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    public function handle(array $arguments): array
    {
        $page = $arguments['page'] ?? 1;
        $limit = $arguments['limit'] ?? 20;

        // Validate limit
        $limit = min((int) $limit, 100);
        $page = max(1, (int) $page);

        // Get all routes from Laravel's route collection
        $routes = Route::getRoutes();

        // Convert to array and extract metadata
        $routeData = [];
        foreach ($routes as $route) {
            $routeData[] = $this->extractRouteMetadata($route);
        }

        // Sort by URI for consistent ordering
        usort($routeData, fn($a, $b) => strcmp($a['uri'], $b['uri']));

        // Apply pagination
        $total = count($routeData);
        $offset = ($page - 1) * $limit;
        $paginatedRoutes = array_slice($routeData, $offset, $limit);

        return [
            'routes' => $paginatedRoutes,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'has_more' => $offset + $limit < $total,
        ];
    }

    /**
     * Extract comprehensive metadata from a Laravel route
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return array<string, mixed>
     */
    protected function extractRouteMetadata($route): array
    {
        $methods = $route->methods();
        $uri = $route->uri();
        $name = $route->getName();
        $action = $route->getActionName();
        $middleware = $route->gatherMiddleware();

        // Extract domain constraint
        $domain = $route->domain();

        // Extract namespace from action
        $namespace = null;
        if (is_string($action) && Str::contains($action, '@')) {
            $controller = explode('@', $action)[0];
            $namespace = Str::beforeLast($controller, '\\');
        }

        // Extract parameters with their constraints
        $parameters = [];
        $uriParameters = $this->extractUriParameters($uri);

        foreach ($uriParameters as $param) {
            $parameters[] = [
                'name' => $param,
                'required' => true, // For simplicity, assuming all URI parameters are required
                'default' => null,
            ];
        }

        return [
            'methods' => $methods,
            'uri' => $uri,
            'name' => $name ?? null,
            'action' => $action,
            'middleware' => $middleware,
            'domain' => $domain ?? null,
            'namespace' => $namespace ?? null,
            'parameters' => $parameters,
        ];
    }

    /**
     * Extract parameter names from URI pattern
     *
     * @param  string  $uri
     * @return array<int, string>
     */
    protected function extractUriParameters(string $uri): array
    {
        preg_match_all('/\{([^}]+)\}/', $uri, $matches);
        return $matches[1] ?? [];
    }
}
