<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'type' => UserType::ADMIN->value,
            'password' => bcrypt('password'),
        ]);
        User::factory(100)->create(['type' => UserType::LECTURER->value]);
        User::factory(200)->create(['type' => UserType::STUDENT->value]);
        $this->call([
            FacultySeeder::class,
            DepartmentSeeder::class,
            AcademicProgramSeeder::class,
            CourseSeeder::class,
            AcademicSemesterSeeder::class,
            StudentSeeder::class,
            LecturerSeeder::class,
            CourseOfferingSeeder::class,
            EnrollmentSeeder::class,
            ClassScheduleSeeder::class,
            AttendanceSeeder::class,
            AssignmentSeeder::class,
            AssignmentSubmissionSeeder::class,
            AssignmentMarkSeeder::class,
            ExamSeeder::class,
            ExamMarkSeeder::class,
            GradeSeeder::class,
        ]);
    }
}
