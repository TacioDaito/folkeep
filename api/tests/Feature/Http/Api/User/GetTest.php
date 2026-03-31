<?php

use App\Models\User;
use App\Services\KeycloakTokenValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createValidator($payload): void
{
    $validator = Mockery::mock(KeycloakTokenValidator::class);
    $validator->shouldReceive('validate')
        ->once()
        ->andReturn($payload);

    app()->instance(KeycloakTokenValidator::class, $validator);
}

describe('GET /api/user', function () {

    afterEach(fn() => Mockery::close());

    it('returns user data when valid JWT token is provided', function () {
        $user = User::factory()->create();

        createValidator((object)['sub' => $user->keycloak_id]);

        $response = $this->withHeaders(['Authorization' => 'Bearer valid-token'])->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonFragment(['keycloak_id' => $user->keycloak_id])
            ->assertJsonFragment(['name' => $user->name])
            ->assertJsonFragment(['email' => $user->email])
            ->assertJsonPath('data.id', (string) $user->id);
    });

    it('returns 404 when user is not found', function () {
        createValidator((object) ['sub' => fake()->uuid()]);

        $response = $this->withHeaders(['Authorization' => 'Bearer valid-token'])->getJson('/api/user');

        $response->assertStatus(404);
    });

    it('returns application/vnd.api+json for successful responses', function () {
        $user = User::factory()->create();

        createValidator((object) ['sub' => $user->keycloak_id]);

        $response = $this->withHeaders(['Authorization' => 'Bearer valid-token'])->getJson('/api/user');

        $response->assertHeaderContains('Content-Type', 'application/vnd.api+json');
    });

    it('returns application/json for error responses', function () {
        $response = $this->getJson('/api/user');

        $response->assertHeaderContains('Content-Type', 'application/json');
    });

});
