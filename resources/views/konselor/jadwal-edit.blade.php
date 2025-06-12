<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jadwal Konselor</title>
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
            <li><a href="{{ route('konselor.dashboard') }}">Home</a></li>
            <li><a href="{{ route('konselor.my_schedules.create') }}">Input Jadwal</a></li>
            <li><a href="{{ route('konselor.my_schedules') }}">My Schedules</a></li>
            <li style="margin-top: 131%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        <h2>Hello {{ $user->name ?? $konselor->nama ?? 'Konselor' }},</h2>
        <h1>Edit Jadwal Anda</h1>
    </div>

    <div class="main">
        {{-- Form action diarahkan ke rute update, dengan metode PUT --}}
        <form action="{{ route('konselor.my_schedules.update', $jadwal->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Penting: Laravel akan menangani ini sebagai PUT request --}}

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

            <div class="form-row1">
                <div class="form-input">
                    <label for="konselor-id">ID Konselor</label>
                    <input type="text" id="konselor-id" name="konselor_id"
                           value="{{ $konselor->id ?? '' }}" readonly>
                </div>
            </div>

            <div class="form-row2">
                <div class="form-input">
                    <label for="hari">Tanggal Jadwal</label>
                    {{-- Isi dengan nilai jadwal yang sudah ada --}}
                    <input type="date" id="hari" name="hari" value="{{ old('hari', $jadwal->hari) }}" required>
                </div>
                <div class="form-input">
                    <label for="waktu">Waktu Mulai Jadwal</label>
                    {{-- Isi dengan nilai jadwal yang sudah ada --}}
                    <input type="time" id="waktu" name="waktu" value="{{ old('waktu', \Carbon\Carbon::parse($jadwal->waktu)->format('H:i')) }}" required>
                </div>
            </div>

            <div class="form-row3">
                <div class="form-input">
                    <label for="status">Status Jadwal</label>
                    {{-- Pilih status yang sudah ada --}}
                    <select id="status" name="status" required>
                        <option value="available" {{ old('status', $jadwal->status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="not_available" {{ old('status', $jadwal->status) == 'not_available' ? 'selected' : '' }}>Not Available</option>
                        <option value="booked" {{ old('status', $jadwal->status) == 'booked' ? 'selected' : '' }}>Booked</option>
                        <option value="cancelled" {{ old('status', $jadwal->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="submit-button">Update Jadwal</button>
        </form>
    </div>

    <div class="profil">
        <img src="{{ asset('images/profil.jpg') }}" alt="Profil Konselor">
        <div class="profile-info">
            <h3>{{ $konselor->nama ?? $user->name ?? 'Nama Konselor' }}</h3>
            <p>{{ $konselor->spesialisasi ?? 'Spesialisasi Konselor' }}</p>
            <p>{{ $user->email ?? 'Email Konselor' }}</p>
        </div>
    </div>
</body>
</html>