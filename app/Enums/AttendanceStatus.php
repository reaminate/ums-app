<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case PRESENT = 'present';
    case ABSENT = 'absent';
    case LATE = 'late';
    case EXCUSED = 'excused';

    public function weight(): float
    {
        return match ($this) {
            self::PRESENT => 1.0,
            self::ABSENT => 0.0,
            self::LATE => 0.5,
            self::EXCUSED => 1.0,
        };
    }
}
