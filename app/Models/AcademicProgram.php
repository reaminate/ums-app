<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
#[Fillable(['name', 'code', 'department_id', 'qualification_level', 'duration', 'required_credits', 'status'])]
#[Hidden('code')]
class AcademicProgram extends Model
{
    /** @use HasFactory<\Database\Factories\AcademicProgramFactory> */
    use HasFactory;
    public function department():BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function courses():BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_program')->using(CourseProgram::class);
    }
    public function students(): HasMany 
    {
        return $this->hasMany(Student::class, 'program_id');
    }
    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->code)) {
                $model->code = static::generateCode($model->name);
            }
        });
    }

    public static function generateCode(string $name): string
    {
        $qualificationLetters = [
            'bachelor' => 'B',
            'bachelors' => 'B',
            'diploma' => 'D',
            'master' => 'M',
            'masters' => 'M',
            'phd' => 'P',
            'doctorate' => 'P',
        ];

        $ignoredWords = ['in', 'of', 'and', 'the'];

        $words = array_values(array_filter(preg_split('/\s+/', trim($name))));

        if (empty($words)) {
            return '';
        }

        $qualification = strtolower(array_shift($words));
        $code = $qualificationLetters[$qualification] ?? strtoupper(substr($qualification, 0, 1));

        foreach ($words as $word) {
            if (in_array(strtolower($word), $ignoredWords, true)) {
                continue;
            }
            $code .= strtoupper(substr($word, 0, 1));
        }

        $baseCode = $code;
        for ($suffix = 1; static::where('code', $code)->exists(); $suffix++) {
            $code = $baseCode.$suffix;
        }

        return $code;
    }
}
