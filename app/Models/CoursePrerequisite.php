<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CoursePrerequisite extends Pivot
{
    /** @use HasFactory<\Database\Factories\CoursePrerequisiteFactory> */
    use HasFactory;

    protected $table = 'course_prerequisite';

    public $timestamps = false;
}
