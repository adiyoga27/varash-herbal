<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index', ['users' => User::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        User::create($this->validated($request));

        return back()->with('success', 'Pengguna ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);
        if ($user->is($request->user())) {
            // Cegah admin mengunci dirinya sendiri
            $data['is_active'] = true;
            $data['role'] = $user->role;
        }
        $user->update($data);

        return back()->with('success', 'Pengguna diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_if($user->is($request->user()), 422, 'Tidak dapat menghapus akun sendiri.');
        $user->delete();

        return back()->with('success', 'Pengguna dihapus.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user)],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => [$user ? 'nullable' : 'required', Password::min(8)],
        ]);
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data + ['is_active' => $request->boolean('is_active')];
    }
}
