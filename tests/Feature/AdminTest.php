<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Article;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subscriber;
use App\Models\User;
use App\Notifications\NewsletterNotification;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->admin = User::where('role', 'superadmin')->first();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_login_and_failed_login_are_logged(): void
    {
        $this->post(route('admin.login.attempt'), ['email' => $this->admin->email, 'password' => 'salah'])->assertSessionHasErrors('email');
        $this->assertDatabaseHas('activity_logs', ['event' => 'login_failed', 'level' => 'danger']);

        $this->post(route('admin.login.attempt'), ['email' => $this->admin->email, 'password' => 'password'])->assertRedirect(route('admin.dashboard'));
        $this->assertDatabaseHas('activity_logs', ['event' => 'login', 'user_id' => $this->admin->id]);
        $this->assertNotNull($this->admin->fresh()->last_login_at);
    }

    public function test_inactive_user_cannot_login(): void
    {
        $this->admin->update(['is_active' => false]);
        $this->post(route('admin.login.attempt'), ['email' => $this->admin->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_all_admin_pages_render(): void
    {
        $this->actingAs($this->admin);
        $order = Order::create(['code' => 'VRS-1', 'customer_name' => 'A', 'customer_phone' => '0812', 'address' => 'x', 'subtotal' => 10, 'total' => 10]);
        $product = Product::first();
        $log = ActivityLog::first();

        foreach ([
            route('admin.dashboard'), route('admin.products.index'), route('admin.products.create'), route('admin.products.edit', $product),
            route('admin.categories.index'), route('admin.articles.index'), route('admin.articles.create'),
            route('admin.testimonials.index'), route('admin.faqs.index'), route('admin.orders.index'), route('admin.orders.show', $order),
            route('admin.subscribers.index'), route('admin.newsletters.index'), route('admin.newsletters.create'),
            route('admin.messages.index'), route('admin.notifications.index'), route('admin.notifications.feed'), route('admin.profile'),
            route('admin.logs.index'), route('admin.logs.show', $log), route('admin.settings'), route('admin.users.index'), route('admin.system-logs'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_editor_cannot_access_superadmin_pages(): void
    {
        $editor = User::create(['name' => 'Editor', 'email' => 'ed@x.com', 'password' => 'password', 'role' => 'editor', 'is_active' => true]);
        $this->actingAs($editor)->get(route('admin.settings'))->assertForbidden();
        $this->actingAs($editor)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($editor)->get(route('admin.products.index'))->assertOk();
    }

    public function test_product_update_is_logged_with_diff(): void
    {
        $product = Product::first();
        $oldPrice = $product->price;

        $this->actingAs($this->admin)->put(route('admin.products.update', $product), [
            'name' => $product->name, 'price' => 999000, 'stock' => 5, 'is_active' => 1,
            'benefits' => "Satu\nDua",
        ])->assertRedirect(route('admin.products.index'));

        $log = ActivityLog::where('event', 'updated')->where('subject_id', $product->id)->latest('id')->first();
        $this->assertSame($this->admin->id, $log->user_id);
        $this->assertEquals($oldPrice, $log->properties['old']['price']);
        $this->assertEquals(999000, $log->properties['attributes']['price']);
        $this->assertSame(['Satu', 'Dua'], $product->fresh()->benefits);
    }

    public function test_order_status_update_recalculates_total(): void
    {
        $order = Order::create(['code' => 'VRS-2', 'customer_name' => 'A', 'customer_phone' => '0812', 'address' => 'x', 'subtotal' => 100000, 'total' => 100000]);

        $this->actingAs($this->admin)->put(route('admin.orders.update', $order), ['status' => 'confirmed', 'shipping_cost' => 15000]);

        $order->refresh();
        $this->assertSame('confirmed', $order->status);
        $this->assertSame(115000, $order->total);
    }

    public function test_newsletter_is_sent_to_active_subscribers_only(): void
    {
        Notification::fake();
        Subscriber::create(['email' => 'a@x.com']);
        Subscriber::create(['email' => 'b@x.com', 'status' => 'unsubscribed']);

        $this->actingAs($this->admin)->post(route('admin.newsletters.store'), ['subject' => 'Promo', 'body' => 'Halo **semua**', 'action' => 'send'])
            ->assertSessionHas('success', 'Newsletter dikirim ke 1 subscriber.');

        Notification::assertSentTo(Subscriber::where('email', 'a@x.com')->first(), NewsletterNotification::class);
        Notification::assertNotSentTo(Subscriber::where('email', 'b@x.com')->first(), NewsletterNotification::class);
    }

    public function test_settings_update_changes_checkout_whatsapp_number(): void
    {
        $this->actingAs($this->admin)->put(route('admin.settings.update'), ['whatsapp_number' => '0811-2222-3333', 'site_name' => 'Varash Herbal'])->assertSessionHas('success');
        $this->assertSame('https://wa.me/6281122223333', wa_link());
        $this->assertDatabaseHas('activity_logs', ['log_name' => 'setting', 'event' => 'updated']);
    }

    public function test_article_html_is_sanitized(): void
    {
        $this->actingAs($this->admin)->post(route('admin.articles.store'), [
            'title' => 'Uji XSS', 'body' => '<p onclick="alert(1)">Aman</p><script>alert(1)</script><a href="javascript:alert(1)">x</a>', 'is_published' => 1,
        ]);
        $body = Article::where('slug', 'uji-xss')->value('body');
        $this->assertStringNotContainsString('<script', $body);
        $this->assertStringNotContainsString('onclick', $body);
        $this->assertStringNotContainsString('javascript:', $body);
    }

    public function test_logs_can_be_exported(): void
    {
        $this->actingAs($this->admin)->get(route('admin.logs.export'))->assertOk()->assertDownload();
        $this->actingAs($this->admin)->get(route('admin.orders.export'))->assertOk()->assertDownload();
    }
}
