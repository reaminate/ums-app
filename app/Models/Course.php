<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
#[Fillable(['name', 'code', 'description', 'department_id', 'credit_value', 'course_level', 'status'])]
#[Hidden('code')]
class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory;
    public function academicPrograms(): BelongsToMany
    {
        return $this->belongsToMany(AcademicProgram::class, 'course_program')->using(CourseProgram::class);
    }
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class, 'course_id');
    }
    //for the prerequisite courses needed
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisite', 'course_id', 'prerequisite_id')
            ->using(CoursePrerequisite::class);
    }
    //for the courses that require this course to be learnt first
    public function prerequisiteFor(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisite', 'prerequisite_id', 'course_id')
            ->using(CoursePrerequisite::class);
    }
}
