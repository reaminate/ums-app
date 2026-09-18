<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
#[Fillable(['status', 'enrolled_at', 'withdrawn_at'])]
class Enrollment extends Pivot
{
    /** @use HasFactory<\Database\Factories\EnrollmentFactory> */
    use HasFactory;

    protected $table = 'enrollment';

    public $timestamps = false;

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    public function courseOffering(): BelongsTo 
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }
    
}
