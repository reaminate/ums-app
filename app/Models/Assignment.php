<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
#[Fillable('course_offering_id', 'title', 'description', 'due_date', 'max_marks', 'file_path', 'original_name', 'mime_type', 'status')]
class Assignment extends Model
{
    /** @use HasFactory<\Database\Factories\AssignmentFactory> */
    use HasFactory;
    public function courseOffering():BelongsTo
    {
        return $this->belongsTo(CourseOffering::class, 'course_offering_id');
    }
    public function assignmentSubmissions():HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'assignment_id');
    }
}
