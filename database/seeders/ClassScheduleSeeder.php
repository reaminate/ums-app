<?php

namespace Database\Seeders;

use App\Models\ClassSchedule;
use Database\Factories\ClassScheduleFactory;
use Illuminate\Database\Seeder;

class ClassScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClassScheduleFactory::resetBookings();

        ClassSchedule::factory(100)->create();
    }
}
