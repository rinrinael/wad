<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Home - Today's Appointment</title>
    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}" />
    <style>
        /* Styling khusus untuk bagian Today's Appointment (Ini tetap, namun pastikan konselor.css loading) */
        h2 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .appointment-card {
            background-color: #f7f9fc;
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            padding: 20px 25px;
            margin-bottom: 15px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: default;
        }

        .appointment-card:hover {
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
            transform: translateY(-3px);
        }

        .appointment-card p {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
            font-weight: 500;
        }

        .appointment-card p:first-child {
            font-weight: 700;
            color: #222;
            font-size: 18px;
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
            <li><a href="{{ route('feedback') }}"> Feedback</a><li>
            <li style="margin-top: 131%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        <h2>Hello {{ $user->name ?? 'User' }},</h2>
        <h1>Good Morning!</h1>
    </div>

    <div class="main">
        <h2 style="margin-bottom: 20px;">My Appointment</h2>

        @forelse($appointments as $appointment)
            <div class="appointment-card">
                <p><strong>ID Booking:</strong> {{ $appointment->id }}</p>
                {{-- Pastikan relasi 'jadwal' dan 'konselor' di Jadwal model sudah benar --}}
                <p><strong>Konselor:</strong> {{ $appointment->jadwal->konselor->nama ?? 'N/A' }}</p>
                <p><strong>Spesialisasi:</strong> {{ $appointment->jadwal->konselor->spesialisasi ?? 'N/A' }}</p>
                {{-- Perbaikan: Akses waktu dari model Jadwal yang terkait --}}
                <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($appointment->jadwal->waktu)->format('H:i') }}</p>
                <p><strong>Status:</strong> {{ $appointment->status }}</p>

                {{-- Tambahkan tombol Cancel di sini --}}
                @if ($appointment->status === 'booked' && \Carbon\Carbon::parse($appointment->jadwal->hari)->isFuture())
                <form action="{{ route('booking.cancel', $appointment->id) }}" method="POST" class="position-absolute top-0 end-0 mt-2 me-2">
                    @csrf
                    @method('PUT')
                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin membatalkan booking ini?')">Cancel</button>
                </form>
                @endif
            </div>
            @empty
                <p>No appointments today.</p>
        @endforelse
    </div>

    <div class="profil">
        <img src="{{ asset('images/profil.jpg') }}" alt="Profile Picture">
        <div class="profile-info">
            <h3>{{ $user->name ?? 'Nama Lengkap' }}</h3>
            {{-- Perbaikan: Akses 'major' dan 'nim' dari relasi Mahasiswa pada objek User --}}
            {{-- Pastikan relasi $user->mahasiswa dan data di tabel 'mahasiswa' sudah ada dan terisi --}}
            <p>{{ $user->mahasiswa->major ?? 'Program Studi' }}</p>
            <p>{{ $user->mahasiswa->nim ?? 'NIM' }}</p>
            <p>{{ $user->email ?? 'Email' }}</p>
        </div>
    </div>

</body>
</html>