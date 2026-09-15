<?php

namespace Database\Seeders;

use App\Models\AcademicProgram;
use App\Models\Course;
use App\Models\CourseProgram;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = AcademicProgram::all('id');
        foreach($programs as $program){
            $courseIds = Course::inRandomOrder()->limit(10)->pluck('id');
            foreach ($courseIds as $courseId) {
                CourseProgram::factory()->create([
                    'academic_program_id' => $program->id,
                    'course_id' => $courseId,
                ]);
            }
        }
    }
}
