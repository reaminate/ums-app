<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
#[Fillable(['name', 'code', 'description', 'department_id', 'credit_value', 'course_level', 'status'])]
#[Hidden('code')]
class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory;
    public function academic_programs(): BelongsToMany
    {
        return $this->belongsToMany(Academic_program::class, 'course_program')->using(Course_program::class);
    }
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    //for the prerequisite courses needed
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisite', 'course_id', 'prerequisite_id')
            ->using(Course_prerequisite::class);
    }
    //for the courses that require this course to be learnt first
    public function prerequisiteFor(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisite', 'prerequisite_id', 'course_id')
            ->using(Course_prerequisite::class);
    }
}
