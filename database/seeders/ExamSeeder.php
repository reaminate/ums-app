<?php

namespace Database\Seeders;

use App\Models\CourseOffering;
use App\Models\Exam;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $course_offerings = CourseOffering::all('id');
        foreach($course_offerings as $course_offering){
            Exam::factory()->create(['course_offering_id'=>$course_offering]);
        }
    }
}
