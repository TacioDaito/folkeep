<?php

namespace App\Contracts;

interface UserServiceInterface
{
    public function getUser(string $sub): \App\Models\User;
}
