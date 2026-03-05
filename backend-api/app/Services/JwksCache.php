<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class JwksCache
{
    private string $jwksUri;
    private int $ttl;
    private string $cacheKey = 'keycloak_jwks';

    public function __construct()
    {
        $this->jwksUri = config('keycloak.jwks_uri');
        $this->ttl     = (int) config('keycloak.jwks_cache_ttl', 3600);
    }

    /**
     * Returns an associative array of kid => JWK (as array).
     *
     * @return array<string, array>
     */
    public function getKeys(): array
    {
        return Cache::remember($this->cacheKey, $this->ttl, function () {
            return $this->fetchKeys();
        });
    }

    /**
     * Force a cache refresh (e.g. after a kid-miss).
     *
     * @return array<string, array>
     */
    public function refresh(): array
    {
        Cache::forget($this->cacheKey);
        return $this->getKeys();
    }

    /**
     * Fetches the JWKS from Keycloak and indexes them by kid.
     *
     * @return array<string, array>
     */
    private function fetchKeys(): array
    {
        $response = Http::timeout(5)->get($this->jwksUri);

        if (! $response->successful()) {
            throw new RuntimeException(
                "Failed to fetch JWKS from {$this->jwksUri}: HTTP {$response->status()}"
            );
        }

        $data = $response->json();

        if (empty($data['keys'])) {
            throw new RuntimeException('JWKS response contained no keys.');
        }

        // Index by kid for O(1) lookup
        $indexed = [];
        foreach ($data['keys'] as $key) {
            if (isset($key['kid'])) {
                $indexed[$key['kid']] = $key;
            }
        }

        return $indexed;
    }
}
