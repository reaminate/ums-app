<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAssignmentPublished extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Assignment $assignment)
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
            'course' => $this->assignment->courseOffering->course->name,
            'title' => $this->assignment->__get('title'),
            'description' => $this->assignment->__get('description'),
            'due_date' => $this->assignment->__get('due_date'),
            'max_marks' => $this->assignment->__get('max_marks'),
        ];
    }
}
