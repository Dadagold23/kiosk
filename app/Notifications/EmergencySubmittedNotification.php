<?php

namespace App\Notifications;

use App\Models\EmergencyRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmergencySubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(public EmergencyRequest $emergencyRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'emergency',
            'title' => 'Emergency Alert Submitted',
            'message' => 'Emergency alert for ' . ucfirst(str_replace('_', ' ', $this->emergencyRequest->emergency_type)) . ' has been submitted.',
            'emergency_id' => $this->emergencyRequest->id,
        ];
    }
}
