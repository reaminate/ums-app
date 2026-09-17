<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAssignmentPublished extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Assignment $assignment, public Student $student)
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
        $student = $this->student;
        $course = Course::findOrFail($this->assignment->courseOffering->course_id);
        $url = url('/student/'.$student->id);
        return (new MailMessage)
            ->salutation('Assalaamu Alaikum')
            ->greeting('Hello')
            ->line("new assignment has been made in the course $course->code")
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
            'course' => $this->assignment->courseOffering->course->name,
            'title' => $this->assignment->__get('title'),
            'description' => $this->assignment->__get('description'),
            'due_date' => $this->assignment->__get('due_date'),
            'max_marks' => $this->assignment->__get('max_marks'),
        ];
    }
}
