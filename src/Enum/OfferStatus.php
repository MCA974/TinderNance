<?php

namespace App\Enum;

enum OfferStatus: string
{
    case DRAFT = 'BROUILLON';
    case ACTIVE = 'ACTIF';
    case PAUSED = 'EN_PAUSE';
    case CLOSED = 'FERME';
}