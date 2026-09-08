<?php

namespace Database\Seeders;

use App\Models\AcademicProgram;
use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcademicProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = Department::all('id');
        foreach($departments as $department){
            AcademicProgram::factory(5)->create(['department_id'=>$department]);
        }
    }
}
