<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\UserStatus;
use App\UserType;

class RegisterUserAction
{
    public function execute(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'user_type_id' => UserType::from($data['user_type_id'])->value,
            'status' => UserStatus::PENDING->value,
        ]);
    }
}
