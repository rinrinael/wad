<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Janji Temu</title>
    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}">
    <style>
        /* Gaya tambahan form */
        .form-section { padding: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #555; }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .submit-button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1rem;
            transition: background-color 0.3s ease;
        }
        .submit-button:hover { background-color: #0056b3; }
        .error-message { color: #dc3545; font-size: 0.85em; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="navigation-bar">
        <div class="row">
            <img src="{{ asset('images/mindmeet.png') }}" alt="MindMeet Logo" class="logo">
            <h2>MindMeet</h2>
        </div>

        <ul>
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('schedule') }}">Schedule</a></li>
            <li><a href="{{ route('appointment') }}">Appointment</a></li>
            <li><a href="{{ route('feedback') }}">Feedback</a></li>
            <li style="margin-top: 131%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        <h2>Hello {{ Auth::user()->name ?? 'Mahasiswa' }},</h2>
        <h1>Silakan Buat Janji Temu!</h1>
    </div>

    <div class="main form-section">
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

        <form action="{{ route('booking.submit') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="nim">NIM:</label>
                <input type="text" id="nim" name="nim" value="{{ old('nim', $mahasiswa->nim ?? '') }}" readonly required>
                @error('nim') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="jadwal_id">Pilih Jadwal Konseling:</label>
                <select id="jadwal_id" name="jadwal_id" required>
                    <option value="">- Pilih Jadwal Konseling -</option>
                    @forelse($jadwalsTersedia as $jadwal)
                        <option value="{{ $jadwal->id }}" {{ old('jadwal_id') == $jadwal->id ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($jadwal->hari)->format('d F Y') }} - {{ \Carbon\Carbon::parse($jadwal->waktu)->format('H:i') }} (Konselor: {{ $jadwal->konselor->nama ?? 'N/A' }})
                        </option>
                    @empty
                        <option value="" disabled>Tidak ada jadwal tersedia saat ini.</option>
                    @endforelse
                </select>
                @error('jadwal_id') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="submit-button">Buat Janji Temu</button>
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