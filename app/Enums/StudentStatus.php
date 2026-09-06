<?php

namespace App\Enums;

enum StudentStatus: string
{
    case ENROLLED = 'currently enrolled in a program';
    case NOTENROLLED = 'currently not enrolled';
    case PROBATION = 'currently on probations';
    case ALLUMNI = 'finished a program';
}
