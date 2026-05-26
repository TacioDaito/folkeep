<?php

return [

    // The JWKS URI for fetching public keys to validate JWT tokens
    'jwks_uri'       => env('KEYCLOAK_JWKS_URL'),

    // The issuer URL for validating the "iss" claim in JWT tokens
    'issuer'         => env('KEYCLOAK_ISSUER'),

    // Cache TTL for JWKS keys in seconds (default: 3600 seconds = 1 hour)
    'jwks_cache_ttl' => env('KEYCLOAK_JWKS_CACHE_TTL', 3600),

    // List of trusted client IDs that can access this API
    // In SPA scenarios, add your frontend's client ID here
    // Example: 'folkeep-frontend,folkeep-api,another-trusted-client'
    'trusted_audiences' => array_filter(
        array_map('trim', explode(',', env('KEYCLOAK_TRUSTED_AUDIENCES', '')))
    ),
];