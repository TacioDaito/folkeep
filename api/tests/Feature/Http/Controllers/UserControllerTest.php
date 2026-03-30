<?php

use App\Models\User;
use App\Services\KeycloakTokenValidator;
use App\Exceptions\TokenException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('GET /api/user', function () {
    it('returns 401 when no Authorization header is provided', function () {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
                'message' => 'Missing or malformed Authorization header.',
            ]);
    });

    it('returns 401 when Authorization header is malformed', function () {
        $response = $this->withHeaders([
            'Authorization' => 'InvalidToken',
        ])->getJson('/api/user');

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
                'message' => 'Missing or malformed Authorization header.',
            ]);
    });

    it('returns 401 when JWT token is invalid', function () {
        $validator = Mockery::mock(KeycloakTokenValidator::class);
        $validator->shouldReceive('validate')
            ->once()
            ->andThrow(new TokenException('Invalid token'));

        app()->instance(KeycloakTokenValidator::class, $validator);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token',
        ])->getJson('/api/user');

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
                'message' => 'Invalid token',
            ]);
    });

    it('returns user data when valid JWT token is provided', function () {
        $keycloakId = '88888888-4444-4444-4444-121212121212';
        $user = User::factory()->create([
            'keycloak_id' => $keycloakId,
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $payload = (object) ['sub' => $keycloakId];

        $validator = Mockery::mock(KeycloakTokenValidator::class);
        $validator->shouldReceive('validate')
            ->once()
            ->andReturn($payload);

        app()->instance(KeycloakTokenValidator::class, $validator);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer valid-token',
        ])->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'attributes',
                ],
            ]);
    });

    it('returns 404 when user is not found', function () {
        $keycloakId = '88888888-4444-4444-4444-121212121212';

        $payload = (object) ['sub' => $keycloakId];

        $validator = Mockery::mock(KeycloakTokenValidator::class);
        $validator->shouldReceive('validate')
            ->once()
            ->andReturn($payload);

        app()->instance(KeycloakTokenValidator::class, $validator);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer valid-token',
        ])->getJson('/api/user');

        $response->assertStatus(404);
    });
});
