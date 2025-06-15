<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Mahasiswa</title>
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
            <li style="margin-top: 114%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        <h2>Hello {{ Auth::user()->name }},</h2>
        <h1>Edit Data Mahasiswa</h1>
    </div>

    <div class="main">
        <form action="{{ route('manage.student.update', $mahasiswa->nim) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Tampilkan pesan sukses/error dari session --}}
            @if (session('success'))
                <div style="color: green; margin-bottom: 10px; padding: 10px; border: 1px solid green; background-color: #d4edda; border-radius: 5px;">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div style="color: red; margin-bottom: 10px; padding: 10px; border: 1px solid red; background-color: #f8d7da; border-radius: 5px;">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div style="color: red; margin-bottom: 10px; padding: 10px; border: 1px solid red; background-color: #ffebeb; border-radius: 5px;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group">
                <label for="nim_baru">NIM:</label>
                <input type="text" id="nim_baru" name="nim_baru" value="{{ old('nim_baru', $mahasiswa->nim) }}" required maxlength="10"> @error('nim_baru')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="nama">Nama Mahasiswa:</label>
                <input type="text" id="nama" name="nama" value="{{ old('nama', $mahasiswa->nama) }}" required>
                @error('nama')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email', $mahasiswa->email_user ?? $mahasiswa->email) }}" required>
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

            <a href="{{ route('manage.student') }}" class="btn-back">Batal</a>
            <button type="submit" class="btn-submit">Update Mahasiswa</button>
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