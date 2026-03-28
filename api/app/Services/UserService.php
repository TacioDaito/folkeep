<?php

namespace App\Services;

use App\Http\Requests\UserRequest;
use App\Contracts\UserContract;
use App\Models\User;

class UserService implements UserContract
{
    public function getUser(UserRequest $request)
    {
        ds(request()->attributes->get('jwt_payload'));
        return User::where('keycloak_id', request()->attributes->get('jwt_payload')->sub)->firstOrFail();
    }
}
