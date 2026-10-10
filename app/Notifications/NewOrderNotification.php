<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'order',
            'icon' => 'cart',
            'title' => 'Pesanan baru '.$this->order->code,
            'message' => $this->order->customer_name.' — '.rupiah($this->order->total),
            'url' => route('admin.orders.show', $this->order),
        ];
    }
}
