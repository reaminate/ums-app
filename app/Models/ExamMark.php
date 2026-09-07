<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
#[Fillable(['exam_id', 'student_id', 'marks'])]
class ExamMark extends Model
{
    /** @use HasFactory<\Database\Factories\ExamMarkFactory> */
    use HasFactory, SoftDeletes;
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
