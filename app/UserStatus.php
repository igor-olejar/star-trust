<?php

namespace App;

enum UserStatus: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case BLOCKED = 'blocked';
    case REJECTED = 'rejected';
    case VERIFIED = 'verified';

    // 1. account created = pending
    // 2. email verified = verified
    // 3. admin approves = active
    // 4. admin blocks = blocked
    // 5. admin rejects = rejected

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'pending',
            self::ACTIVE => 'active',
            self::BLOCKED => 'blocked',
            self::REJECTED => 'rejected',
            self::VERIFIED => 'verified',
        };
    }

    public function colorClasses(): string
    {
        return match($this) {
            self::PENDING => 'bg-amber-100 text-amber-700',
            self::VERIFIED => 'bg-blue-100 text-blue-700',
            self::ACTIVE => 'bg-emerald-100 text-emerald-700',
            self::BLOCKED => 'bg-red-100 text-red-700',
            self::REJECTED => 'bg-red-100 text-red-700',
        };
    }
}
