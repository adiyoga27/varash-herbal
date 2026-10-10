<?php

namespace App\Notifications;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewSubscriberNotification extends Notification
{
    use Queueable;

    public function __construct(public Subscriber $subscriber) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'subscriber',
            'icon' => 'mail',
            'title' => 'Subscriber baru',
            'message' => $this->subscriber->email,
            'url' => route('admin.subscribers.index'),
        ];
    }
}
