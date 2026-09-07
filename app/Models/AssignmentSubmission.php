<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
#[Fillable('assignment_id', 'student_id', 'file_path', 'original_name', 'mime_type', 'comments', 'submitted_at', 'status')]
class AssignmentSubmission extends Model
{
    /** @use HasFactory<\Database\Factories\AssignmentSubmissionFactory> */
    use HasFactory, SoftDeletes;
    public function assignmentMark():HasOne
    {
        return $this->hasOne(AssignmentMark::class, 'assignment_submission_id');
    }
    public function student():BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    public function assignment():BelongsTo
    {
        return $this->belongsTo(Assignment::class, 'assignment_id');
    }
}
