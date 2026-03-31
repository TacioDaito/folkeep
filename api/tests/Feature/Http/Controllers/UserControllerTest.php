<?php

use App\Models\User;
use App\Services\KeycloakTokenValidator;
use App\Exceptions\TokenException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('GET /api/user', function () {

    afterEach(fn() => Mockery::close());

    it('returns 401 when no Authorization header is provided', function () {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
                'message' => 'Missing or malformed Authorization header.',
            ]);
    });

    it('returns 401 when Authorization header is malformed', function ($malformedHeader) {
        $response = $this->withHeaders([
            'Authorization' => $malformedHeader,
        ])->getJson('/api/user');

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
                'message' => 'Missing or malformed Authorization header.',
            ]);
    })
    ->with(['Token', 'Bearer', 'Bearer ', '']);

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
            ->assertJsonFragment(['keycloak_id' => $user->keycloak_id])
            ->assertJsonFragment(['name' => $user->name])
            ->assertJsonFragment(['email' => $user->email])
            ->assertJsonPath('$.data.id', (string) $user->id);
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
