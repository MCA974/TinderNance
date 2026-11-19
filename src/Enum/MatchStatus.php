<?php

namespace App\Enum;

enum MatchStatus: string
{
    case ACTIVE = 'ACTIVE';
    case ARCHIVED = 'ARCHIVED';
    case UNMATCHED = 'UNMATCHED';
}