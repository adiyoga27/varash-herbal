@extends('layouts.admin')

@section('title', 'Pengguna Admin')

@section('content')
<div x-data="crudModal({ name: '', email: '', role: 'admin', is_active: true })">
    <div class="flex justify-end"><button @click="create(@js(route('admin.users.store')))" class="a-btn-primary"><x-icon name="plus" class="size-4" /> Tambah Pengguna</button></div>
    <div class="a-card mt-5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="a-table">
                <thead><tr><th>Pengguna</th><th>Role</th><th class="hidden md:table-cell">Login Terakhir</th><th class="hidden sm:table-cell">Status</th><th class="text-right">Aksi</th></tr></thead>
                <tbody>
                    @foreach ($users as $u)
                        <tr>
                            <td><div class="flex items-center gap-3">
                                <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-forest-500 to-forest-700 text-xs font-bold text-white">{{ $u->initials() }}</span>
                                <div class="min-w-0"><p class="font-semibold text-slate-900">{{ $u->name }} @if ($u->is(auth()->user()))<span class="text-xs text-slate-400">(Anda)</span>@endif</p><p class="truncate text-xs text-slate-500">{{ $u->email }}</p></div>
                            </div></td>
                            <td><span class="a-badge bg-forest-50 text-forest-800">{{ \App\Models\User::ROLES[$u->role] ?? $u->role }}</span></td>
                            <td class="hidden md:table-cell text-xs text-slate-500">{{ $u->last_login_at?->diffForHumans() ?? 'Belum pernah' }}<br>{{ $u->last_login_ip }}</td>
                            <td class="hidden sm:table-cell"><span class="a-badge {{ $u->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-700' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td><div class="flex justify-end gap-1">
                                <a href="{{ route('admin.logs.index', ['user_id' => $u->id]) }}" class="a-icon-btn" title="Lihat aktivitas"><x-icon name="activity" class="size-4" /></a>
                                <button @click="edit(@js(route('admin.users.update', $u)), @js($u->only(['name', 'email', 'role', 'is_active'])))" class="a-icon-btn"><x-icon name="edit" class="size-4" /></button>
                                @unless ($u->is(auth()->user()))
                                    <form method="POST" action="{{ route('admin.users.destroy', $u) }}" data-confirm="Hapus pengguna {{ $u->name }}?">@csrf @method('DELETE')<button class="a-icon-btn hover:!text-red-600"><x-icon name="trash" class="size-4" /></button></form>
                                @endunless
                            </div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <x-admin.modal title="Pengguna">
        <form method="POST" :action="action" class="space-y-4 p-5">
            @csrf
            <template x-if="editing"><input type="hidden" name="_method" value="PUT"></template>
            <div><label class="a-label">Nama *</label><input name="name" x-model="form.name" required class="a-input"></div>
            <div><label class="a-label">Email *</label><input type="email" name="email" x-model="form.email" required class="a-input"></div>
            <div><label class="a-label">Role</label>
                <select name="role" x-model="form.role" class="a-input">@foreach (\App\Models\User::ROLES as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select>
                <p class="mt-1 text-xs text-slate-500">Super Admin dapat mengelola pengguna, pengaturan, & system log.</p>
            </div>
            <div><label class="a-label">Password <span x-show="editing" class="font-normal text-slate-400">(kosongkan jika tidak diubah)</span></label><input type="password" name="password" :required="!editing" minlength="8" autocomplete="new-password" class="a-input"></div>
            <x-admin.toggle name="is_active" model="form.is_active" label="Akun aktif" />
            <div class="flex gap-2 pt-2"><button type="button" @click="open = false" class="a-btn-light flex-1">Batal</button><button class="a-btn-primary flex-1">Simpan</button></div>
        </form>
    </x-admin.modal>
</div>
@endsection
