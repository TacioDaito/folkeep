<?php

use App\Mcp\Servers\RouteMcpServer;
use Laravel\Mcp\Facades\Mcp;

// Register the Route Management and Discovery MCP server
Mcp::web('/mcp/routes', RouteMcpServer::class);
