<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
#[Fillable(['user_id', 'staff_number', 'name', 'email', 'department_id', 'status'])]
#[Hidden('user_id')]
class Lecturer extends Model
{
    /** @use HasFactory<\Database\Factories\LecturerFactory> */
    use HasFactory, SoftDeletes;
    public function assignmentMarks(): HasMany
    {
        return $this->hasMany(AssignmentMark::class, 'lecturer_id');
    }
    public function user(): BelongsTo 
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function department() :BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function courseOfferings(): HasMany
    {
        return $this->hasMany(CourseOffering::class, 'lecturer_id');
    }
}
