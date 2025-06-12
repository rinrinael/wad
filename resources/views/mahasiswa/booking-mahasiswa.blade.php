<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Jadwal Mahasiswa</title>
    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}">
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
            <li style="margin-top: 131%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        {{-- Menggunakan $user yang dikirim dari controller --}}
        <h2>Hello {{ $user->name ?? 'User' }},</h2>
        <h1>Silakan Buat Janji Temu!</h1>
    </div>

    <div class="main">
        <form action="{{ route('booking.submit') }}" method="POST">
            @csrf

            {{-- Menampilkan pesan sukses/error dari session --}}
            @if (session('success'))
                <div style="color: green; margin-bottom: 10px;">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div style="color: red; margin-bottom: 10px;">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div style="color: red; margin-bottom: 10px;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-row1">
                <div class="form-input">
                    <label for="mahasiswa-nim">NIM</label>
                    {{-- Menggunakan $user->student_id atau $mahasiswa->nim jika ada --}}
                    {{-- Ini diisi otomatis dan mungkin sebaiknya read-only --}}
                    <input type="text" id="mahasiswa-nim" name="nim"
                           value="{{ old('nim', $mahasiswa->nim ?? '') }}" readonly>
                </div>
                {{-- Hapus input "Name" karena tidak digunakan dalam proses booking --}}
                {{-- <div class="form-input">
                    <label for="mahasiswa-name">Name</label>
                    <input type="text" id="mahasiswa-name" name="name" placeholder="Your Name" value="{{ old('name', $user->name ?? '') }}">
                </div> --}}
            </div>

            <div class="form-row-schedule">
                <div class="form-input">
                    <label for="jadwal_id">Pilih Jadwal</label>
                    <select id="jadwal_id" name="jadwal_id" required>
                        <option value="">-- Pilih Jadwal Konseling --</option>
                        @forelse($jadwalsTersedia as $jadwal)
                            <option value="{{ $jadwal->id }}" {{ old('jadwal_id') == $jadwal->id ? 'selected' : '' }}>
                                {{ $jadwal->konselor->nama ?? 'Konselor Tanpa Nama' }} -
                                {{ \Carbon\Carbon::parse($jadwal->hari)->format('d F Y') }} -
                                {{ \Carbon\Carbon::parse($jadwal->waktu)->format('H:i') }}
                            </option>
                        @empty
                            <option value="" disabled>Tidak ada jadwal tersedia saat ini.</option>
                        @endforelse
                    </select>
                </div>
            </div>

            {{-- Hapus input Start Date, End Date, Start Time, End Time --}}
            {{-- Karena tanggal dan waktu diambil dari jadwal_id yang dipilih --}}
            {{-- <div class="form-row2"> ... </div> --}}
            {{-- <div class="form-row3"> ... </div> --}}

            <button type="submit" class="submit-button">Buat Booking</button>
        </form>
    </div>

    <div class="profil">
        <img src="{{ asset('images/profil.jpg') }}" alt="Profile Picture">
        <div class="profile-info">
            <h3>{{ $user->name ?? 'Nama Lengkap' }}</h3>
            <p>{{ $mahasiswa->nim ?? $user->student_id ?? 'NIM' }}</p>
            <p>{{ $mahasiswa->major ?? 'Program Studi' }}</p> {{-- Asumsi major ada di model Mahasiswa --}}
            <p>{{ $user->email ?? 'Email' }}</p>
        </div>
    </div>
</body>
</html>