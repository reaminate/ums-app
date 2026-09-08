<?php

namespace Database\Seeders;

use App\Models\AcademicSemester;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcademicSemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AcademicSemester::factory(9)->create();
    }
}
