<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Counselor</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .appointment-info {
            flex: 1;
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

        /* --- PERBAIKAN: Gaya untuk Button Group dan Tombol di dalamnya --- */
        .button-group {
            display: flex;
            gap: 10px; /* Jarak antar tombol */
            flex-shrink: 0; /* Mencegah group tombol menyusut jika ruang terbatas */
        }

        /* Gaya umum untuk semua tombol di dalam .button-group */
        /* Ini adalah gaya yang sama yang kita gunakan di admin/appointments/index.blade.php */
        .button-group .button {
            background-color: #007bff; /* Warna biru untuk Edit */
            color: white;
            border: 1px solid transparent; /* Border konsisten */
            padding: 8px 12px; /* Padding konsisten */
            border-radius: 5px; /* Bentuk konsisten */
            cursor: pointer;
            text-decoration: none; /* Untuk link */
            
            /* Untuk mempusatkan teks di dalam tombol */
            display: inline-flex;
            align-items: center;
            justify-content: center;
            
            /* Konsistensi ukuran dan font */
            font-size: 16px;
            font-family: inherit;
            line-height: 1.2;
            height: auto;
            box-sizing: border-box;
            white-space: nowrap; /* Mencegah teks melipat */
            min-width: 70px; /* Lebar minimum konsisten */
            text-align: center;
            
            /* Reset gaya bawaan browser */
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            margin: 0; /* Pastikan tidak ada margin bawaan browser */
            outline: none; /* Hilangkan outline fokus */

            transition: background-color 0.2s ease, transform 0.1s ease, box-shadow 0.2s ease; /* Efek transisi */
        }

        /* Gaya spesifik untuk tombol Delete */
        .button-group .button.delete {
            background-color: #dc3545; /* Warna merah untuk Hapus */
        }

        /* Efek hover untuk semua tombol */
        .button-group .button:hover {
            filter: brightness(110%); /* Mencerahkan sedikit */
            transform: translateY(-1px); /* Efek sedikit naik */
            box-shadow: 0 2px 5px rgba(0,0,0,0.2); /* Bayangan saat di-hover */
        }
        .button-group .button:active {
            transform: translateY(0); /* Kembali ke posisi semula saat diklik */
            box-shadow: none;
        }
        /* --- AKHIR PERBAIKAN --- */
        
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
        <h1>Good Morning!</h1>
    </div>

    <div class="main">
        <h2 style="margin-bottom: 20px; margin-top: 5px;">List Counselor</h2>

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

        {{-- Loop Data Counselor --}}
        @foreach ($counselors as $counselor)
            <div class="appointment-card">
                <div class="appointment-info">
                    <p><strong>Nama:</strong> {{ $counselor->nama }}</p>
                    <p><strong>Spesialisasi:</strong> {{ $counselor->spesialisasi }}</p>
                    <p><strong>Email:</strong> {{ $counselor->email }}</p>
                </div>
                <div class="button-group">
                    {{-- PERBAIKAN: Hapus style inline dan tambahkan class="button" --}}
                    <a href="{{ route('counselor.edit', $counselor->id) }}" class="button">
                        Edit
                    </a>
                    {{-- PERBAIKAN: Hapus style inline dan tambahkan class="button delete" --}}
                    <form action="{{ route('counselor.destroy', $counselor->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus counselor ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button delete">Hapus</button>
                    </form>
                </div>
            </div>
        @endforeach
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