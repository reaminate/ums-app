<?php

namespace App\Notifications;

use App\Models\ExamMark;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamResultPublished extends Notification
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
        return ['database'];
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
