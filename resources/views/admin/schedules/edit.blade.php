<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Jadwal</title>
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
        <h1>Edit Jadwal</h1>
    </div>

    <div class="main">
        {{-- Form action diarahkan ke rute update, dengan metode PUT --}}
        <form action="{{ route('manage.schedule.update', $jadwal->id) }}" method="POST">
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
                <label for="konselor-id">ID Konselor</label>
                <input type="text" id="konselor-id" name="konselor_id" value="{{ $jadwal->konselor_id }}" readonly>
            </div>

            <div class="form-group">
                <label for="konselor-name">Nama Konselor</label>
                <input type="text" id="konselor-name" value="{{ $jadwal->konselor->nama ?? 'N/A' }}" readonly>
            </div>

            <div class="form-group">
                <label for="hari">Tanggal Jadwal</label>
                <input type="date" id="hari" name="hari" value="{{ old('hari', \Carbon\Carbon::parse($jadwal->hari)->format('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
                <label for="waktu">Waktu Mulai Jadwal</label>
                <input type="time" id="waktu" name="waktu" value="{{ old('waktu', \Carbon\Carbon::parse($jadwal->waktu)->format('H:i')) }}" required>
            </div>

            <div class="form-group">
                <label for="status">Status Jadwal</label>
                <select id="status" name="status" required>
                    <option value="available" {{ old('status', $jadwal->status) == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="not_available" {{ old('status', $jadwal->status) == 'not_available' ? 'selected' : '' }}>Not Available</option>
                    <option value="booked" {{ old('status', $jadwal->status) == 'booked' ? 'selected' : '' }}>Booked</option>
                    <option value="cancelled" {{ old('status', $jadwal->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <a href="{{ route('manage.schedule') }}" class="btn-back">Batal</a>
            <button type="submit" class="btn-submit">Update Jadwal</button>
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