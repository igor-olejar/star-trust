<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\UserStatus;

class ChangeUserStatusAction
{
    public static function execute(User $user, UserStatus $newStatus): void
    {
        $user->update(['status' => $newStatus->value]);
    }
}
