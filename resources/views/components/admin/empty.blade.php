@props(['icon' => 'box', 'message' => 'Belum ada data.'])
<div class="flex flex-col items-center px-6 py-14 text-center">
    <span class="grid size-14 place-items-center rounded-2xl bg-slate-100 text-slate-400"><x-icon :name="$icon" class="size-7" /></span>
    <p class="mt-3 text-sm text-slate-500">{{ $message }}</p>
    {{ $slot }}
</div>
