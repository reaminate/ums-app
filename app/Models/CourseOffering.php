<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
#[Fillable(['course_id', 'semester_id', 'lecturer_id', 'max_students', 'status', 'start_date', 'end_date'])]

class CourseOffering extends Model
{
    /** @use HasFactory<\Database\Factories\CourseOfferingFactory> */
    use HasFactory;
    public function course():BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
    public function semester():BelongsTo
    {
        return $this->belongsTo(AcademicSemester::class, 'semester_id');
    }
    public function lecturer():BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }
    public function students():BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'enrollment')->using(Enrollment::class)
            ->withPivot('status', 'enrolled_at', 'withdrawn_at');
    }
    public function classSchedules():HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'course_offering_id');
    }
    public function assignments():HasMany
    {
        return $this->hasMany(Assignment::class, 'course_offering_id');
    }
    public function exams():HasMany
    {
        return $this->hasMany(Exam::class, 'course_offering_id');
    }
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'course_offering_id');
    }
    
}
