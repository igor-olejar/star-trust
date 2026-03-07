<?php

namespace App;

enum UserType: int
{
    case VENUE = 1;
    case ARTIST = 2;
    case PROMOTER = 3;

    public function label(): string
    {
        return match ($this) {
            self::VENUE => 'Venue',
            self::ARTIST => 'Artist',
            self::PROMOTER => 'Promoter',
        };
    }
}
