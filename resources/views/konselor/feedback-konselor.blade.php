<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Konselor Dashboard - Feedback</title>
    {{-- Memuat file CSS eksternal konselor.css --}}
    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}" />

    {{-- Gaya CSS inline khusus untuk halaman feedback ini --}}
    <style>
    /*
    * Gaya di sini hanya untuk elemen-elemen di dalam area 'main' (konten utama feedback).
    * BODY, NAVIGATION-BAR, HEADER, MAIN (sebagai wrapper feedback), dan PROFIL
    * SEMUANYA HARUSNYA SUDAH DIATUR OLEH konselor.css.
    */

    /* Feedback Container di dalam .main (yang sekarang menjadi main content feedback) */
    /* Kita memberikan background putih, shadow, dan border-radius ke .main langsung */
    /* Jadi .feedback-container ini hanya perlu mengatur konten internalnya */
    .feedback-container {
        max-width: 100%; /* Agar mengisi lebar parent .main */
        padding: 0; /* Hapus padding agar tidak ada double padding dengan .main */
        background: none; /* Hapus background */
        box-shadow: none; /* Hapus bayangan */
        border-radius: 0; /* Hapus border-radius */
        margin: 0; /* Hapus margin */
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

    /* Styling untuk Setiap Kartu Feedback - MIRIP DENGAN .appointment-card */
    .feedback-card {
        background-color: #f7f9fc; /* Latar belakang abu-abu muda seperti appointment-card */
        border-radius: 10px; /* Sudut membulat seperti appointment-card */
        box-shadow: 0 3px 6px rgba(0,0,0,0.1); /* Bayangan seperti appointment-card */
        padding: 20px 25px; /* Padding di dalam kartu seperti appointment-card */
        margin-bottom: 15px; /* Jarak bawah antar kartu */
        transition: transform 0.2s ease, box-shadow 0.2s ease; /* Transisi hover */
        cursor: default;
        border: none; /* Pastikan tidak ada border tambahan */
    }

    .feedback-card:hover {
        box-shadow: 0 8px 15px rgba(0,0,0,0.15); /* Hover effect */
        transform: translateY(-3px); /* Hover effect */
    }

    .feedback-card p {
        margin: 5px 0; /* Margin antar baris informasi */
        font-size: 16px;
        color: #555;
        font-weight: 500;
    }

    .feedback-card p strong {
        color: #222; /* Warna label bold */
        font-weight: 700; /* Ketebalan font label */
        display: inline-block; /* Pastikan strong di baris yang sama */
        min-width: 120px; /* Lebar minimum untuk label agar rapi */
        vertical-align: top; /* Rata atas jika konten komentar panjang */
    }

    /* Gaya khusus untuk label "Nama mahasiswa" agar lebih menonjol, seperti p:first-child di appointment-card */
    .feedback-card p:first-child {
        font-weight: 700;
        color: #222;
        font-size: 18px;
    }
    .feedback-card p:first-child strong {
        font-size: 18px; /* Pastikan strong juga ikut membesar */
    }

    .feedback-card .komentar-text {
        display: block; /* Agar komentar di baris baru */
        margin-top: 5px; /* Jarak dari label "Komentar:" */
        background-color: #fff; /* Latar belakang komentar putih */
        border: 1px solid #ccc; /* Border seperti input */
        padding: 10px;
        border-radius: 5px;
        white-space: pre-wrap; /* Mempertahankan spasi dan baris baru */
        word-wrap: break-word; /* Memastikan teks komentar tidak meluber */
        min-height: 80px; /* Tinggi minimum area komentar */
        color: #333; /* Warna teks komentar */
    }

    .rating-stars {
        color: gold; /* Warna bintang rating */
        font-size: 24px; /* Ukuran font bintang */
        letter-spacing: 2px; /* Jarak antar bintang */
    }
    .rating-stars .star-empty {
        color: #ccc; /* Warna bintang kosong */
    }

    .feedback-meta {
        font-size: 13px;
        color: #888;
        margin-top: 15px;
        text-align: right;
        border-top: 1px dashed #e0e0e0; /* Garis putus-putus */
        padding-top: 10px;
    }

    .no-feedbacks-message {
        text-align: center;
        color: #777;
        font-size: 18px;
        margin-top: 50px;
        background-color: #fff; /* Background untuk pesan jika tidak ada feedback */
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 3px 6px rgba(0,0,0,0.05); /* Bayangan seperti card */
    }

    /*
    * Catatan: Media queries untuk responsifitas harusnya sudah ada di konselor.css.
    * Jika tidak ada, Anda perlu menambahkannya ke konselor.css Anda, bukan di sini.
    */
    </style>
</head>
<body>

    {{-- Sidebar Navigasi Kiri - Menggunakan gaya dari konselor.css --}}
    <div class="navigation-bar">
        <div class="row">
            <img src="{{ asset('images/mindmeet.png') }}" alt="MindMeet Logo" class="logo">
            <h2>MindMeet</h2>
        </div>

        <ul>
            <li><a href="{{ route('konselor.dashboard') }}">Home</a></li>
            <li><a href="{{ route('konselor.my_schedules.create') }}">Input Jadwal</a></li>
            <li><a href="{{ route('konselor.my_schedules') }}">My Schedules</a></li>
            {{-- Tandai 'Feedback' sebagai aktif --}}
            <li><a href="{{ route('konselor.feedback') }}" class="active"> Feedback</a><li></li>
            <li style="margin-top: 131%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    {{-- HEADER - Menggunakan gaya dari konselor.css --}}
    <div class="header">
        <h2>Hello {{ $user->name ?? ($konselor->user->name ?? 'Konselor') }},</h2>
        <h1>Feedback</h1>
    </div>

    {{-- MAIN CONTENT FEEDBACK - Menggunakan gaya dari konselor.css untuk penempatan Grid,
         dan gaya inline untuk konten di dalamnya --}}
    <div class="main">
        <div class="feedback-container">
            {{-- Ini adalah bagian konten yang spesifik untuk Feedback --}}
            <div class="feedback-title">Feedback</div>
            <div class="list-feedback-subtitle">List Feedback</div>

            @forelse($feedbacks as $feedback)
                <div class="feedback-card">
                    <p><strong>Nama mahasiswa:</strong> {{ $feedback->mahasiswa->user->name ?? 'Mahasiswa Tidak Diketahui' }}</p>
                    <p><strong>NIM:</strong> {{ $feedback->mahasiswa->nim ?? 'NIM Tidak Diketahui' }}</p>
                    <p><strong>Jam Pertemuan:</strong> {{ $feedback->jam_pertemuan }}</p>
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
                </div>
            @empty
                <p class="no-feedbacks-message">Belum ada feedback yang diberikan.</p>
            @endforelse
        </div>
    </div>

    {{-- Profil Konselor (Sidebar Kanan) - Menggunakan gaya dari konselor.css --}}
    <div class="profil">
        <img src="{{ asset('images/profil.jpg') }}" alt="Profil Konselor">
        <div class="profile-info">
            <h3>{{ $user->name ?? ($konselor->user->name ?? 'Nama Konselor') }}</h3>
            <p>{{ $konselor->spesialisasi ?? 'Spesialisasi Konselor' }}</p>
            <p>{{ $user->email ?? 'Email Konselor' }}</p>
        </div>
    </div>

</body>
</html>