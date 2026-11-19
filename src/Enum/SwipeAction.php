<?php

namespace App\Enum;

enum SwipeAction: string
{
    case LIKE = 'LIKE';
    case DISLIKE = 'DISLIKE';
    case SUPER_LIKE = 'SUPER_LIKE';
}