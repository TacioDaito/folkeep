<?php

namespace App\Contracts;
use App\Http\Requests\UserRequest;

interface UserContract
{
    public function getUser(UserRequest $request);
}