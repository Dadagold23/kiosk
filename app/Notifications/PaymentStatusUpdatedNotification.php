<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentStatusUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $reference,
        public string $status
    ) {
    }

    public function via(object $notifiable): array
    {
        return app()->environment('local')
            ? ['database']
            : ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment Status Updated')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your payment status has been updated.')
            ->line('Reference: ' . $this->reference)
            ->line('Status: ' . ucfirst($this->status));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment',
            'title' => 'Payment Status Updated',
            'message' => 'Payment ' . $this->reference . ' is now ' . $this->status . '.',
            'reference' => $this->reference,
        ];
    }
}
