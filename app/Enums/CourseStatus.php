<?php

namespace App\Enums;

enum CourseStatus: string
{
    case OFFERED = 'offered';
    case ONGOING = 'ongoing';
    case NOTOFFERED = 'not offered';
}
