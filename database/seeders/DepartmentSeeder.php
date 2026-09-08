<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faculties = Faculty::all('id');
        foreach($faculties as $faculty){
            Department::factory(3)->create(['faculty_id'=>$faculty]);
        }
        
    }
}
