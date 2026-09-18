<?php

namespace App\Notifications;

use App\Models\Department;
use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LecturerCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $user, public Lecturer $lecturer)
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $lecturer_info = $this->lecturer;
        $department_info = Department::findOrFail($this->lecturer->department_id);
        $url = url('/lecturer/'.$this->lecturer->id);
        return (new MailMessage)
            ->salutation('Assalaamu Alaikum')
            ->greeting('Hello')
            ->line("We at this univeristy, congratulate you for joining us")
            ->line("lectruer number: $lecturer_info->staff_number")
            ->line("Department: $department_info->name")
            ->line("Email: $lecturer_info->email")
            ->line("Password: password123 (We highly encourage you to change this at your convinience)")
            ->action('You may view your information here', $url)
            ->line('kind regards')
            ->from('lecturersupport@gmu.com');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->lecturer->id,
            'user_id' => $this->user->id,
            'lecturer_number' => $this->lecturer->staff_number,
            'name' => $this->lecturer->name,
            'email' => $this->lecturer->email
        ];
    }
}
