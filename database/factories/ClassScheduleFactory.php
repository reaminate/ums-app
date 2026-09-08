<?php

namespace Database\Factories;

use App\Enums\DaysOfTheWeek;
use App\Models\ClassSchedule;
use App\Models\CourseOffering;
use Illuminate\Database\Eloquent\Factories\Factory;
use RuntimeException;

/**
 * @extends Factory<ClassSchedule>
 */
class ClassScheduleFactory extends Factory
{
    public const TIME_SLOTS = [
        ['08:00:00', '09:30:00'],
        ['09:45:00', '11:15:00'],
        ['11:30:00', '13:00:00'],
        ['14:00:00', '15:30:00'],
        ['15:45:00', '17:15:00'],
        ['17:30:00', '19:00:00'],
    ];

    public const ROOMS = [
        'Room 101', 'Room 102', 'Room 103', 'Room 104',
        'Room 201', 'Room 202', 'Room 203', 'Room 204',
        'Lab A', 'Lab B', 'Lab C',
        'Lecture Hall 1', 'Lecture Hall 2',
    ];

    /**
     * Lecturer|day|start_time slots already handed out during this seeding run.
     *
     * @var array<string, true>
     */
    protected static array $lecturerSlots = [];

    /**
     * Room|day|start_time slots already handed out during this seeding run.
     *
     * @var array<string, true>
     */
    protected static array $roomSlots = [];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $courseOfferings = CourseOffering::pluck('lecturer_id', 'id');
        $courseOfferingId = $this->faker->randomElement($courseOfferings->keys()->all());
        $lecturerId = $courseOfferings[$courseOfferingId];

        [$day, $startTime, $endTime, $roomNumber] = $this->findAvailableSlot($lecturerId);

        return [
            'course_offering_id' => $courseOfferingId,
            'day' => $day,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'room_number' => $roomNumber,
        ];
    }

    /**
     * 
     *
     * @return array{0: DaysOfTheWeek, 1: string, 2: string, 3: string}
     */
    protected function findAvailableSlot(int $lecturerId): array
    {
        $days = DaysOfTheWeek::cases();

        for ($attempt = 0; $attempt < 500; $attempt++) {
            $day = $this->faker->randomElement($days);
            [$start, $end] = $this->faker->randomElement(self::TIME_SLOTS);
            $room = $this->faker->randomElement(self::ROOMS);

            $lecturerKey = "{$lecturerId}|{$day->value}|{$start}";
            $roomKey = "{$room}|{$day->value}|{$start}";

            if (isset(self::$lecturerSlots[$lecturerKey]) || isset(self::$roomSlots[$roomKey])) {
                continue;
            }

            self::$lecturerSlots[$lecturerKey] = true;
            self::$roomSlots[$roomKey] = true;

            return [$day, $start, $end, $room];
        }

        throw new RuntimeException('Unable to find a free class schedule slot (lecturer/room grid exhausted) after 500 attempts.');
    }
    //call before running the seed
    public static function resetBookings(): void
    {
        self::$lecturerSlots = [];
        self::$roomSlots = [];
    }
}
