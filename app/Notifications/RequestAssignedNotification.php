<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestAssignedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $requestType,
        public string $title,
        public string $route
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(ucfirst($this->requestType) . ' Request Updated')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your ' . $this->requestType . ' request has been updated or assigned.')
            ->line('Reference: ' . $this->title)
            ->action('View Details', $this->route);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->requestType,
            'title' => ucfirst($this->requestType) . ' Request Updated',
            'message' => $this->title . ' has been updated or assigned.',
            'route' => $this->route,
        ];
    }
}
