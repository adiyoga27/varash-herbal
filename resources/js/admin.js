import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

document.addEventListener('alpine:init', () => {
    Alpine.store('toast', {
        items: [],
        show(message, type = 'success') {
            const id = Date.now() + Math.random();
            this.items.push({ id, message, type });
            setTimeout(() => (this.items = this.items.filter((t) => t.id !== id)), 4000);
        },
    });

    // Lonceng notifikasi: polling ringan setiap 30 detik
    Alpine.data('notifications', (feedUrl) => ({
        open: false,
        unread: 0,
        items: [],
        lastUnread: null,
        init() {
            this.fetch();
            setInterval(() => document.visibilityState === 'visible' && this.fetch(), 30000);
        },
        async fetch() {
            try {
                const res = await fetch(feedUrl, { headers: { Accept: 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();
                if (this.lastUnread !== null && data.unread > this.lastUnread && data.items[0]) {
                    Alpine.store('toast').show('🔔 ' + data.items[0].title, 'info');
                }
                this.lastUnread = data.unread;
                this.unread = data.unread;
                this.items = data.items;
            } catch (_) {}
        },
    }));

    // Modal create/edit generik: isi form dari objek JSON
    Alpine.data('crudModal', (defaults = {}) => ({
        open: false,
        editing: false,
        action: '',
        form: { ...defaults },
        create(action) {
            this.form = { ...defaults };
            this.action = action;
            this.editing = false;
            this.open = true;
        },
        edit(action, data) {
            this.form = { ...defaults, ...data };
            this.action = action;
            this.editing = true;
            this.open = true;
        },
    }));

    // Pratinjau gambar sebelum upload
    Alpine.data('imagePreview', (initial = null) => ({
        preview: initial,
        pick(e) {
            const file = e.target.files[0];
            if (file) this.preview = URL.createObjectURL(file);
        },
    }));

    // Penghitung karakter untuk field SEO
    Alpine.data('counter', (initial = '', max = 160) => ({
        value: initial,
        max,
        get left() { return this.max - (this.value?.length || 0); },
    }));
});

// Konfirmasi untuk form berbahaya: <form data-confirm="Yakin?">
document.addEventListener('submit', (e) => {
    const msg = e.target.dataset?.confirm;
    if (msg && !window.confirm(msg)) e.preventDefault();
});

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();
