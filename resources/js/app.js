import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import intersect from '@alpinejs/intersect';

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;

async function postJson(url, method, body = {}) {
    const res = await fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
        body: JSON.stringify(body),
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok) {
        const first = data.errors ? Object.values(data.errors)[0]?.[0] : null;
        throw new Error(first || data.message || 'Terjadi kesalahan, silakan coba lagi.');
    }
    return data;
}

document.addEventListener('alpine:init', () => {
    // Toast global
    Alpine.store('toast', {
        items: [],
        show(message, type = 'success') {
            const id = Date.now() + Math.random();
            this.items.push({ id, message, type });
            setTimeout(() => this.dismiss(id), 3800);
        },
        dismiss(id) {
            this.items = this.items.filter((t) => t.id !== id);
        },
    });

    // Keranjang global (jumlah item di header & drawer)
    Alpine.store('cart', {
        count: Number(document.querySelector('meta[name="cart-count"]')?.content || 0),
        bump: false,
        set(count) {
            this.count = count;
            this.bump = true;
            setTimeout(() => (this.bump = false), 400);
        },
    });

    // Tombol tambah ke keranjang
    Alpine.data('addToCart', (productId, initialQty = 1) => ({
        qty: initialQty,
        loading: false,
        async add() {
            if (this.loading) return;
            this.loading = true;
            try {
                const data = await postJson(window.routes.cartAdd, 'POST', { product_id: productId, qty: this.qty });
                Alpine.store('cart').set(data.count);
                Alpine.store('toast').show(data.message);
            } catch (e) {
                Alpine.store('toast').show(e.message, 'error');
            } finally {
                this.loading = false;
            }
        },
        inc() { this.qty = Math.min(99, this.qty + 1); },
        dec() { this.qty = Math.max(1, this.qty - 1); },
    }));

    // Baris item di halaman keranjang
    Alpine.data('cartItem', (productId, qty, subtotal) => ({
        qty,
        subtotal,
        removed: false,
        async update(newQty) {
            newQty = Math.max(0, Math.min(99, newQty));
            try {
                const data = await postJson(`${window.routes.cartBase}/${productId}`, newQty === 0 ? 'DELETE' : 'PATCH', { qty: newQty });
                Alpine.store('cart').set(data.count);
                this.qty = newQty;
                this.removed = newQty === 0;
                const item = data.items.find((i) => i.id === productId);
                this.subtotal = item ? item.subtotal_formatted : this.subtotal;
                window.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
                if (this.removed) Alpine.store('toast').show(data.message);
            } catch (e) {
                Alpine.store('toast').show(e.message, 'error');
            }
        },
    }));

    // Form newsletter
    Alpine.data('newsletter', (source = 'footer') => ({
        email: '',
        loading: false,
        done: false,
        async submit() {
            if (this.loading) return;
            this.loading = true;
            try {
                const data = await postJson(window.routes.subscribe, 'POST', { email: this.email, source });
                Alpine.store('toast').show(data.message);
                this.done = true;
                this.email = '';
            } catch (e) {
                Alpine.store('toast').show(e.message, 'error');
            } finally {
                this.loading = false;
            }
        },
    }));
});

// Animasi muncul saat di-scroll
const io = new IntersectionObserver(
    (entries) => entries.forEach((e) => e.isIntersecting && (e.target.classList.add('is-visible'), io.unobserve(e.target))),
    { threshold: 0.12 },
);
document.addEventListener('DOMContentLoaded', () => document.querySelectorAll('.reveal').forEach((el) => io.observe(el)));

Alpine.plugin(collapse);
Alpine.plugin(intersect);
window.Alpine = Alpine;
Alpine.start();
