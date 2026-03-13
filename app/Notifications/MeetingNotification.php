<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $title;
    public $message;
    public $url;
    public $icon;
    public $iconColor;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $url, $icon = 'fa-bell', $iconColor = 'text-primary')
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->icon = $icon;
        $this->iconColor = $iconColor;
    }

    /**
     * Get the notification's delivery channels.
     * Use database for in-app bell. You can add 'mail' later if needed.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Currently sending to database only to power the bell notification.
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject($this->title)
                    ->line($this->message)
                    ->action('Lihat Detail', url($this->url));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'icon' => $this->icon,
            'iconColor' => $this->iconColor,
        ];
    }
}
