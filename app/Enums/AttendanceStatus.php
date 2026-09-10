<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case VIABLE = 'viable for finals';
    case BARELY_VIABLE = 'barely viable for finals';
    case NOTVIABLE = 'not viable for finals';

}
