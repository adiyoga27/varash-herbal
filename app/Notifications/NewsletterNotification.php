<?php

namespace App\Notifications;

use App\Models\Newsletter;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewsletterNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Newsletter $newsletter) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(Subscriber $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->newsletter->subject)
            ->markdown('emails.newsletter', [
                'newsletter' => $this->newsletter,
                'unsubscribeUrl' => route('newsletter.unsubscribe', $notifiable->token),
            ]);
    }
}
