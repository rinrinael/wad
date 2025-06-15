<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Schedules</title>
    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}">
    <style>
        /* --- PERBAIKAN: Gaya untuk .schedule-card (Disalin dari .appointment-card) --- */
        .schedule-card {
            background-color: #f7f9fc; /* Latar belakang abu terang */
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1); /* Efek 3D / bayangan */
            padding: 20px 25px;
            margin-bottom: 15px;
            cursor: default;
            /* Tambahan untuk konsistensi layout jika diperlukan */
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            transition: transform 0.2s ease, box-shadow 0.2s ease; /* Transisi untuk hover */
        }
        .schedule-card:hover {
            box-shadow: 0 8px 15px rgba(0,0,0,0.15); /* Bayangan lebih kuat saat hover */
            transform: translateY(-3px); /* Efek sedikit terangkat saat hover */
        }

        .schedule-card p {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
            font-weight: 500;
        }
        .schedule-card p strong {
            color: #222;
        }

        /* Gaya untuk kontainer .main agar bisa di-scroll */
        .main {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            
            max-height: calc(100vh - 180px);
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Styling untuk scrollbar */
        .main::-webkit-scrollbar {
            width: 8px;
        }
        .main::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 4px;
        }
        .main::-webkit-scrollbar-track {
            background-color: rgba(0,0,0,0.1);
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
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('schedule') }}">Schedule</a></li>
            <li><a href="{{ route('appointment') }}">Appointment</a></li>
            <li><a href="{{ route('feedback') }}">Feedback</a></li>
            <li style="margin-top: 131%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        <h2>Hello {{ Auth::user()->name ?? 'Mahasiswa' }},</h2>
        <h1>Available Schedules</h1>
    </div>

    <div class="main">
        @forelse($jadwals as $jadwal)
            <div class="schedule-card"> {{-- Pastikan div ini membungkus setiap jadwal --}}
                <p><strong>Konselor:</strong> {{ $jadwal->konselor->nama ?? 'N/A' }}</p>
                <p><strong>Spesialisasi:</strong> {{ $jadwal->konselor->spesialisasi ?? 'N/A' }}</p>
                <p><strong>Hari:</strong> {{ \Carbon\Carbon::parse($jadwal->hari)->format('d F Y') }}</p>
                <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($jadwal->waktu)->format('H:i') }}</p>
                <p><strong>Status:</strong> {{ ucfirst($jadwal->status) }}</p>
            </div>
        @empty
            <p style="text-align: center; color: #777;">Tidak ada jadwal tersedia saat ini.</p>
        @endforelse
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