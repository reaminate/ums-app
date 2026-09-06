<?php

namespace App\Enums;

enum AssignmentStatus: string
{
    case HIDDEN = 'hidden';
    case SHOWN = 'available for students';
    case FINISHED = 'due date has reached';
}
