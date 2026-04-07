<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\TokenException;
use App\Services\KeycloakTokenValidator;

/**
 * Middleware to validate Keycloak JWT tokens on incoming requests.
 * If valid, the decoded payload is attached to the request attributes.
 * If invalid, a 401 Unauthorized response is returned with an error message.
 */
class ValidateKeycloakToken
{
    public function __construct(
        private KeycloakTokenValidator $validator
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization', '');

        if (! str_starts_with($authHeader, 'Bearer ')) {
            return $this->unauthorized('Missing or malformed Authorization header.');
        }

        $token = substr($authHeader, 7);

        try {
            $payload = $this->validator->validate($token);
            $request->attributes->set('jwt_payload', $payload);
        } catch (TokenException $e) {
            return $this->unauthorized($e->getMessage());
        }

        return $next($request);
    }

    /**
     * Helper to return a standardized 401 Unauthorized response with a message.
     *
     * @param string $message
     * @return Response
     */
    private function unauthorized(string $message): Response
    {
        return response()->json(['error' => 'Unauthorized', 'message' => $message], 401);
    }
}
