<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - List Appointments</title>
    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}">
    <style>
        /* Styling untuk daftar appointments */
        .appointment-list-container {
            margin-top: 20px;
            padding: 0 20px;
        }
        .appointment-item {
            background-color: #f7f9fc;
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            padding: 15px 20px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .appointment-details p {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
        }
        .no-appointments-message {
            text-align: center;
            color: #777;
            font-size: 18px;
            margin-top: 50px;
        }
        .appointment-actions {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }

        /* --- PERBAIKAN KRUSIAL UNTUK KONSISTENSI BENTUK TOMBOL --- */
        /* Gaya umum untuk tombol Aksi (Edit, Delete) */
        .appointment-actions .button {
            background-color: #007bff; /* Warna biru untuk Edit */
            color: white;
            /* --- Gaya yang Lebih Agresif untuk Konsistensi --- */
            border: 1px solid transparent; /* Pastikan border konsisten */
            padding: 8px 12px; /* Padding yang menentukan ukuran utama */
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            
            /* Flexbox untuk konten di tengah tombol */
            display: inline-flex;
            align-items: center; /* Pusatkan teks secara vertikal */
            justify-content: center; /* Pusatkan teks secara horizontal */
            
            /* Ukuran font dan properti teks */
            font-size: 16px;
            font-family: inherit; /* Warisi font dari body/global */
            line-height: 1.2; /* Line height konsisten */
            height: auto; /* Biarkan tinggi diatur oleh padding dan content */
            box-sizing: border-box; /* Pastikan perhitungan box model konsisten */
            white-space: nowrap; /* Mencegah teks melipat (wrap) */
            min-width: 70px; /* Beri lebar minimum agar ukuran lebih seragam */
            text-align: center; /* Pastikan teks di tengah */
            
            /* Reset gaya bawaan browser secara agresif */
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            margin: 0; /* Pastikan tidak ada margin bawaan browser */
            outline: none; /* Hilangkan outline fokus browser */

            transition: background-color 0.2s ease, transform 0.1s ease, box-shadow 0.2s ease;
        }

        /* Gaya spesifik untuk tombol Delete */
        .appointment-actions .button.delete {
            background-color: #dc3545; /* Warna merah untuk Delete */
        }

        /* Efek hover */
        .appointment-actions .button:hover {
            filter: brightness(110%);
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .appointment-actions .button:active {
            transform: translateY(0);
            box-shadow: none;
        }
        /* --- AKHIR PERBAIKAN KRUSIAL --- */

        .appointment-status {
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.85em;
            font-weight: bold;
            color: white;
        }
        .appointment-status.booked { background-color: #007bff; }
        .appointment-status.completed { background-color: #28a745; }
        .appointment-status.cancelled { background-color: #dc3545; }

        /* Styling profil sidebar */
        .profil {
            width: 250px;
            padding: 20px;
            background-color: #f0f2f5;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-left: 20px;
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
            <li><a href="{{ route('manage.booking') }}">Manage Booking</a></li>
            <li><a href="{{ route('manage.counselor') }}">Manage Counselor</a></li>
            <li><a href="{{ route('manage.schedule') }}">Manage Schedule</a></li>
            <li><a href="{{ route('manage.student') }}">Manage Student</a></li>
            <li><a href="{{ route('manage.feedback') }}" class="active"> Manage Feedback</a></li>
            <li style="margin-top: 131%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        <h2>Hello Admin,</h2>
        <h1>Good Morning!</h1>
    </div>

    <div class="main appointment-list-container">
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

        <h2 style="margin-bottom: 20px;">List Appointments</h2>

        @forelse($appointments as $appointment)
            <div class="appointment-item">
                <div class="appointment-details">
                    <p><strong>ID Booking:</strong> {{ $appointment->id }}</p>
                    <p><strong>Mahasiswa:</strong> {{ $appointment->mahasiswa->nama ?? 'N/A' }} (NIM: {{ $appointment->mahasiswa->nim ?? 'N/A' }})</p>
                    <p><strong>Konselor:</strong> {{ $appointment->jadwal->konselor->nama ?? 'N/A' }}</p>
                    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($appointment->jadwal->hari)->format('d F Y') }}</p>
                    <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($appointment->jadwal->waktu)->format('H:i') }}</p>
                    <p><strong>Status:</strong> <span class="appointment-status {{ $appointment->status }}">{{ ucfirst($appointment->status) }}</span></p>
                </div>
                <div class="appointment-actions">
                    <a href="{{ route('appointments.edit', $appointment->id) }}" class="button">Edit</a>

                    <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus appointment ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button delete">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="no-appointments-message">Tidak ada appointments yang terdaftar.</p>
        @endforelse
    </div>

    <div class="profil">
        <img src="{{ asset('images/profil.jpg') }}" alt="Foto Profil Admin">
        <div class="profile-info">
            <h3>{{ Auth::user()->name ?? 'Admin' }}</h3>
            <p>{{ Auth::user()->email ?? 'admin@gmail.com' }}</p>
        </div>
    </div>

</body>
</html>