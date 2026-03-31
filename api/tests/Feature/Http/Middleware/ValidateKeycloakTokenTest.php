<?php

use App\Services\KeycloakTokenValidator;
use App\Exceptions\TokenException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

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
        $validator = Mockery::mock(KeycloakTokenValidator::class);
        $validator->shouldReceive('validate')
            ->once()
            ->andThrow(new TokenException('Invalid token'));

        app()->instance(KeycloakTokenValidator::class, $validator);

        $response = $this->withHeaders(['Authorization' => 'Bearer invalid-token'])->getJson('/api/hello');

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
                'message' => 'Invalid token',
            ]);
    });

});
