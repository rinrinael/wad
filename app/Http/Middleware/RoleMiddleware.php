<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $roles): Response
    {
        if (!Auth::check()) {
            // Jika user belum login, arahkan ke halaman login
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Mengambil role yang dibutuhkan sebagai array
        $requiredRoles = explode(',', $roles);

        // Memastikan user memiliki properti 'role' dan tidak kosong
        if (!isset($user->role) || empty($user->role)) {
            // Jika role user tidak terdefinisi, arahkan ke login atau tampilkan 403
            // Ini untuk mencegah loop jika ada user tanpa role
            Auth::logout(); // Logout user yang tidak punya role valid
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Role pengguna tidak terdefinisi. Silakan login kembali.');
            // Atau: abort(403, 'Role pengguna tidak terdefinisi.');
        }

        // Cek apakah user memiliki salah satu dari role yang dibutuhkan untuk rute ini
        if (!in_array($user->role, $requiredRoles)) {
            // User tidak memiliki role yang diizinkan untuk rute ini.
            // Arahkan mereka ke dashboard yang sesuai dengan role mereka.

            if ($user->role === 'mahasiswa') {
                return redirect()->route('home')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
            } elseif ($user->role === 'konselor') {
                return redirect()->route('konselor.dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
            } elseif ($user->role === 'admin') {
                return redirect()->route('manage.booking')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
            }

            // Fallback jika role tidak dikenali atau tidak ada rute dashboard spesifik
            // Pilihan paling aman adalah abort(403) untuk mencegah redirect loop tak terduga.
            abort(403, 'Akses tidak sah. Anda tidak memiliki izin.');
            // Atau: return redirect('/')->with('error', 'Akses tidak sah.');
        }

        // Jika user memiliki role yang sesuai, lanjutkan request
        return $next($request);
    }
}