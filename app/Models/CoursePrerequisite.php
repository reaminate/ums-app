<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CoursePrerequisite extends Pivot
{
    protected $table = 'course_prerequisite';

    public $timestamps = false;
}
