<?php

namespace App;

enum VotingCategory: int
{
    case PUNCTUALITY = 1;
    case PROFESSIONALISM = 2;
    case STAGE_MANNER = 3;
    case TECH_AND_SOUND = 4;
    case COMMUNICATION = 5;
    case SAFETYl_SECURITY = 6;
    case PROMO_EFFORTS = 7;
    case ORGANISATION = 8;

    public function label(): string
    {
        return match ($this) {
            self::PUNCTUALITY => 'Punctuality',
            self::PROFESSIONALISM => 'Professionalism',
            self::STAGE_MANNER => 'Stage Manner',
            self::TECH_AND_SOUND => 'Tech and Sound',
            self::COMMUNICATION => 'Communication',
            self::SAFETYl_SECURITY => 'Safety & Security',
            self::PROMO_EFFORTS => 'Promo Efforts',
            self::ORGANISATION => 'Organisation',
        };
    }
}
