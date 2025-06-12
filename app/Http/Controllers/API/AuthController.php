<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Konselor; // Pastikan ini di-import
use App\Models\Mahasiswa; // Pastikan ini di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * Menampilkan form login.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Menampilkan form registrasi.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Memproses registrasi user baru dan membuat record terkait (mahasiswa/konselor).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in(['mahasiswa', 'konselor'])],

            // Validasi 'spesialisasi' hanya jika role adalah 'konselor'
            'spesialisasi' => [
                'sometimes', // Hanya jika field ada di request
                Rule::requiredIf($request->role === 'konselor'), // Wajib jika role konselor
                'string',
                'max:255',
            ],
            // Validasi 'nim' hanya jika role adalah 'mahasiswa'
            'nim' => [
                'sometimes', // Hanya jika field ada di request
                Rule::requiredIf($request->role === 'mahasiswa'), // Wajib jika role mahasiswa
                'integer', // NIM harus angka bulat
                'unique:mahasiswa,nim', // Pastikan NIM unik di tabel mahasiswa
            ],
            // 'tanggal' mahasiswa, jika tidak dari form, akan pakai now()
            // 'major' mahasiswa, jika tidak dari form, akan pakai default atau kosong
        ]);

        // 1. Buat User baru di tabel 'users'
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        // 2. Buat record terkait di tabel 'konselor' atau 'mahasiswa'
        if ($user->role === 'konselor') {
            Konselor::create([
                'user_id' => $user->id,
                'nama' => $validatedData['name'],
                'email' => $validatedData['email'],
                // Ambil spesialisasi dari validatedData, jika tidak ada (meskipun requiredIf), default 'Umum'
                'spesialisasi' => $validatedData['spesialisasi'] ?? 'Umum',
                'password' => Hash::make($validatedData['password']), // Hash password jika kolomnya masih ada di tabel 'konselor'
            ]);
        } elseif ($user->role === 'mahasiswa') {
            Mahasiswa::create([
                'user_id' => $user->id,
                'nama' => $validatedData['name'],
                'email' => $validatedData['email'],
                // Ambil NIM dari validatedData
                'nim' => $validatedData['nim'],
                'tanggal' => now()->toDateString(), // Menggunakan tanggal saat ini sebagai default
                // 'major' => null, // Jika ada kolom 'major' di tabel mahasiswa dan tidak diisi dari form
                'password' => Hash::make($validatedData['password']), // Hash password jika kolom 'password' masih ada di tabel 'mahasiswa'
            ]);
        }

        // 3. Login user yang baru terdaftar
        Auth::login($user);

        // 4. Redirect sesuai role setelah register
        if ($user->role === 'mahasiswa') {
            return redirect()->route('home')->with('success', 'Registrasi berhasil! Selamat datang.');
        } elseif ($user->role === 'konselor') {
            return redirect()->route('konselor.dashboard')->with('success', 'Registrasi konselor berhasil! Selamat datang.');
        }

        // Redirect default jika role tidak dikenali
        return redirect('/')->with('success', 'Registrasi berhasil! Selamat datang.');
    }

    /**
     * Memproses login user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        // Redirect ke halaman yang dituju setelah login berhasil berdasarkan role
        $user = Auth::user();
        if ($user->role === 'mahasiswa') {
            return redirect()->intended(route('home'));
        } elseif ($user->role === 'konselor') {
            return redirect()->intended(route('konselor.dashboard'));
        } elseif ($user->role === 'admin') {
            return redirect()->intended(route('manage.booking'));
        }

        return redirect()->intended('/');
    }

    /**
     * Melakukan logout user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Menampilkan profil user (jika digunakan sebagai API endpoint).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
}