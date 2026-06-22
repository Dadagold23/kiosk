<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order)
    {
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
            ->subject('New Order Submitted')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your order has been submitted successfully.')
            ->line('Order Number: ' . $this->order->order_no)
            ->line('Total: ₦' . number_format($this->order->total, 2))
            ->action('View Order', route('orders.show', $this->order->order_no))
            ->line('Thank you for using Kiosk.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order',
            'title' => 'New Order Submitted',
            'message' => 'Order ' . $this->order->order_no . ' was created successfully.',
            'order_no' => $this->order->order_no,
        ];
    }
}
