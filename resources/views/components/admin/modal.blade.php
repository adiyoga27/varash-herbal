@props(['title' => '', 'size' => 'max-w-lg'])
{{-- Dipakai di dalam scope x-data="crudModal(...)" --}}
<div x-cloak x-show="open" class="fixed inset-0 z-50 flex items-end justify-center sm:items-center sm:p-4" role="dialog" aria-modal="true" @keydown.escape.window="open = false">
    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="open = false"></div>
    <div x-show="open" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="translate-y-8 opacity-0 sm:translate-y-0 sm:scale-95" x-transition:leave="transition duration-150" x-transition:leave-end="opacity-0"
         class="relative flex max-h-[92dvh] w-full {{ $size }} flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl sm:rounded-2xl">
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
            <h2 class="text-lg font-bold text-slate-900">{{ $title }}</h2>
            <button type="button" @click="open = false" class="a-icon-btn" aria-label="Tutup"><x-icon name="x" /></button>
        </div>
        <div class="overflow-y-auto">{{ $slot }}</div>
    </div>
</div>
