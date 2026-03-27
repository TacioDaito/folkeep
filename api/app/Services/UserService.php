<?php

namespace App\Services;

use App\Http\Requests\UserRequest;
use App\Models\User;

class UserService implements UserServiceInterface
{
    public function getUser(UserRequest $request)
    {
        return User::where('keycloak_id', request()->attributes->get('jwt_payload')->sub)->firstOrFail();
    }
}
