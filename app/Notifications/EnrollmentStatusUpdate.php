<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EnrollmentStatusUpdate extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Student $student, public CourseOffering $course_offering, public string $status)
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
        return ['database', 'mail'];
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $student_info = $this->student;
        $course = Course::findOrFail($this->course_offering->course_id);
        $url = url('/student/'.$this->student->id);
        return (new MailMessage)
            ->salutation('Assalaamu Alaikum')
            ->greeting('Hello')
            ->line("Your enrollment status in $course->name has been $this->status")
            ->action('You may view your information here', $url)
            ->line('kind regards')
            ->from('studentsupport@gmu.com');          
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'student' => $this->student->name,
            'course' => $this->course_offering->course->name,
            'status' => $this->status
        ];
    }
}
