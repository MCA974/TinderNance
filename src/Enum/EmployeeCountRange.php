<?php

namespace App\Enum;

enum EmployeeCountRange: string
{
    case RANGE_1_10 = '1-10';
    case RANGE_11_49 = '11-49';
    case RANGE_50_249 = '50-249';
    case RANGE_250_PLUS = '250+';
}