<?php

namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'admin';
    case LECTURER = 'lecturer';
    case STUDENT = 'student';
}
