<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
#[Fillable(['name', 'code', 'department_id', 'qualification_level', 'duration', 'required_credits', 'status'])]
#[Hidden('code')]
class Academic_program extends Model
{
    /** @use HasFactory<\Database\Factories\AcademicProgramFactory> */
    use HasFactory;
    public function department():BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function courses():BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_program')->using(Course_program::class);
    }
}
