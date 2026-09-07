<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
#[Fillable(['course_offering_id', 'day', 'start_time', 'end_time','room_number'])]
class ClassSchedule extends Model
{
    /** @use HasFactory<\Database\Factories\ClassScheduleFactory> */
    use HasFactory;
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'class_schedule_id');
    }
    public function courseOffering(): BelongsTo
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }
    
}
