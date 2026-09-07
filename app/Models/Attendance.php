<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
#[Fillable('student_id', 'class_schedule_id', 'status', 'recorded_at')]
class Attendance extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceFactory> */
    use HasFactory;
    public function student(): BelongsTo 
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    public function classSchedule():BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class, 'class_schedule_id');
    }
    
}
