<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Available Schedules</title>
    {{-- Pastikan file CSS ini ada di public/css/ --}}
    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}" />
    <style>
        /* Anda bisa menambahkan atau mengkustomisasi gaya di sini jika diperlukan */
        .schedule-list {
            margin-top: 20px;
            padding: 0 20px;
        }
        .schedule-card {
            background-color: #f7f9fc;
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            padding: 15px 20px;
            margin-bottom: 10px;
        }
        .schedule-card p {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
        }
        .no-schedule-message {
            text-align: center;
            color: #777;
            font-size: 18px;
            margin-top: 50px;
        }
    </style>
</head>
<body>

    <div class="navigation-bar">
        <div class="row">
            {{-- Pastikan file gambar ini ada di public/images/ --}}
            <img src="{{ asset('images/mindmeet.png') }}" alt="MindMeet Logo" class="logo">
            <h2>MindMeet</h2>
        </div>

        <ul>
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('schedule') }}">Schedule</a></li>
            <li><a href="{{ route('appointment') }}">Appointment</a></li>
            <li style="margin-top: 131%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        <h1>Available Schedules</h1>
    </div>

    <div class="main schedule-list">
        {{-- Menggunakan variabel $jadwals yang dikirim dari controller --}}
        @forelse($jadwals as $jadwal)
            <div class="schedule-card">
                <p><strong>Konselor:</strong> {{ $jadwal->konselor->nama ?? 'N/A' }}</p>
                <p><strong>Spesialisasi:</strong> {{ $jadwal->konselor->spesialisasi ?? 'N/A' }}</p>
                <p><strong>Hari:</strong> {{ \Carbon\Carbon::parse($jadwal->hari)->format('d F Y') }}</p>
                <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($jadwal->waktu)->format('H:i') }}</p>
                <p><strong>Status:</strong> {{ $jadwal->status }}</p>
                {{-- <a href="{{ route('appointment', ['jadwal_id' => $jadwal->id]) }}">Book Now</a> --}}
            </div>
        @empty
            <p class="no-schedule-message">Tidak ada jadwal yang tersedia saat ini.</p>
        @endforelse
    </div>

    {{-- Bagian profil biasanya tidak ada di halaman jadwal, jika ada, tambahkan di sini --}}

</body>
</html>