<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use App\Contracts\UserServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

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
        return Cache::remember("user:{$sub}", now()->addMinutes(10), function () use ($sub) {
            return User::where('keycloak_id', $sub)->select(['id', 'name', 'email', 'keycloak_id'])->firstOrFail();
        });
    }
}
