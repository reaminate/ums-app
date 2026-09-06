<?php

namespace App\Enums;

enum LecturerStatus: string
{
    case TEACHING ='currently teaching one course';
    case AVAILABLE = 'currently not teaching a course';
    case ONLEAVE = 'currently on leave';
}
