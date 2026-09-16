<?php

namespace App\Listeners;

use App\Enums\LecturerStatus;
use App\Enums\StudentStatus;
use App\Enums\UserType;
use App\Events\NewUserCreated;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\User;
use App\Notifications\LecturerCreated;
use App\Notifications\StudentCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateModelForUser
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewUserCreated $event): void
    {
        $user = $event->user;
        switch($user['type']){
            case UserType::STUDENT->value:
                $student = Student::create([
                    'user_id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'program_id' => $user['program_id'],
                    'status' => StudentStatus::ENROLLED->value,
                ]);
                $student_user = User::find($student->user_id);
                $student->user->notify(new StudentCreated($student_user, $student));
            break;
            case UserType::LECTURER->value:
                $lecturer = Lecturer::create([
                    'user_id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'department_id'=>$user['department_id'],
                    'status' => LecturerStatus::AVAILABLE->value,
                ]);
                $lecturer_user = User::find($lecturer->user_id);
                $lecturer->user->notify(new LecturerCreated($lecturer_user, $lecturer));
            break;
        }


    }
}
