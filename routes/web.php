<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Website (publik)
|--------------------------------------------------------------------------
*/
Route::get('/', HomeController::class)->name('home');

Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/kategori/{category:slug}', [ProductController::class, 'index'])->name('products.category');

Route::get('/artikel', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/artikel/{article:slug}', [ArticleController::class, 'show'])->name('articles.show');

Route::get('/tentang-kami', [PageController::class, 'about'])->name('about');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/kontak', [PageController::class, 'contact'])->name('contact');
Route::post('/kontak', [PageController::class, 'sendContact'])->middleware('throttle:5,1')->name('contact.send');
Route::get('/kebijakan-privasi', [PageController::class, 'privacy'])->name('privacy');

Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
Route::post('/keranjang', [CartController::class, 'add'])->middleware('throttle:60,1')->name('cart.add');
Route::patch('/keranjang/{productId}', [CartController::class, 'update'])->whereNumber('productId')->name('cart.update');
Route::delete('/keranjang/{productId}', [CartController::class, 'remove'])->whereNumber('productId')->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');
Route::get('/checkout/selesai/{code}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/whatsapp/{code}', [CheckoutController::class, 'whatsapp'])->name('checkout.whatsapp');

Route::get('/lacak-pesanan', [OrderTrackingController::class, 'index'])->name('order.track');
Route::get('/pesanan/{code}', [OrderTrackingController::class, 'show'])->name('order.show');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->middleware('throttle:6,1')->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribeForm'])->name('newsletter.unsubscribe');
Route::post('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe.confirm');

Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots']);

/*
|--------------------------------------------------------------------------
| Panel Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [Admin\AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::resource('products', Admin\ProductController::class)->except('show');
        Route::post('products/{id}/restore', [Admin\ProductController::class, 'restore'])->name('products.restore');
        Route::resource('categories', Admin\CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('articles', Admin\ArticleController::class)->except('show');
        Route::resource('testimonials', Admin\TestimonialController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('faqs', Admin\FaqController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::get('orders/export', [Admin\OrderController::class, 'export'])->name('orders.export');
        Route::resource('orders', Admin\OrderController::class)->only(['index', 'show', 'update', 'destroy']);

        Route::get('subscribers/export', [Admin\SubscriberController::class, 'export'])->name('subscribers.export');
        Route::patch('subscribers/{subscriber}/toggle', [Admin\SubscriberController::class, 'toggle'])->name('subscribers.toggle');
        Route::resource('subscribers', Admin\SubscriberController::class)->only(['index', 'store', 'destroy']);

        Route::post('newsletters/{newsletter}/send', [Admin\NewsletterController::class, 'send'])->name('newsletters.send');
        Route::resource('newsletters', Admin\NewsletterController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

        Route::resource('messages', Admin\MessageController::class)->only(['index', 'show', 'destroy']);

        Route::get('notifications', [Admin\NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/feed', [Admin\NotificationController::class, 'feed'])->name('notifications.feed');
        Route::get('notifications/{id}', [Admin\NotificationController::class, 'read'])->name('notifications.read');
        Route::post('notifications/read-all', [Admin\NotificationController::class, 'readAll'])->name('notifications.readAll');

        Route::get('profile', [Admin\ProfileController::class, 'edit'])->name('profile');
        Route::put('profile', [Admin\ProfileController::class, 'update'])->name('profile.update');

        Route::get('logs', [Admin\ActivityLogController::class, 'index'])->name('logs.index');
        Route::get('logs/export', [Admin\ActivityLogController::class, 'export'])->name('logs.export');
        Route::get('logs/{log}', [Admin\ActivityLogController::class, 'show'])->whereNumber('log')->name('logs.show');

        Route::middleware('admin:superadmin')->group(function () {
            Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings');
            Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
            Route::resource('users', Admin\UserController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::delete('logs/purge', [Admin\ActivityLogController::class, 'purge'])->name('logs.purge');
            Route::get('system-logs', [Admin\SystemLogController::class, 'index'])->name('system-logs');
            Route::post('system-logs/clear', [Admin\SystemLogController::class, 'clear'])->name('system-logs.clear');
        });
    });
});
