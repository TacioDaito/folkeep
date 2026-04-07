<?php

namespace Tests\Traits;

use Mockery;
use App\Services\KeycloakTokenValidator;

/**
 * Trait to provide a helper method for mocking the KeycloakTokenValidator in tests.
 * This allows us to easily set up expected payloads for token validation without
 * needing to mock the validator in every test case.
 */
trait MockAuthValidator
{
    /**
     * Create a mock KeycloakTokenValidator that returns a specified payload when validate() is called.
     *
     * @param \stdClass $payload The payload to return when the validate method is called.
     *
     */
    public function createValidator(\stdClass $payload): void
    {
        $validator = Mockery::mock(KeycloakTokenValidator::class);

        $validator->shouldReceive('validate')->once()->andReturn($payload);

        app()->instance(KeycloakTokenValidator::class, $validator);
    }

    /**
    * Create a mock KeycloakTokenValidator that throws a specified exception when validate() is called.
    *
    * @param \Exception $exception The exception to throw when the validate method is called.
    *
    */
    public function createExceptionThrowingValidator(\Exception $exception): void
    {
        $validator = Mockery::mock(KeycloakTokenValidator::class);

        $validator->shouldReceive('validate')->once()->andThrow($exception);

        app()->instance(KeycloakTokenValidator::class, $validator);
    }

}
