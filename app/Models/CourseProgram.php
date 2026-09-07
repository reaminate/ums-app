<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseProgram extends Pivot
{
    protected $table = 'course_program';

    public $timestamps = false;
}
