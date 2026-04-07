<?php

use App\Models\User;

/**
* Test suite for the GET /api/user endpoint, which returns the authenticated user's information.
* This endpoint requires a valid JWT token in the Authorization header and returns user data
* in JSON API format. The tests cover successful retrieval of user data, handling of not found
* users, and correct content types for responses. It uses the MockAuthValidator trait to mock
* the token validation process and simulate different scenarios.
*/
describe('GET /api/user', function () {

    afterEach(fn() => Mockery::close());

    it('returns user data when valid JWT token is provided', function () {
        $user = User::factory()->create();

        $this->createValidator((object)['sub' => $user->keycloak_id]);

        $response = $this->withHeaders(['Authorization' => 'Bearer valid-token'])->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'users',
                    'id' => (string) $user->id,
                    'attributes' => [
                        'keycloak_id' => $user->keycloak_id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                ],
            ]);
    });

    it('returns 404 when user is not found', function () {
        $this->createValidator((object) ['sub' => fake()->uuid()]);

        $response = $this->withHeaders(['Authorization' => 'Bearer valid-token'])->getJson('/api/user');

        $response->assertStatus(404);
    });

    it('returns application/vnd.api+json for successful responses', function () {
        $user = User::factory()->create();

        $this->createValidator((object) ['sub' => $user->keycloak_id]);

        $response = $this->withHeaders(['Authorization' => 'Bearer valid-token'])->getJson('/api/user');

        $response->assertHeaderContains('Content-Type', 'application/vnd.api+json');
    });

    it('returns application/json for error responses', function () {
        $response = $this->getJson('/api/user');

        $response->assertHeaderContains('Content-Type', 'application/json');
    });

});
