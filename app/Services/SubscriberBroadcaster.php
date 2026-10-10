<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Newsletter;
use App\Models\Product;
use App\Models\Subscriber;
use App\Notifications\NewsletterNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

/** Mengirim newsletter / notifikasi konten baru ke seluruh subscriber aktif (via queue). */
class SubscriberBroadcaster
{
    public static function send(Newsletter $newsletter): int
    {
        $count = 0;

        Subscriber::active()->chunkById(200, function ($subscribers) use ($newsletter, &$count) {
            Notification::send($subscribers, new NewsletterNotification($newsletter));
            $count += $subscribers->count();
        });

        $newsletter->update(['status' => 'sent', 'sent_at' => now(), 'recipients_count' => $count]);
        ActivityLogger::log('sent', "Newsletter \"{$newsletter->subject}\" dikirim ke {$count} subscriber", $newsletter, ['recipients' => $count], 'newsletter', 'success');

        return $count;
    }

    public static function newProduct(Product $product): int
    {
        $body = "Kami baru saja meluncurkan **{$product->name}**!\n\n"
            .($product->short_description ? $product->short_description."\n\n" : '')
            .'Harga: **'.rupiah($product->finalPrice())."**\n\n"
            .'[Lihat produk →]('.route('products.show', $product).')';

        return self::send(Newsletter::create([
            'user_id' => Auth::id(),
            'subject' => "Produk Baru: {$product->name} 🌿",
            'body' => $body,
        ]));
    }

    public static function newArticle(Article $article): int
    {
        $body = "## {$article->title}\n\n"
            .($article->excerpt ?: str(strip_tags($article->body))->limit(250))
            ."\n\n[Baca selengkapnya →](".route('articles.show', $article).')';

        return self::send(Newsletter::create([
            'user_id' => Auth::id(),
            'subject' => "Artikel Baru: {$article->title}",
            'body' => $body,
        ]));
    }
}
