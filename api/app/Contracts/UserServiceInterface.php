<?php

namespace App\Contracts;

/**
 * UserServiceInterface defines the contract for user-related business logic, such as retrieving user information.
 */
interface UserServiceInterface
{
    /**
     * Retrieve a user by their Keycloak subject identifier.
     *
     * @param string $sub The Keycloak subject identifier (sub claim from JWT).
     * @return \Illuminate\Database\Eloquent\Model The User model instance corresponding to the given sub.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If no user is found with the given sub.
     */
    public function getUser(string $sub): \Illuminate\Database\Eloquent\Model;
}
