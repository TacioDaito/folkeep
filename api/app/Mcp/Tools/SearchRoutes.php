<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Server\Tool;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SearchRoutes extends Tool
{
    protected string $name = 'search_routes';

    protected string $title = 'Search Routes';

    protected string $description = 'Filters the route list based on a keyword in the URI or name. Returns matching routes with their metadata.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'keyword' => [
                    'type' => 'string',
                    'description' => 'Keyword to search for in route URI or name',
                ],
                'limit' => [
                    'type' => 'integer',
                    'minimum' => 1,
                    'maximum' => 100,
                    'description' => 'Maximum number of results to return (optional, default: 20)',
                ],
            ],
            'required' => ['keyword'],
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
                'keyword' => [
                    'type' => 'string',
                    'description' => 'The search keyword used',
                ],
                'total' => [
                    'type' => 'integer',
                    'description' => 'Total number of matching routes',
                ],
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
                'search_in' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'description' => 'Fields that were searched (uri, name)',
                ],
            ],
            'required' => ['keyword', 'total', 'routes', 'search_in'],
        ];
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    public function handle(array $arguments): array
    {
        $keyword = $arguments['keyword'];
        $limit = $arguments['limit'] ?? 20;

        // Validate inputs
        if (empty(trim($keyword))) {
            return [
                'keyword' => $keyword,
                'total' => 0,
                'routes' => [],
                'search_in' => ['uri', 'name'],
                'error' => 'Keyword cannot be empty',
            ];
        }

        // Validate limit
        $limit = min((int) $limit, 100);

        // Get all routes from Laravel's route collection
        $routes = Route::getRoutes();

        // Convert to array and filter by keyword
        $routeData = [];
        foreach ($routes as $route) {
            $routeInfo = $this->extractRouteMetadata($route);

            // Check if keyword matches in URI or name (case-insensitive)
            $uriMatch = Str::contains(Str::lower($routeInfo['uri']), Str::lower($keyword));
            $nameMatch = $routeInfo['name'] && Str::contains(Str::lower($routeInfo['name']), Str::lower($keyword));

            if ($uriMatch || $nameMatch) {
                $routeData[] = $routeInfo;
            }
        }

        // Sort by URI for consistent ordering
        usort($routeData, fn($a, $b) => strcmp($a['uri'], $b['uri']));

        // Apply limit
        $total = count($routeData);
        $limitedRoutes = array_slice($routeData, 0, $limit);

        return [
            'keyword' => $keyword,
            'total' => $total,
            'routes' => $limitedRoutes,
            'search_in' => ['uri', 'name'],
        ];
    }

    /**
     * Extract metadata from a Laravel route (reusing from ListRoutes)
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
