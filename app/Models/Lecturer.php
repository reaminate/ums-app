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
class staff extends Model
{
    /** @use HasFactory<\Database\Factories\staffFactory> */
    use HasFactory, SoftDeletes;
    public function assignmentMarks(): HasMany
    {
        return $this->hasMany(AssignmentMark::class, 'staff_id');
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
        return $this->hasMany(CourseOffering::class, 'staff_id');
    }
    protected static function booted():void
    {
        static::created(function($model){
            $staff_id_number =(int) round(((($model->id + 576.57)*162.30987)-10)/30.3);

            $model->staff_number = "L0.$staff_id_number";
        });
    }
}
