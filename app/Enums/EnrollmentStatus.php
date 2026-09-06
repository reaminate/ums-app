<?php

namespace App\Enums;

enum EnrollmentStatus: string
{
    case ENROLLED = 'enrolled';
    case WITHDRAWN = 'withdrawn';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
