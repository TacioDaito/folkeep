<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Server\Tool;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class GetRouteDetails extends Tool
{
    protected string $name = 'get_route_details';

    protected string $title = 'Get Route Details';

    protected string $description = 'Get detailed metadata for a specific route by URI or name, including middleware, controller method, and parameter requirements.';

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'uri' => [
                    'type' => 'string',
                    'description' => 'The URI of the route to inspect (e.g., /api/users)',
                ],
                'name' => [
                    'type' => 'string',
                    'description' => 'The name of the route to inspect (e.g., users.index)',
                ],
            ],
            'oneOf' => [
                ['required' => ['uri']],
                ['required' => ['name']],
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
                'found' => [
                    'type' => 'boolean',
                    'description' => 'Whether the route was found',
                ],
                'route' => [
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
                                    'constraints' => ['type' => 'object'],
                                ],
                            ],
                            'description' => 'Route parameters with their constraints',
                        ],
                        'defaults' => [
                            'type' => 'object',
                            'description' => 'Default parameter values',
                        ],
                        'wheres' => [
                            'type' => 'object',
                            'description' => 'Parameter validation constraints',
                        ],
                    ],
                    'required' => ['methods', 'uri', 'action'],
                ],
                'error' => [
                    'type' => 'string',
                    'description' => 'Error message if route not found',
                ],
            ],
            'required' => ['found'],
        ];
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    public function handle(array $arguments): array
    {
        $uri = $arguments['uri'] ?? null;
        $name = $arguments['name'] ?? null;

        if (!$uri && !$name) {
            return [
                'found' => false,
                'error' => 'Either "uri" or "name" parameter must be provided',
            ];
        }

        // Get all routes from Laravel's route collection
        $routes = Route::getRoutes();

        $route = null;

        if ($uri) {
            // Find route by URI
            $route = $routes->getByName($uri) ?: $this->findRouteByUri($routes, $uri);
        } else {
            // Find route by name
            $route = $routes->getByName($name);
        }

        if (!$route) {
            return [
                'found' => false,
                'error' => $uri
                    ? "No route found with URI: {$uri}"
                    : "No route found with name: {$name}",
            ];
        }

        return [
            'found' => true,
            'route' => $this->extractDetailedRouteMetadata($route),
        ];
    }

    /**
     * Find route by URI pattern (since getByName might not work for URIs)
     *
     * @param  \Illuminate\Routing\RouteCollection  $routes
     * @param  string  $uri
     * @return \Illuminate\Routing\Route|null
     */
    protected function findRouteByUri($routes, string $uri): ?object
    {
        foreach ($routes as $route) {
            if ($route->uri() === $uri) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Extract detailed metadata from a Laravel route
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return array<string, mixed>
     */
    protected function extractDetailedRouteMetadata($route): array
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
        $wheres = $route->wheres ?? [];

        foreach ($uriParameters as $param) {
            $parameters[] = [
                'name' => $param,
                'required' => true,
                'default' => null,
                'constraints' => $wheres[$param] ?? null,
            ];
        }

        // Get default values
        $defaults = $route->defaults ?? [];

        return [
            'methods' => $methods,
            'uri' => $uri,
            'name' => $name ?? null,
            'action' => $action,
            'middleware' => $middleware,
            'domain' => $domain ?? null,
            'namespace' => $namespace ?? null,
            'parameters' => $parameters,
            'defaults' => $defaults,
            'wheres' => $wheres,
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
