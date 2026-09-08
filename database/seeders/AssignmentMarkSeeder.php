<?php

namespace Database\Seeders;

use App\Models\AssignmentMark;
use App\Models\AssignmentSubmission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignmentMarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assignment_submissions = AssignmentSubmission::all('id');
        foreach($assignment_submissions as $assignment_submission){
            AssignmentMark::factory()->create([
                'assignment_submission_id' => $assignment_submission,
            ]);
        }
    }
}
