<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
#[Fillable(['user_id', 'student_number', 'name', 'email', 'program_id', 'enrollment_year', 'status'])]
#[Hidden('user_id')]
class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory, SoftDeletes;
    public function assignmentSubmissions():HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'student_id');
    }
    public function attendances():HasMany
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }
    public function examMarks(): HasMany
    {
        return $this->hasMany(ExamMark::class, 'student_id');
    }
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'student_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function academicProgram():BelongsTo
    {
        return $this->belongsTo(AcademicProgram::class, 'program_id');
    }
    public function courseOfferings(): BelongsToMany
    {
        return $this->belongsToMany(CourseOffering::class, 'enrollment')->using(Enrollment::class)
            ->withPivot('status', 'enrolled_at', 'withdrawn_at');
    }
    protected static function booted():void
    {
        static::created(function($model){
            $student_id_number =(int) round(((($model->id + 480.57)*160.30987)-26)/56.3);

            $model->student_number = "S0.$student_id_number";
        });
    }
}
