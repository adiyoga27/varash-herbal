<?php

namespace App\Notifications;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeSubscriberNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(Subscriber $notifiable): MailMessage
    {
        $site = setting('site_name', config('app.name'));

        return (new MailMessage)
            ->subject("Selamat datang di newsletter {$site} 🌿")
            ->greeting('Halo'.($notifiable->name ? ' '.$notifiable->name : '').'!')
            ->line("Terima kasih telah berlangganan newsletter {$site}.")
            ->line('Anda akan menerima tips hidup sehat alami, info produk terbaru, dan promo spesial langsung di email Anda.')
            ->action('Lihat Produk Kami', route('products.index'))
            ->line('Tidak ingin menerima email lagi? [Berhenti berlangganan]('.route('newsletter.unsubscribe', $notifiable->token).')');
    }
}
