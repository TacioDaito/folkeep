<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\UserServiceInterface;

class UserController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService
    ) {}

    public function getUser(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->userService->getUser($request->attributes->get('jwt_payload')?->sub));
    }

}
