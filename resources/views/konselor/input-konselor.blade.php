<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Jadwal Konselor</title> {{-- Ubah judul --}}
    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}">
</head>
<body>
    <div class="navigation-bar">
        <div class="row">
            <img src="{{ asset('images/mindmeet.png') }}" alt="MindMeet Logo" class="logo">
            <h2>MindMeet</h2>
        </div>

        <ul>
            {{-- Sesuaikan rute untuk Konselor --}}
            <li><a href="{{ route('konselor.dashboard') }}">Home</a></li> {{-- Ini harus jadi rute dashboard konselor --}}
            <li><a href="{{ route('konselor.my_schedules') }}">My Schedules</a></li> {{-- Ubah ini jadi lihat jadwal sendiri --}}
            <li><a href="{{ route('konselor.my_schedules.store') }}">Input Jadwal</a></li> {{-- Ubah ini jadi input jadwal --}}
            <li><a href="{{ route('konselor.feedback') }}"> Feedback</a><li></li>
            {{-- Jika ada edit booking, tambahkan rute yang sesuai --}}
            {{-- <li><a href="{{ route('booking.edit') }}">Edit Booking</a></li> --}}
            <li style="margin-top: 131%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        {{-- Gunakan data dinamis dari controller --}}
        <h2>Hello {{ $user->name ?? 'Konselor' }},</h2>
        <h1>Input Jadwal Baru Anda</h1> {{-- Ubah judul halaman --}}
    </div>

    <div class="main">
        {{-- Form action diarahkan ke rute JadwalController@store --}}
        <form action="{{ route('konselor.my_schedules.store') }}" method="POST">
            @csrf

            {{-- Tampilkan pesan sukses/error dari session --}}
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
                    <label for="konselor-id">ID Konselor Anda</label>
                    {{-- ID konselor diisi otomatis dari $konselor->id dan readonly --}}
                    <input type="text" id="konselor-id" name="konselor_id"
                           value="{{ old('konselor_id', $konselor->id ?? '') }}" readonly>
                </div>
                {{-- Input 'Konselor Name' dihapus karena tidak relevan --}}
                {{-- <div class="form-input">
                    <label for="konselor-name">Name</label>
                    <input type="text" id="konselor-name" name="konselor_name" placeholder="Konselor Name" value="{{ old('konselor_name') }}">
                </div> --}}
            </div>

            <div class="form-row2">
                <div class="form-input">
                    <label for="hari">Tanggal Jadwal</label>
                    {{-- Sesuai dengan kolom 'hari' di tabel jadwal --}}
                    <input type="date" id="hari" name="hari" value="{{ old('hari') }}" required>
                </div>
                <div class="form-input">
                    <label for="waktu">Waktu Mulai Jadwal</label>
                    {{-- Sesuai dengan kolom 'waktu' di tabel jadwal --}}
                    <input type="time" id="waktu" name="waktu" value="{{ old('waktu') }}" required>
                </div>
            </div>

            <div class="form-row3">
                <div class="form-input">
                    <label for="status">Status Jadwal</label>
                    {{-- Tambahkan input untuk status (misal: 'available') --}}
                    <select id="status" name="status" required>
                        <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="not_available" {{ old('status') == 'not_available' ? 'selected' : '' }}>Not Available</option>
                        {{-- 'booked' seharusnya diatur otomatis oleh sistem, bukan konselor --}}
                    </select>
                </div>
            </div>

            <button type="submit" class="submit-button">Simpan Jadwal</button>
        </form>
    </div>

    <div class="profil">
        <img src="{{ asset('images/profil.jpg') }}" alt="Profil Konselor">
        <div class="profile-info">
            {{-- Gunakan data dinamis dari $user dan $konselor --}}
            <h3>{{ $konselor->nama ?? $user->name ?? 'Nama Konselor' }}</h3>
            <p>{{ $konselor->spesialisasi ?? 'Spesialisasi' }}</p> {{-- Asumsi ada kolom spesialisasi di Konselor --}}
            <p>{{ $user->email ?? 'Email Konselor' }}</p>
        </div>
    </div>
</body>
</html>