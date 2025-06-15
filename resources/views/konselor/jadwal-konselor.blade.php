<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Schedules - Konselor</title>
    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}" />
    <style>
        /* Styling untuk daftar jadwal */
        .schedule-list-container {
            margin-top: 20px;
            padding: 0 20px;
        }
        .schedule-item {
            background-color: #f7f9fc;
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            padding: 15px 20px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .schedule-details p {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
        }
        .no-schedules-message {
            text-align: center;
            color: #777;
            font-size: 18px;
            margin-top: 50px;
        }
        .schedule-actions {
            display: flex; /* Agar tombol berdampingan */
            gap: 10px; /* Jarak antar tombol */
        }
        .schedule-actions .button { /* Ubah dari button menjadi class button */
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none; /* Untuk link */
            display: inline-block; /* Agar link bisa diberi padding */
        }
        .schedule-actions .button.delete { /* Untuk tombol delete */
            background-color: #dc3545;
        }
        /* Tambahan styling untuk profil sidebar jika diperlukan */
        .profil {
            /* Sesuaikan gaya profil sidebar Anda */
            width: 250px;
            padding: 20px;
            background-color: #f0f2f5;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-left: 20px; /* Jarak dari konten utama */
        }
        .profil img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        .profile-info h3 {
            margin: 0 0 5px 0;
            color: #333;
        }
        .profile-info p {
            margin: 0 0 3px 0;
            color: #666;
            font-size: 0.9em;
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
            <li><a href="{{ route('konselor.feedback') }}"> Feedback</a><li></li>
            <li style="margin-top: 131%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        <h2>Hello {{ $user->name ?? $konselor->nama ?? 'Konselor' }},</h2>
        <h1>Daftar Jadwal Anda</h1>
    </div>

    <div class="main schedule-list-container">
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

        @forelse($jadwals as $jadwal)
            <div class="schedule-item">
                <div class="schedule-details">
                    <p><strong>Hari:</strong> {{ \Carbon\Carbon::parse($jadwal->hari)->format('d F Y') }}</p>
                    <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($jadwal->waktu)->format('H:i') }}</p>
                    <p><strong>Status:</strong> {{ $jadwal->status }}</p>
                </div>
                <div class="schedule-actions">
                    {{-- Tombol Edit: Link ke halaman edit jadwal --}}
                    <a href="{{ route('konselor.my_schedules.edit', $jadwal->id) }}" class="button">Edit</a>

                    {{-- Tombol Delete: Form tersembunyi dengan metode DELETE --}}
                    <form action="{{ route('konselor.my_schedules.destroy', $jadwal->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                        @csrf
                        @method('DELETE') {{-- Penting: Mengubah request menjadi DELETE --}}
                        <button type="submit" class="button delete">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="no-schedules-message">Anda belum memiliki jadwal yang terdaftar.</p>
            <p class="no-schedules-message">Silakan <a href="{{ route('konselor.my_schedules.create') }}">tambahkan jadwal baru</a>.</p>
        @endforelse
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