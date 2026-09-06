<?php

namespace App\Enums;

enum AcademicStatus: string
{
    case GOOD_STANDING = 'meet the requirement';
    case ACADEMIC_PROBATION = 'doesnt meet the requirement';
    case ACADEMIC_DISMISSAL = 'removed from the program';

}
