<?php

namespace App\Enum;

enum SchoolStatus: string
{
    case ENROLLED = 'INSCRIT';         // Déjà inscrit dans une école
    case SEARCHING = 'RECHERCHE';       // En recherche d'école
    case ADMITTED = 'ADMIT';         // Admis sous réserve de contrat
}