<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamMark;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamResultPublished extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ExamMark $examMark)
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
        $student = Student::findOrFail($this->examMark->student_id);
        $exam= Exam::findOrFail($this->examMark->exam_id);
        $course = Course::findOrFail($exam->courseOffering->course_id);
        $exam_mark = $this->examMark;
        $url = url('/student/'.$student->id);
        return (new MailMessage)
            ->salutation('Assalaamu Alaikum')
            ->greeting('Hello')
            ->line("The marks for $exam->exam_type for the course $course->name have been published")
            ->line("You got $exam_mark->marks")
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
            'exam' => $this->examMark->exam->courseOffering->course->name,
            'mark' => $this->examMark->__get('marks'),
        ];
    }
}
