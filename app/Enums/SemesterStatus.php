<?php

namespace App\Enums;

enum SemesterStatus: string
{
    case REGISTRATION_CLOSED = 'registration is closed';
    case REGISTRATION_OPEN = 'registration is open';
    case ONGOING = 'semester is on going';
    case FINISHED = 'semester finished';
}
