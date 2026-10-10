<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\Testimonial;
use App\Models\User;
use App\Notifications\NewContactMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about', [
            'testimonials' => Testimonial::where('is_active', true)->latest()->take(3)->get(),
        ]);
    }

    public function faq()
    {
        return view('pages.faq', ['faqs' => Faq::where('is_active', true)->orderBy('sort_order')->get()]);
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function sendContact(Request $request)
    {
        // Honeypot sederhana untuk menahan bot
        if ($request->filled('website')) {
            return back()->with('success', 'Pesan Anda telah terkirim.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $contact = ContactMessage::create($data + ['ip_address' => $request->ip()]);

        Notification::send(User::where('is_active', true)->get(), new NewContactMessageNotification($contact));

        return back()->with('success', 'Terima kasih! Pesan Anda telah kami terima dan akan segera dibalas.');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }
}
