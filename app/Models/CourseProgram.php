<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseProgram extends Pivot
{
    /** @use HasFactory<\Database\Factories\CourseProgramFactory> */
    use HasFactory;

    protected $table = 'course_program';

    public $timestamps = false;
}
