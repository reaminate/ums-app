<?php

namespace App\Notifications;

use App\Models\ClassSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClassHasChanged extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ClassSchedule $class_schedule)
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
        $course_name = $this->class_schedule->courseOffering->course->name;
        $class_schedule = $this->class_schedule;
        return (new MailMessage)
            ->greeting('Attention')
            ->line("The schedule for the course $course_name has changed")
            ->line("Day: $class_schedule->day")
            ->line("Start time: $class_schedule->start_time")
            ->line("End Time: $class_schedule->end_time")
            ->line("Room: $class_schedule->room_number");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->class_schedule->id,
            'course_offering_id' => $this->class_schedule->course_offering_id,
            'day' => $this->class_schedule->day,
            'start_time' => $this->class_schedule->start_time,
            'end_time' => $this->class_schedule->end_time,
            'room_number' => $this->class_schedule->room_number,
        ];
    }
}
