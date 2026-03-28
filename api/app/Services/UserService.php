<?php

namespace App\Services;

use App\Contracts\UserServiceInterface;
use App\Models\User;

class UserService implements UserServiceInterface
{
    public function getUser(string $sub): User
    {
        return User::where('keycloak_id', $sub)->firstOrFail();
    }
}
