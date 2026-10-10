<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification
{
    use Queueable;

    public function __construct(public ContactMessage $contact) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'message',
            'icon' => 'chat',
            'title' => 'Pesan baru dari '.$this->contact->name,
            'message' => str($this->contact->message)->limit(60)->toString(),
            'url' => route('admin.messages.show', $this->contact),
        ];
    }
}
