<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mahasiswa Dashboard - My Bookings</title>

    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}" />

    <style>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .appointment-info {
            flex-grow: 1;
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

        /* --- PERBAIKAN: Gaya untuk Status Badge --- */
        .appointment-status {
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.85em;
            font-weight: bold;
            color: white;
            display: inline-block;
            text-transform: capitalize; /* Otomatis kapitalisasi huruf pertama */
        }
        .appointment-status.booked { background-color: #007bff; }
        .appointment-status.completed { background-color: #28a745; }
        .appointment-status.cancelled { background-color: #dc3545; }
        /* --- AKHIR PERBAIKAN STATUS BADGE --- */

        /* --- PERBAIKAN: Gaya untuk Tombol Cancel --- */
        .cancel-button {
            background-color: #dc3545; /* Merah */
            color: white;
            border: 1px solid transparent;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease, transform 0.1s ease;
            white-space: nowrap;
            min-width: 100px;
            text-align: center;
            
            /* Reset default browser styling */
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            margin: 0;
            outline: none;
        }
        .cancel-button:hover {
            background-color: #c82333;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .cancel-button:active {
            transform: translateY(0);
            box-shadow: none;
        }
        .cancel-button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
            opacity: 0.7;
        }
        /* --- AKHIR PERBAIKAN TOMBOL CANCEL --- */
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
        <h2>Hello {{ $user->name ?? 'Mahasiswa' }},</h2>
        <h1>Good Morning!</h1>
    </div>

    <div class="main">
        <h2 style="margin-bottom: 20px;">My Bookings</h2>

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

        @forelse($appointments as $booking)
            <div class="appointment-card">
                <div class="appointment-info">
                    <p><strong>ID Booking:</strong> {{ $booking->id }}</p>
                    <p><strong>Konselor:</strong> {{ $booking->jadwal->konselor->nama ?? 'N/A' }}</p>
                    <p><strong>Spesialisasi:</strong> {{ $booking->jadwal->konselor->spesialisasi ?? 'N/A' }}</p>
                    <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($booking->jadwal->hari)->format('d F Y') }}</p>
                    <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($booking->jadwal->waktu)->format('H:i') }}</p>
                    <p><strong>Status:</strong> <span class="appointment-status {{ $booking->status }}">{{ ucfirst($booking->status) }}</span></p>
                </div>
                <div>
                    {{-- Tombol Cancel Booking --}}
                    @if ($booking->status == 'booked')
                        <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan booking ini?');">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="cancel-button">Cancel Booking</button>
                        </form>
                    @else
                        <button type="button" class="cancel-button" disabled>Booking {{ ucfirst($booking->status) }}</button>
                    @endif
                </div>
            </div>
        @empty
            <p>Anda belum memiliki janji temu yang akan datang.</p>
        @endforelse
    </div>

    <div class="profil">
        <img src="{{ asset('images/profil.jpg') }}" alt="Foto Profil">
        <div class="profile-info">
            <h3>{{ $user->name ?? 'Mahasiswa' }}</h3>
            <p>{{ $user->mahasiswa->nim ?? 'NIM' }}</p>
            <p>{{ $user->email ?? 'mahasiswa@example.com' }}</p>
        </div>
    </div>
</body>
</html>