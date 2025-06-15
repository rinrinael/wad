<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\User; // Penting: Impor model User
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Jadwal; // <-- Tambahkan ini
use App\Models\Konselor;
use Carbon\Carbon;

class MahasiswaController extends Controller
{
    public function index() {
        $students = Mahasiswa::with('user')->get();
        return view('admin.kelola-mahasiswa', ['students' => $students]);
    }

    public function create() {
        // Ambil jadwal yang tersedia untuk ditampilkan di form
        // Filter jadwal yang tersedia dan di masa depan, eager load konselor
        $jadwalsTersedia = Jadwal::whereDoesntHave('booking')
                                 ->where('status', 'available')
                                 ->where('hari', '>=', Carbon::today()->toDateString())
                                 ->with('konselor')
                                 ->get();
        
        // Asumsi ini form untuk register mahasiswa baru (user_id akan di-assign setelah User dibuat)
        // Jika form ini hanya untuk melengkapi profil Mahasiswa setelah user register,
        // maka Auth::user() bisa digunakan untuk mendapatkan user_id.
        // Untuk sekarang, kita asumsikan ini adalah form pendaftaran awal.

        return view('mahasiswa.create', compact('jadwalsTersedia'));
    }

    public function store(Request $request) {
         $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
            'nim'       => 'required|string|max:10|unique:mahasiswa,nim', // Batasi NIM max 10 karakter
            'tanggal'   => 'required|date',
            'jadwal_id' => 'required|exists:jadwal,id',
        ]);

        // 1. Buat record di tabel `users`
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password), // Password di-hash untuk tabel users
            'role'      => 'mahasiswa',
        ]);

        // 2. Buat record di tabel `mahasiswa`, link ke `user` yang baru dibuat
        Mahasiswa::create([
            'user_id'   => $user->id,
            'nim'       => $request->nim,
            'nama'      => $request->name,
            'email'     => $request->email,
            'tanggal'   => $request->tanggal,
            'jadwal_id' => $request->jadwal_id,
            'password'  => $user->password, // <--- TAMBAHKAN BARIS INI: Simpan password yang sudah di-hash dari User
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Pendaftaran mahasiswa berhasil!');
    }

    public function destroy($nim) {
        $mahasiswa = Mahasiswa::findOrFail($nim);
        $mahasiswa->delete();

        return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa deleted successfully.');
    }
    
    // --- METODE UNTUK ADMIN MANAGEMENT STUDENT ---

    public function editAdmin($nim)
    {
        $mahasiswa = Mahasiswa::with('user')->findOrFail($nim);
        return view('admin.students.edit', compact('mahasiswa'));
    }

    public function updateAdmin(Request $request, $nim)
    {
        $mahasiswa = Mahasiswa::with('user')->findOrFail($nim); // Pastikan user dimuat

        $validatedData = $request->validate([
            'nama' => 'required|string|max:255', // Ini adalah nama dari form
            'nim_baru' => [
                'required',
                'string',
                'max:10', // Batasan sesuai diskusi sebelumnya
                Rule::unique('mahasiswa', 'nim')->ignore($mahasiswa->nim, 'nim'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($mahasiswa->user->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // --- UPDATE DATA MAHASISWA (TABEL 'mahasiswa') ---
        $mahasiswa->nama = $validatedData['nama'];
        $mahasiswa->email = $validatedData['email']; // Tetap simpan ini jika kolom email ada di tabel mahasiswa
        
        if ($mahasiswa->nim != $validatedData['nim_baru']) {
            $mahasiswa->nim = $validatedData['nim_baru'];
        }
        $mahasiswa->save(); // Simpan perubahan pada Mahasiswa

        // --- UPDATE DATA USER TERKAIT (TABEL 'users') ---
        $user = $mahasiswa->user; // Dapatkan objek User terkait
        if ($user) {
            $user->name = $validatedData['nama']; // <-- FIX: Perbarui juga kolom 'name' di tabel users
            $user->email = $validatedData['email'];
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save(); // Simpan perubahan pada User
        }

        return redirect()->route('manage.student')->with('success', 'Data mahasiswa berhasil diperbarui.');
    }

    public function destroyAdmin($nim)
    {
        $mahasiswa = Mahasiswa::with('user')->findOrFail($nim);

        $userId = $mahasiswa->user_id;

        $mahasiswa->delete();

        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $user->delete();
            }
        }

        return redirect()->route('manage.student')->with('success', 'Mahasiswa dan User terkait berhasil dihapus.');
    }
}