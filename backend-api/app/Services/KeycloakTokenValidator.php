<?php
namespace App\Services;

use App\Exceptions\TokenException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use stdClass;

/**
 * Service to validate Keycloak JWT tokens.
 * Uses the JwksCache to retrieve public keys for signature verification.
 * Validates standard claims like issuer and audience.
 * Throws TokenException with appropriate messages on validation failure.
 */
class KeycloakTokenValidator
{
    public function __construct(private JwksCache $jwksCache)
    {}

    /**
     * Validate the token and return its decoded payload.
     *
     * @throws TokenException
     * @return stdClass
     */
    public function validate(string $token): stdClass
    {
        $header = $this->decodeHeader($token);
        $kid    = $header->kid ?? null;

        $keys = $this->jwksCache->getKeys();

        // If the kid isn't in cache, do a single refresh in case keys rotated
        if ($kid && ! isset($keys['kid'])) {
            $keys = $this->jwksCache->refresh();
        }

        if (empty($keys)) {
            throw new TokenException('No JWKS keys available.', 401);
        }

        try {
            // Reconstruct the JWKS format with kid field included in each key
            $keySet = ['keys' => array_values($keys)];
            $parsedKeys = JWK::parseKeySet($keySet);
            
            $payload = JWT::decode($token, $parsedKeys);
        } catch (ExpiredException $e) {
            throw new TokenException('Token has expired.', 401);
        } catch (SignatureInvalidException $e) {
            throw new TokenException('Token signature is invalid.', 401);
        } catch (BeforeValidException $e) {
            throw new TokenException('Token is not yet valid.', 401);
        } catch (\Exception $e) {
            throw new TokenException('Token validation failed: ' . $e->getMessage(), 401);
        }

        $this->validateClaims($payload);

        return $payload;
    }

    /**
     * Validate standard claims like issuer and audience.
     *
     * @throws TokenException
     */
    private function validateClaims(stdClass $payload): void
    {
        $expectedIssuer   = config('keycloak.issuer');
        $trustedAudiences = config('keycloak.trusted_audiences', []);

        if (($payload->iss ?? null) !== $expectedIssuer) {
            throw new TokenException('Token issuer is invalid.', 401);
        }

        // In SPA scenarios, the token's aud/azp will be the frontend client ID,
        // not the API's client ID. We validate against trusted audiences instead.
        if (! empty($trustedAudiences)) {
            $aud = $payload->aud ?? null;
            $azp = $payload->azp ?? null;

            $audiences = is_array($aud) ? $aud : (is_string($aud) ? [$aud] : []);

            $tokenRelevant = in_array($azp, $trustedAudiences, true)
                || count(array_intersect($audiences, $trustedAudiences)) > 0;

            if (! $tokenRelevant) {
                throw new TokenException('Token audience is not trusted.', 401);
            }
        }
    }

    /**
     * Decode the JWT header to extract the kid.
     *
     * @throws TokenException
     * @return stdClass
     */
    private function decodeHeader(string $token): stdClass
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new TokenException('Malformed JWT.', 401);
        }

        $headerJson = base64_decode(strtr($parts[0], '-_', '+/'), true);

        if ($headerJson === false) {
            throw new TokenException('Failed to decode JWT header.', 401);
        }

        return json_decode($headerJson) ?: new \stdClass();
    }
}
