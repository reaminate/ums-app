<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case ONTIME = 'on time';
    case LATE = 'not on time';
    case NOTSUBMITTED = 'never submitted';

    public function weight(): float
    {
        return match($this){
            self::ONTIME => 1.0,
            self::LATE => 0.8,
            self::NOTSUBMITTED => 0.0,
        };
    }
}
