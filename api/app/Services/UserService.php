<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use App\Contracts\UserServiceInterface;
use App\Models\User;

/**
 * UserService is responsible for handling user-related business logic.
 *
 * @param string $sub
 * @return Model
 */
class UserService implements UserServiceInterface
{
    public function getUser(string $sub): Model
    {
        return User::where('keycloak_id', $sub)->firstOrFail();
    }
}
