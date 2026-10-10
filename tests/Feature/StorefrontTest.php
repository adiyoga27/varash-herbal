<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subscriber;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Notifications\WelcomeSubscriberNotification;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_public_pages_render(): void
    {
        $product = Product::first();
        $article = Article::first();

        foreach (['/', '/produk', "/produk/{$product->slug}", '/kategori/minyak-herbal', '/artikel', "/artikel/{$article->slug}", '/tentang-kami', '/faq', '/kontak', '/keranjang', '/lacak-pesanan', '/kebijakan-privasi'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_product_page_has_seo_markup(): void
    {
        $product = Product::where('slug', 'varash-classic-natural-oil')->first();

        $this->get(route('products.show', $product))
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('products.show', $product).'">', false)
            ->assertSee('"@type":"Product"', false)
            ->assertSee('"@type":"BreadcrumbList"', false)
            ->assertSee('og:image', false);
    }

    public function test_sitemap_and_robots(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml')
            ->assertSee(route('products.show', Product::first()));
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /admin')->assertSee('Sitemap:');
    }

    public function test_inactive_product_is_hidden(): void
    {
        $product = Product::first();
        $product->update(['is_active' => false]);

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_checkout_creates_order_and_redirects_to_whatsapp(): void
    {
        Notification::fake();
        $product = Product::where('slug', 'varash-natural-oil')->first();

        $this->postJson(route('cart.add'), ['product_id' => $product->id, 'qty' => 2])
            ->assertOk()->assertJson(['count' => 2]);

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '0812-3456-7890',
            'address' => 'Jl. Raya Ubud No. 1',
            'city' => 'Gianyar',
            'province' => 'Bali',
            'payment_method' => 'transfer',
        ]);

        $order = Order::with('items')->first();
        $response->assertRedirect(route('checkout.success', $order->code));
        $this->assertSame(2 * $product->finalPrice(), $order->total);
        $this->assertCount(1, $order->items);
        Notification::assertSentTo(User::first(), NewOrderNotification::class);

        $this->get(route('checkout.success', $order->code))->assertOk()->assertSee($order->code);

        $wa = $this->get(route('checkout.whatsapp', $order->code));
        $wa->assertRedirect();
        $target = $wa->headers->get('Location');
        $this->assertStringStartsWith('https://wa.me/6282210810856?text=', $target);
        $message = rawurldecode(parse_url($target, PHP_URL_QUERY));
        $this->assertStringContainsString($order->code, $message);
        $this->assertStringContainsString('Budi Santoso', $message);
        $this->assertStringContainsString('VARASH NATURAL OIL', strtoupper($message));

        $this->assertNotNull($order->fresh()->whatsapp_sent_at);
        $this->assertDatabaseHas('activity_logs', ['event' => 'checkout', 'subject_id' => $order->id]);
        $this->assertDatabaseHas('activity_logs', ['event' => 'whatsapp_redirect', 'subject_id' => $order->id]);

        // Keranjang dikosongkan setelah checkout
        $this->get(route('checkout.index'))->assertRedirect(route('cart.index'));
    }

    public function test_checkout_validates_required_fields(): void
    {
        $this->post(route('cart.add'), ['product_id' => Product::first()->id]);

        $this->post(route('checkout.store'), [])->assertSessionHasErrors(['customer_name', 'customer_phone', 'address', 'city', 'province']);
        $this->assertSame(0, Order::count());
    }

    public function test_success_page_is_private_to_buyer_session(): void
    {
        $order = Order::create(['code' => 'VRS-TEST-0001', 'customer_name' => 'X', 'customer_phone' => '0811', 'address' => 'a', 'subtotal' => 1, 'total' => 1]);

        $this->get(route('checkout.success', $order->code))->assertNotFound();
        $this->get(route('checkout.whatsapp', $order->code))->assertNotFound();
    }

    public function test_newsletter_subscribe_and_unsubscribe(): void
    {
        Notification::fake();

        $this->postJson(route('newsletter.subscribe'), ['email' => 'Sehat@Example.com'])->assertOk();
        $subscriber = Subscriber::firstOrFail();
        $this->assertSame('sehat@example.com', $subscriber->email);
        Notification::assertSentTo($subscriber, WelcomeSubscriberNotification::class);

        // Duplikat tidak membuat baris baru
        $this->postJson(route('newsletter.subscribe'), ['email' => 'sehat@example.com'])->assertOk();
        $this->assertSame(1, Subscriber::count());

        $this->get(route('newsletter.unsubscribe', $subscriber->token))->assertOk();
        $this->assertSame('active', $subscriber->fresh()->status); // GET hanya konfirmasi
        $this->post(route('newsletter.unsubscribe.confirm', $subscriber->token))->assertOk();
        $this->assertSame('unsubscribed', $subscriber->fresh()->status);
    }

    public function test_contact_form_notifies_admin(): void
    {
        $this->post(route('contact.send'), ['name' => 'Ani', 'message' => 'Halo, apakah ada COD?'])->assertSessionHas('success');
        $this->assertSame(1, User::first()->unreadNotifications()->count());
    }
}
