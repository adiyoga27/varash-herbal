<?php

namespace App\Providers;

use App\Services\ActivityLogger;
use App\Services\CartService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(CartService::class);
    }

    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));
        Paginator::defaultView('partials.pagination');

        View::composer(['layouts.app', 'partials.*'], function ($view) {
            $view->with('cartCount', app(CartService::class)->count());
        });

        $this->registerAuthLogging();
    }

    private function registerAuthLogging(): void
    {
        Event::listen(Login::class, function (Login $event) {
            $user = $event->user;
            $user->forceFill(['last_login_at' => now(), 'last_login_ip' => request()->ip()])->saveQuietly();
            ActivityLogger::log('login', "{$user->name} berhasil login", $user, ['remember' => $event->remember], 'auth', 'success', $user->id);
        });

        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                ActivityLogger::log('logout', "{$event->user->name} logout", $event->user, [], 'auth', 'info', $event->user->id);
            }
        });

        Event::listen(Failed::class, function (Failed $event) {
            $email = $event->credentials['email'] ?? '-';
            ActivityLogger::log('login_failed', "Percobaan login gagal untuk {$email}", $event->user, ['email' => $email], 'auth', 'danger');
        });
    }
}
