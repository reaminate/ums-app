<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CoursePrerequisiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Course::all()->groupBy('department_id')->each(function ($courses) {
            $courses->each(function (Course $course) use ($courses) {
                $eligible = $courses->where('course_level', '<', $course->course_level);

                if ($eligible->isEmpty()) {
                    return;
                }

                $prerequisites = $eligible->random(min(fake()->numberBetween(0, 2), $eligible->count()));

                if ($prerequisites->isEmpty()) {
                    return;
                }

                $course->prerequisites()->attach($prerequisites->pluck('id'));
            });
        });
    }
}
