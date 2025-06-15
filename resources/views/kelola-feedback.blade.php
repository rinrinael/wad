<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Dashboard - Kelola Feedback</title>
    {{-- Memuat file CSS eksternal admin.css (Anda mungkin perlu membuat ini jika belum ada) --}}
    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}" />

    {{-- Gaya CSS inline khusus untuk halaman kelola feedback ini --}}
    <style>

    .feedback-container {
        max-width: 100%;
        padding: 0;
        background: none;
        box-shadow: none;
        border-radius: 0;
        margin: 0;
    }

    /* Judul dan Subjudul di dalam Kontainer Feedback */
    .feedback-title {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 10px;
        color: #333;
        text-align: left;
    }

    .list-feedback-subtitle {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 20px;
        color: #333;
        text-align: left;
    }

    /* Styling untuk Setiap Kartu Feedback - MIRIP DENGAN feedback-card konselor */
    .feedback-card {
        background-color: #f7f9fc;
        border-radius: 10px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        padding: 20px 25px;
        margin-bottom: 15px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: default;
        border: none;
    }

    .feedback-card:hover {
        box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        transform: translateY(-3px);
    }

    .feedback-card p {
        margin: 5px 0;
        font-size: 16px;
        color: #555;
        font-weight: 500;
    }

    .feedback-card p strong {
        color: #222;
        font-weight: 700;
        display: inline-block;
        min-width: 120px;
        vertical-align: top;
    }

    /* Gaya khusus untuk label "Nama mahasiswa" agar lebih menonjol */
    .feedback-card p:first-child {
        font-weight: 700;
        color: #222;
        font-size: 18px;
    }
    .feedback-card p:first-child strong {
        font-size: 18px;
    }

    .feedback-card .komentar-text {
        display: block;
        margin-top: 5px;
        background-color: #fff;
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 5px;
        white-space: pre-wrap;
        word-wrap: break-word;
        min-height: 80px;
        color: #333;
    }

    .rating-stars {
        color: gold;
        font-size: 24px;
        letter-spacing: 2px;
    }
    .rating-stars .star-empty {
        color: #ccc;
    }

    .feedback-meta {
        font-size: 13px;
        color: #888;
        margin-top: 15px;
        text-align: right;
        border-top: 1px dashed #e0e0e0;
        padding-top: 10px;
    }

    .no-feedbacks-message {
        text-align: center;
        color: #777;
        font-size: 18px;
        margin-top: 50px;
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.05);
    }

    /* Styling untuk tombol aksi (Edit, Hapus) */
    .action-buttons {
        margin-top: 15px;
        text-align: right; /* Pindahkan tombol ke kanan */
    }

    .action-buttons a,
    .action-buttons button {
        padding: 8px 15px;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.1s ease;
        text-decoration: none;
        display: inline-block; /* Agar bisa diatur margin */
        margin-left: 10px; /* Jarak antar tombol */
        border: none;
        text-align: center;
    }

    .action-buttons .edit-button {
        background-color: #4CAF50; /* Hijau */
        color: white;
    }

    .action-buttons .edit-button:hover {
        background-color: #45a049;
        transform: translateY(-1px);
    }

    .action-buttons .delete-button {
        background-color: #F44336; /* Merah */
        color: white;
    }

    .action-buttons .delete-button:hover {
        background-color: #da190b;
        transform: translateY(-1px);
    }

    /* Styling untuk pesan success/error */
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        border: 1px solid transparent;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border-color: #c3e6cb;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }

    /*
     * Catatan: Media queries untuk responsifitas harusnya sudah ada di admin.css.
     * Jika tidak ada, Anda perlu menambahkannya ke admin.css Anda, bukan di sini.
     */
    </style>
</head>
<body>

    {{-- Sidebar Navigasi Kiri - Asumsi menggunakan gaya dari admin.css --}}
    <div class="navigation-bar">
        <div class="row">
            <img src="{{ asset('images/mindmeet.png') }}" alt="MindMeet Logo" class="logo">
            <h2>MindMeet</h2>
        </div>

        <ul>
            {{-- Anda bisa menyesuaikan menu navigasi admin di sini --}}
            <li><a href="{{ route('manage.booking') }}">Manage Booking</a></li>
            <li><a href="{{ route('manage.counselor') }}">Manage Counselor</a></li>
            <li><a href="{{ route('manage.schedule') }}">Manage Schedule</a></li>
            <li><a href="{{ route('manage.student') }}">Manage Student</a></li>
            <li><a href="{{ route('manage.feedback') }}" class="active"> Manage Feedback</a></li>
            <li style="margin-top: 114%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    {{-- HEADER - Asumsi menggunakan gaya dari admin.css --}}
    <div class="header">
        {{-- Asumsi Anda memiliki variabel $user atau $admin yang tersedia di view admin --}}
        <h2>Hello {{ Auth::user()->name ?? 'Admin' }},</h2>
        <h1>Kelola Feedback</h1>
    </div>

    {{-- MAIN CONTENT - Asumsi menggunakan gaya dari admin.css untuk penempatan Grid --}}
    <div class="main">
        <div class="feedback-container">
            {{-- Pesan Sukses atau Error --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="feedback-title">Kelola Feedback</div>
            <div class="list-feedback-subtitle">Daftar Semua Feedback</div>

            @forelse($feedbacks as $feedback)
                <div class="feedback-card">
                    <p><strong>Nama Mahasiswa:</strong> {{ $feedback->mahasiswa->user->name ?? 'Mahasiswa Tidak Diketahui' }}</p>
                    <p><strong>NIM:</strong> {{ $feedback->mahasiswa->nim ?? 'NIM Tidak Diketahui' }}</p>
                    <p><strong>Konselor:</strong> {{ $feedback->konselor->user->name ?? 'Konselor Tidak Diketahui' }}</p>
                    {{-- Pastikan `booking` dan `jadwal` di-load untuk mengakses waktu pertemuan --}}
                    <p><strong>Waktu Pertemuan:</strong> {{ $feedback->booking->jadwal->tanggal->format('d M Y') ?? 'N/A' }}, {{ \Carbon\Carbon::parse($feedback->booking->jadwal->waktu)->format('H:i') ?? 'N/A' }}</p>
                    <p><strong>Komentar:</strong>
                        <span class="komentar-text">{{ $feedback->komentar }}</span>
                    </p>
                    <p><strong>Rating:</strong>
                        <span class="rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                {!! $i <= $feedback->rating ? '&#9733;' : '<span class="star-empty">&#9733;</span>' !!}
                            @endfor
                        </span>
                    </p>
                    <div class="feedback-meta">
                        Diberikan pada: {{ $feedback->created_at->format('d F Y, H:i') }}
                    </div>
                    <div class="action-buttons">
                        <a href="{{ route('feedback.edit', $feedback->id) }}" class="edit-button">Edit</a>
                        <form action="{{ route('feedback.destroy', $feedback->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus feedback ini?')" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-button">Hapus</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="no-feedbacks-message">Belum ada feedback yang tersedia untuk dikelola.</p>
            @endforelse
        </div>
    </div>

    {{-- Profil Admin (Sidebar Kanan) - Asumsi menggunakan gaya dari admin.css --}}
    <div class="profil">
        {{-- Anda bisa menampilkan info profil admin di sini --}}
        <img src="{{ asset('images/profil.jpg') }}" alt="Profil Admin"> {{-- Ganti dengan gambar profil admin --}}
        <div class="profile-info">
            <h3>{{ Auth::user()->name}}</h3>
            <p>{{ Auth::user()->email}}</p>
        </div>
    </div>

</body>
</html>