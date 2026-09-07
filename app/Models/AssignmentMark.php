<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
#[Fillable(['assignment_submission_id', 'marks', 'comments', 'marked_at','lecturer_id'])]
class AssignmentMark extends Model
{
    /** @use HasFactory<\Database\Factories\AssignmentMarkFactory> */
    use HasFactory, SoftDeletes;
    public function lecturer():BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }
    public function assignmentSubmission():BelongsTo
    {
        return $this->belongsTo(AssignmentSubmission::class, 'assignment_submission_id');
    }
}
