<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseOffering;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseOfferingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = Course::all('id');
        foreach($courses as $course){
            CourseOffering::factory(10)->create(['course_id'=>$course]);
        }
        
    }
}
