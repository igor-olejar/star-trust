<?php

namespace App;

enum UserStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case BLOCKED = 'blocked';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'pending',
            self::ACTIVE => 'active',
            self::BLOCKED => 'blocked',
            self::REJECTED => 'rejected',
        };
    }
}
