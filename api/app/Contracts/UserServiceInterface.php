<?php

namespace App\Contracts;

interface UserServiceInterface
{
    public function getUser(string $sub): \Illuminate\Database\Eloquent\Model;
}
