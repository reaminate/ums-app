<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamMarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Exam::with('courseOffering.students')->get()->each(function (Exam $exam) {
            $exam->courseOffering->students->each(function (Student $student) use ($exam) {
                ExamMark::factory()->create([
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                ]);
            });
        });
    }
}
