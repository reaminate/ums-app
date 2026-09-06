<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
#[Fillable(['name', 'faculty_id'])]
class Department extends Model
{
    /** @use HasFactory<\Database\Factories\DepartmentFactory> */
    use HasFactory;
    public function faculty():BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }
    
    public function academic_programs():HasMany
    {
        return $this->hasMany(Academic_program::class, 'department_id');
    }
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'department_id');
    }
}
