@props(['name', 'checked' => false, 'label' => '', 'model' => null])
<label class="inline-flex cursor-pointer items-center gap-3">
    <input type="checkbox" name="{{ $name }}" value="1" class="peer sr-only" @if ($model) x-model="{{ $model }}" @else @checked($checked) @endif>
    <span class="relative h-6 w-11 rounded-full bg-slate-200 transition peer-checked:bg-forest-600 peer-focus-visible:ring-4 peer-focus-visible:ring-forest-500/20 after:absolute after:top-0.5 after:left-0.5 after:size-5 after:rounded-full after:bg-white after:shadow after:transition peer-checked:after:translate-x-5"></span>
    <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
</label>
