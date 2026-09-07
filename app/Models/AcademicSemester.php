<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
#[Fillable('name', 'year', 'start_date', 'end_date', 'registration_start_date', 'registration_end_date', 'status')]

class AcademicSemester extends Model
{
    /** @use HasFactory<\Database\Factories\AcademicSemesterFactory> */
    use HasFactory;
    public function courseOfferings():HasMany
    {
        return $this->hasMany(CourseOffering::class, 'semester_id');
    }
}
