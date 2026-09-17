<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Models\AssignmentMark;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentGraded extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public AssignmentMark $assignmentMark)
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
        $student = Student::findOrFail($this->assignmentMark->assignmentSubmission->student_id);
        $assignment =  Assignment::findOrFail($this->assignmentMark->assignmentSubmission->assignment_id);
        $course = Course::findOrFail($assignment->courseOffering->course_id);
        $assignment_mark = $this->assignmentMark->marks;
        $percent = $assignment_mark/$assignment->max_marks;
        $url = url('/student/'.$student->id);
        return (new MailMessage)
            ->salutation('Assalaamu Alaikum')
            ->greeting('Hello')
            ->line("The assignment $assignment->title from the course $course->code has been marked")
            ->line("You got $assignment_mark")
            ->lineIf($percent>0.5, 'congratulations !!')
            ->lineIf($percent<0.5, 'better luck next time')
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
            'course' => $this->assignmentMark->assignmentSubmission->assignment->courseOffering->course->name,
            'title' => $this->assignmentMark->assignmentSubmission->assignment->title,
            'mark' => $this->assignmentMark->__get('marks'),
            'comments' => $this->assignmentMark->__get('comments'),
        ];
    }
}
