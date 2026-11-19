<?php

namespace App\Enum;

enum UserRole: string
{
    case STUDENT = 'STUDENT';
    case COMPANY = 'COMPANY';
    case ADMIN = 'ADMIN';
}