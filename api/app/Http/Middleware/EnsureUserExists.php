<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

/**
 * Middleware that ensures a local User record exists for the Keycloak-authenticated user.
 * Must be run after ValidateKeycloakToken middleware (which sets jwt_payload).
 * If no User record exists for the JWT sub claim, one is created automatically.
 */
class EnsureUserExists
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $payload = $request->attributes->get('jwt_payload');

        if (! $payload) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Missing JWT payload. Ensure authentication middleware runs first.',
            ], 401);
        }

        $sub = $payload->sub;

        $user = User::firstOrCreate(
            ['keycloak_id' => $sub],
            [
                'name' => $payload->preferred_username ?? $sub,
                'email' => $payload->email ?? null,
                'password' => null,
            ]
        );

        $request->attributes->set('user', $user);

        return $next($request);
    }
}
