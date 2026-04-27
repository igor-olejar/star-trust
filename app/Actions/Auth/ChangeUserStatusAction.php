<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Models\UserStatusChange;
use App\UserStatus;

class ChangeUserStatusAction
{
    public static function execute(User $user, UserStatus $newStatus, ?int $adminId = null): void
    {
        $oldStatus = $user->status;

        // Log the status change
        UserStatusChange::create([
            'user_id' => $user->id,
            'admin_id' => $adminId,
            'from_status' => $oldStatus->value,
            'to_status' => $newStatus->value,
        ]);

        // Update the user's status
        $user->update(['status' => $newStatus->value]);
    }
}
