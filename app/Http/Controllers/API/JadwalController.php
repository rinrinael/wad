<?php

namespace App\Http\Controllers\API; // Namespace tetap API

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Konselor; // Tambahkan ini jika belum ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Pastikan Carbon diimpor

class JadwalController extends Controller
{
    // Fungsi pembantu untuk mendapatkan ID konselor yang login
    protected function getLoggedInKonselorId()
    {
        $user = Auth::user()->load('konselor'); // Pastikan relasi 'konselor' di User model
        // Jika user adalah konselor dan memiliki data konselor terkait
        if ($user && $user->role === 'konselor' && $user->konselor) {
            return $user->konselor->id;
        }
        // Jika tidak, kembalikan null atau throw error (tergantung kebutuhan)
        abort(403, 'Unauthorized. Not a linked konselor.'); // Atau return null
    }

    /**
     * Tampilkan semua jadwal milik konselor yang sedang login (Read All).
     * Diakses melalui GET /konselor/my-schedules
     */
    public function index()
    {
        $konselorId = $this->getLoggedInKonselorId();
        $jadwals = Jadwal::where('konselor_id', $konselorId)->get();

        // Eager load user dan konselor untuk profil sidebar di view
        $user = Auth::user()->load('konselor');
        $konselor = $user->konselor;

        return view('konselor/jadwal-konselor', compact('jadwals', 'user', 'konselor'));
    }

    /**
     * Menampilkan form untuk membuat jadwal baru (Create Form).
     * Diakses melalui GET /konselor/my-schedules/create
     */
    public function create()
    {
        $user = Auth::user()->load('konselor'); // Eager load konselor untuk profil sidebar
        $konselor = $user->konselor; // Dapatkan objek konselor terkait

        if (!$konselor) {
            abort(403, 'Unauthorized. Konselor data not linked.');
        }

        return view('konselor/input-konselor', compact('user', 'konselor'));
    }

    /**
     * Simpan jadwal baru ke database (Store).
     * Diakses melalui POST /konselor/my-schedules
     */
    public function store(Request $request)
    {
        $konselorId = $this->getLoggedInKonselorId(); // Dapatkan ID konselor yang login

        $request->validate([
            'hari' => 'required|date',
            'waktu' => 'required|date_format:H:i|before_or_equal:21:00',
            'status' => 'required|string|in:available,not_available' // Tambahkan validasi status yang diizinkan
        ]);

        $jadwal = Jadwal::create([
            'konselor_id' => $konselorId, // Gunakan ID konselor yang benar
            'hari' => $request->hari,
            'waktu' => $request->waktu,
            'status' => $request->status,
        ]);

        // Redirect ke halaman daftar jadwal konselor setelah berhasil menyimpan
        return redirect()->route('konselor.my_schedules')->with('success', 'Jadwal berhasil dibuat!');
    }

    /**
     * Tampilkan detail satu jadwal (Read One).
     * Diakses melalui GET /konselor/my-schedules/{id}
     * Biasanya untuk API atau tampilan detail, bukan form edit.
     */
    public function show($id)
    {
        $konselorId = $this->getLoggedInKonselorId(); // Dapatkan ID konselor yang login
        $jadwal = Jadwal::where('konselor_id', $konselorId)->findOrFail($id);
        return response()->json($jadwal); // Tetap JSON karena rute show biasanya untuk API/data mentah
    }

    /**
     * Menampilkan form untuk mengedit jadwal (Edit Form).
     * Diakses melalui GET /konselor/my-schedules/{id}/edit
     */
    public function edit($id)
    {
        $konselorId = $this->getLoggedInKonselorId();
        // Temukan jadwal berdasarkan ID dan pastikan itu milik konselor yang login
        $jadwal = Jadwal::where('konselor_id', $konselorId)->findOrFail($id);

        $user = Auth::user()->load('konselor');
        $konselor = $user->konselor;

        return view('konselor/jadwal-edit', compact('jadwal', 'user', 'konselor')); // Mengembalikan view form edit
    }

    /**
     * Update jadwal di database (Update).
     * Diakses melalui PUT /konselor/my-schedules/{id}
     */
    public function update(Request $request, $id)
    {
        $konselorId = $this->getLoggedInKonselorId();
        $jadwal = Jadwal::where('konselor_id', $konselorId)->findOrFail($id);

        $request->validate([
            'hari' => 'sometimes|required|date',
            'waktu' => 'sometimes|required|date_format:H:i|before_or_equal:21:00',
            'status' => 'sometimes|required|string|in:available,not_available,booked,cancelled' // Tambahkan status booked/cancelled
        ]);

        $jadwal->update($request->only(['hari', 'waktu', 'status']));

        // Redirect ke halaman daftar jadwal konselor setelah berhasil update
        return redirect()->route('konselor.my_schedules')->with('success', 'Jadwal berhasil diupdate.');
    }

    /**
     * Hapus jadwal dari database (Destroy).
     * Diakses melalui DELETE /konselor/my-schedules/{id}
     */
    public function destroy($id)
    {
        $konselorId = $this->getLoggedInKonselorId();
        $jadwal = Jadwal::where('konselor_id', $konselorId)->findOrFail($id);
        $jadwal->delete();

        // Redirect ke halaman daftar jadwal konselor setelah berhasil menghapus
        return redirect()->route('konselor.my_schedules')->with('success', 'Jadwal berhasil dihapus.');
    }
    
    // --- METODE BARU UNTUK ADMIN MANAGEMENT SCHEDULE ---

    /**
     * Tampilkan semua jadwal (untuk Admin).
     * Metode ini dipanggil oleh rute 'manage.schedule'.
     */
    public function indexAdmin()
    {
        // Ambil SEMUA jadwal dengan data konselornya dan juga relasi booking
        $schedules = Jadwal::with(['konselor', 'booking'])->latest()->get(); // <-- PERUBAHAN DI SINI: Tambahkan 'booking'

        // Arahkan ke view admin dengan membawa data semua jadwal
        return view('admin.kelola-jadwal', compact('schedules'));
    }

    /**
     * Menampilkan form untuk mengedit jadwal (untuk Admin).
     * Diakses melalui GET /admin/schedules/{id}/edit
     */
    public function editAdmin($id)
    {
        $jadwal = Jadwal::with('konselor')->findOrFail($id); // Eager load konselor
        $user = Auth::user(); // User yang login (admin)

        return view('admin.schedules.edit', compact('jadwal', 'user')); // Menggunakan view baru di admin.schedules.edit
    }

    /**
     * Update jadwal di database (untuk Admin).
     * Diakses melalui PUT /admin/schedules/{id}
     */
    public function updateAdmin(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);

        $request->validate([
            'hari' => 'required|date',
            'waktu' => 'required|date_format:H:i|before_or_equal:21:00',
            'status' => 'required|string|in:available,not_available,booked,cancelled'
        ]);

        $jadwal->update($request->only(['hari', 'waktu', 'status']));

        return redirect()->route('manage.schedule')->with('success', 'Jadwal berhasil diupdate oleh Admin.');
    }

    /**
     * Hapus jadwal dari database (untuk Admin).
     * Diakses melalui DELETE /admin/schedules/{id}
     */
    public function destroyAdmin($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        
        // Opsional: Periksa apakah ada booking terkait sebelum menghapus
        // Jika ada booking, Anda mungkin ingin menghapus booking tersebut juga atau mengubah statusnya
        // Contoh: Jika ada booking, set statusnya ke 'cancelled' dan kemudian hapus jadwal
        if ($jadwal->booking) {
            $jadwal->booking->status = 'cancelled'; // Atau hapus bookingnya: $jadwal->booking->delete();
            $jadwal->booking->save();
        }
        
        $jadwal->delete();

        return redirect()->route('manage.schedule')->with('success', 'Jadwal berhasil dihapus oleh Admin.');
    }
}