<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
#[Fillable(['student_id', 'course_offering_id', 'total_assignment_score', 'total_test_marks', 'grade_score'])]
class Grade extends Model
{
    /** @use HasFactory<\Database\Factories\GradeFactory> */
    use HasFactory, SoftDeletes;
    public function student(): BelongsTo 
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    public function courseOffering(): BelongsTo 
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }
    
    
}
