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
        $validator = $this->mock(KeycloakTokenValidator::class, function ($mock) {
            $mock->shouldReceive('validate')
                ->once()
                ->andThrow(new TokenException('Invalid token'));
        });

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
        $keycloakId = 'test-keycloak-id-123';
        $user = User::factory()->create([
            'keycloak_id' => $keycloakId,
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $payload = (object) ['sub' => $keycloakId];

        $validator = $this->mock(KeycloakTokenValidator::class, function ($mock) use ($payload) {
            $mock->shouldReceive('validate')
                ->once()
                ->andReturn($payload);
        });

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
        $keycloakId = 'non-existent-keycloak-id';

        $payload = (object) ['sub' => $keycloakId];

        $validator = $this->mock(KeycloakTokenValidator::class, function ($mock) use ($payload) {
            $mock->shouldReceive('validate')
                ->once()
                ->andReturn($payload);
        });

        $response = $this->withHeaders([
            'Authorization' => 'Bearer valid-token',
        ])->getJson('/api/user');

        $response->assertStatus(404);
    });
});
