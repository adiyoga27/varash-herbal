<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\User;
use App\Notifications\NewSubscriberNotification;
use App\Notifications\WelcomeSubscriberNotification;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:150'],
            'name' => ['nullable', 'string', 'max:100'],
            'source' => ['nullable', 'string', 'max:50'],
        ], ['email.required' => 'Email wajib diisi.', 'email.email' => 'Format email tidak valid.']);

        $email = strtolower($data['email']);
        $subscriber = Subscriber::where('email', $email)->first();

        if ($subscriber?->status === 'active') {
            $message = 'Email Anda sudah terdaftar sebagai subscriber. Terima kasih! 💚';
        } else {
            if ($subscriber) {
                $subscriber->update(['status' => 'active', 'unsubscribed_at' => null]);
            } else {
                $subscriber = Subscriber::create([
                    'email' => $email,
                    'name' => $data['name'] ?? null,
                    'source' => $data['source'] ?? 'website',
                    'ip_address' => $request->ip(),
                ]);
            }

            $subscriber->notify(new WelcomeSubscriberNotification);
            Notification::send(User::where('is_active', true)->get(), new NewSubscriberNotification($subscriber));
            ActivityLogger::log('subscribed', "Subscriber baru: {$email}", $subscriber, [], 'newsletter', 'success');

            $message = 'Berhasil berlangganan! Cek email Anda untuk info & promo terbaru. 🌿';
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function unsubscribeForm(string $token)
    {
        $subscriber = Subscriber::where('token', $token)->firstOrFail();

        return view('pages.unsubscribe', compact('subscriber'));
    }

    public function unsubscribe(string $token)
    {
        $subscriber = Subscriber::where('token', $token)->firstOrFail();

        if ($subscriber->status === 'active') {
            $subscriber->update(['status' => 'unsubscribed', 'unsubscribed_at' => now()]);
            ActivityLogger::log('unsubscribed', "Subscriber berhenti berlangganan: {$subscriber->email}", $subscriber, [], 'newsletter', 'warning');
        }

        return view('pages.unsubscribe', ['subscriber' => $subscriber, 'done' => true]);
    }
}
