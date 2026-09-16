<?php

namespace App\Enums;

enum CourseOfferingStatus: string
{
    case OPEN = 'open';
    case CLOSED = 'closed';
    case ONGOING = 'ongoing';
}
