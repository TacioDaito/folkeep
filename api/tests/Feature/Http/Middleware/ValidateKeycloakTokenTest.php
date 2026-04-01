<?php

use App\Exceptions\TokenException;

/**
* Test suite for the ValidateKeycloakToken middleware, which is responsible for validating JWT tokens on incoming requests.
* The tests cover scenarios such as missing or malformed Authorization headers, invalid tokens, and successful validation.
* The MockAuthValidator trait is used to allow the simulation of different validation outcomes without needing to rely
* on actual token generation. Each test asserts that the middleware returns the appropriate HTTP
* status codes and error messages based on the validation results.
*/
describe('JWT validation middleware', function () {

    afterEach(fn() => Mockery::close());

    it('returns 401 when no Authorization header is provided', function () {
        $response = $this->getJson('/api/hello');

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
                'message' => 'Missing or malformed Authorization header.',
            ]);
    });

    it('returns 401 when Authorization header is malformed', function ($malformedHeader, $message) {
        $response = $this->withHeaders(['Authorization' => $malformedHeader,])->getJson('/api/hello');

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
                'message' => $message,
            ]);
    })
    ->with([
        ['Token', 'Missing or malformed Authorization header.'],
        ['Bearer', 'Missing or malformed Authorization header.'],
        ['Bearer ', 'Malformed JWT.'],
        ['', 'Missing or malformed Authorization header.']
    ]);

    it('returns 401 when JWT token is invalid', function () {
        $this->createExceptionThrowingValidator(new TokenException('Invalid token'));

        $response = $this->withHeaders(['Authorization' => 'Bearer invalid-token'])->getJson('/api/hello');

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
                'message' => 'Invalid token',
            ]);
    });

    it('returns 200 and attaches payload when JWT token is valid', function () {
        $payload = (object) ['sub' => fake()->uuid()];
        $this->createValidator($payload);

        $response = $this->withHeaders(['Authorization' => 'Bearer valid-token'])->getJson('/api/hello');

        $response->assertStatus(200);

        $this->assertEquals($payload, request()->attributes->get('jwt_payload'));
    });

});
