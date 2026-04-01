<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Contracts\UserServiceInterface;

/**
 * UserController handles HTTP requests related to user information.
 */
class UserController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService
    ) {}

    /**
     * Handle the incoming request to get the authenticated user's information.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getUser(Request $request): JsonResponse
    {
        $user = $this->userService->getUser($request->attributes->get('jwt_payload')?->sub ?? '');
        return $user->toResource()->response();
    }

}
