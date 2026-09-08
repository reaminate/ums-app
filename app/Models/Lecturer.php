<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
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
    protected static function booted():void
    {
        // staff_number is NOT NULL/unique but its real value is derived from the
        // auto-incremented id, which doesn't exist yet at insert time. Insert a
        // unique placeholder first, then overwrite it with the real value once the
        // id is known.
        static::creating(function($model){
            $model->staff_number ??= (string) Str::ulid();
        });
        static::created(function($model){
            $staff_id_number =(int) round(((($model->id + 576.57)*162.30987)-10)/30.3);

            $model->staff_number = "L0.$staff_id_number";
            $model->saveQuietly();
        });
    }
}
