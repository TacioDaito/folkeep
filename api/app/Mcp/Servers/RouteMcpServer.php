<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\GetRouteDetails;
use App\Mcp\Tools\ListRoutes;
use App\Mcp\Tools\SearchRoutes;
use Illuminate\Support\Facades\Route;
use Laravel\Mcp\Server;

class RouteMcpServer extends Server
{
    protected string $name = 'Route Management and Discovery';

    protected string $version = '1.0.0';

    protected string $instructions = <<<'MARKDOWN'
        This MCP server provides comprehensive route management and discovery capabilities for the Laravel application.

        Available tools:
        - list_routes: Returns all registered routes with their metadata
        - get_route_details: Get detailed information about a specific route by URI or name
        - search_routes: Search for routes matching a keyword in URI or name

        All routes are retrieved from Laravel's route collection using Route::getRoutes().
    MARKDOWN;

    /**
     * @var array<int, string>
     */
    protected array $supportedProtocolVersion = [
        '2025-11-25',
        '2025-06-18',
        '2025-03-26',
        '2024-11-05',
    ];

    /**
     * @var array<string, array<string, bool>|stdClass|string>
     */
    protected array $capabilities = [
        self::CAPABILITY_TOOLS => [
            'listChanged' => false,
        ],
    ];

    /**
     * @var array<int, class-string>
     */
    protected array $tools = [
        ListRoutes::class,
        GetRouteDetails::class,
        SearchRoutes::class,
    ];

    public int $maxPaginationLength = 100;

    public int $defaultPaginationLength = 20;

    protected function boot(): void
    {
        // Add any server-specific boot logic here
    }
}
