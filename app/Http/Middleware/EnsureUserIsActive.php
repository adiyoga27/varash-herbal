<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next, ?string $role = null): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            Auth::logout();

            return redirect()->route('admin.login')->with('error', 'Akun Anda tidak aktif.');
        }

        if ($role === 'superadmin' && ! $user->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}
