<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Konselor</title> <!-- PERUBAHAN: Ubah judul dari "Edit Jadwal Konselor" menjadi "Edit Konselor" -->
    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}">
    <style>
        /* Gaya tambahan untuk form edit (jika diperlukan) */
        .form-section h1 {
            margin-bottom: 30px;
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
        {{-- PERUBAHAN KRUSIAL DI SINI: Menggunakan $konselor->id dan nama rute yang benar 'counselor.update' --}}
        <form action="{{ route('counselor.update', $konselor->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Penting: Laravel akan menangani ini sebagai PUT request --}}

            <div class="form-group">
                <label for="nama">Nama:</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $konselor->nama) }}" required>
                @error('nama')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="spesialisasi">Spesialisasi:</label>
                <input type="text" id="spesialisasi" name="spesialisasi" value="{{ old('spesialisasi', $konselor->spesialisasi) }}" required>
                @error('spesialisasi')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email', $konselor->email) }}" required>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password (kosongkan jika tidak ingin mengubah):</label>
                <input type="password" id="password" name="password">
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password:</label>
                <input type="password" id="password_confirmation" name="password_confirmation">
            </div>

            <a href="{{ route('manage.counselor') }}" class="btn-back">Batal</a>
            <button type="submit" class="btn-submit">Update Konselor</button>
        </form>
    </div>
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