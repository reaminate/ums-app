<?php

namespace App\Notifications;

use App\Models\AcademicProgram;
use App\Models\Student;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $user, public Student $student)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $student_info = $this->student;
        $academic_program = AcademicProgram::findOrFail($this->student->program_id);
        $url = url('/student/'.$this->student->id);
        return (new MailMessage)
            ->greeting('Hello')
            ->line("We at this univeristy, congratulate you for enrolling in $academic_program->name")
            ->line("Student number: $student_info->student_number")
            ->line("Email: $student_info->email")
            ->line("Password: password123 (We highly encourage you to change this at your convinience)")
            ->action('You may view your information here', $url)
            ->line('Hope you enjoy your stay here');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->student->id,
            'user_id' => $this->user->id,
            'student_number' => $this->student->student_number,
            'name' => $this->student->name,
            'email' => $this->student->email
        ];
    }
}
