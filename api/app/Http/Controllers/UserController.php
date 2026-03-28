<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function getUser(UserRequest $request)
    {
        return response()->json($this->userService->getUser($request));
    }

}
