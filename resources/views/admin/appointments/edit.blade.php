<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Appointment</title>
    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}">
    <style>
        /* Gaya tambahan untuk form edit (jika diperlukan) */
        .form-section h1 {
            margin-bottom: 30px;
        }
        /* Tambahan gaya untuk input readonly agar terlihat berbeda */
        input[readonly] {
            background-color: #e9ecef; /* Warna abu-abu terang */
            opacity: 1; /* Pastikan tidak transparan */
        }
    </style>
</head>
<body>
    <div class="navigation-bar">
        <div class="row">
            <img src="{{ asset('images/mindmeet.png') }}" alt="MindMeet Logo" class="logo">
            <h2>MindMeet</h2>
        </div>

        <ul>
            <li><a href="{{ route('manage.booking') }}">Manage Booking</a></li>
            <li><a href="{{ route('manage.counselor') }}">Manage Counselor</a></li>
            <li><a href="{{ route('manage.schedule') }}">Manage Schedule</a></li>
            <li><a href="{{ route('manage.student') }}">Manage Student</a></li>
            <li><a href="{{ route('manage.feedback') }}" class="active"> Manage Feedback</a></li>
            <li style="margin-top: 114%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        <h2>Hello {{ Auth::user()->name }},</h2>
        <h1>Good Morning!</h1>
    </div>

    <div class="main">
        {{-- Form action diarahkan ke rute update, dengan metode PUT --}}
        <form action="{{ route('appointments.update', $appointment->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Penting: Laravel akan menangani ini sebagai PUT request --}}

            <div class="form-group">
                <label for="mahasiswa">Mahasiswa:</label>
                {{-- Menampilkan nama mahasiswa, tidak bisa diedit langsung di sini --}}
                <input type="text" id="mahasiswa" name="mahasiswa_nama" value="{{ $appointment->mahasiswa->nama ?? 'N/A' }}" readonly>
            </div>

            <div class="form-group">
                <label for="konselor">Konselor:</label>
                {{-- Menampilkan nama konselor, tidak bisa diedit langsung di sini --}}
                <input type="text" id="konselor" name="konselor_nama" value="{{ $appointment->jadwal->konselor->nama ?? 'N/A' }}" readonly>
            </div>

            <div class="form-group">
                <label for="tanggal">Tanggal Jadwal Booking:</label>
                {{-- Gunakan $appointment->jadwal->hari untuk tanggal jadwal --}}
                <input type="date" id="tanggal" name="tanggal_display" value="{{ old('tanggal_display', \Carbon\Carbon::parse($appointment->jadwal->hari)->format('Y-m-d')) }}" readonly>
            </div>

            <div class="form-group">
                <label for="waktu">Waktu Jadwal:</label>
                <input type="time" id="waktu" name="waktu_display" value="{{ old('waktu_display', \Carbon\Carbon::parse($appointment->jadwal->waktu)->format('H:i')) }}" readonly>
            </div>

            <div class="form-group">
                <label for="status">Status:</label>
                <select id="status" name="status" required>
                    <option value="booked" {{ old('status', $appointment->status) == 'booked' ? 'selected' : '' }}>Booked</option>
                    <option value="completed" {{ old('status', $appointment->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ old('status', $appointment->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="jadwal_id">Pilih Jadwal Baru (untuk Reschedule):</label>
                <select id="jadwal_id" name="jadwal_id" required>
                    {{-- Opsi jadwal saat ini --}}
                    <option value="{{ $appointment->jadwal_id }}" selected>
                        Jadwal Saat Ini: {{ \Carbon\Carbon::parse($appointment->jadwal->hari)->format('d F Y') }} Pukul {{ \Carbon\Carbon::parse($appointment->jadwal->waktu)->format('H:i') }} (Konselor: {{ $appointment->jadwal->konselor->nama ?? 'N/A' }})
                    </option>

                    @foreach($availableJadwals as $jadwal_option) {{-- Gunakan nama variabel berbeda untuk menghindari konflik --}}
                        <option value="{{ $jadwal_option->id }}" 
                            {{ old('jadwal_id', $appointment->jadwal_id) == $jadwal_option->id ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($jadwal_option->hari)->format('d F Y') }} - {{ \Carbon\Carbon::parse($jadwal_option->waktu)->format('H:i') }} (Konselor: {{ $jadwal_option->konselor->nama }})
                        </option>
                    @endforeach
                </select>
                @error('jadwal_id')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            {{-- PERBAIKAN: Ubah rute 'appointments.index' menjadi 'manage.booking' --}}
            <a href="{{ route('manage.booking') }}" class="btn-back">Batal</a>
            <button type="submit" class="btn-submit">Update Appointment</button>
        </form>
    </div>

    <div class="profil">
        <img src="{{ asset('images/profil.jpg') }}" alt="Foto Profil">
        <div class="profile-info">
            <h3>{{ Auth::user()->name }}</h3>
            <p>{{ Auth::user()->email }}</p>
        </div>
    </div>
</body>
</html>