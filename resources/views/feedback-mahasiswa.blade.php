<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Feedback - MindMeet</title>
    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}" />
    <style>
        /* CSS Tambahan Khusus untuk Halaman Feedback Form */

        /* Atur lebar dan posisi form feedback box */
        .feedback-form-box {
            background-color: rgb(240, 240, 240);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            max-width: 950px; /* Sesuaikan lebar maksimal form */
            /* Margin kiri untuk mengimbangi navigation-bar,
               pastikan ini tidak bertabrakan dengan margin dari .main jika sudah ada */
            /* margin-left: 250px; Sesuaikan dengan lebar navigation-bar */
            margin-bottom: 30px; /* Jarak bawah agar tidak terlalu mepet */
        }

        .form-row {
            margin-bottom: 15px;
        }

        .form-row label {
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
            color: #444;
        }

        .form-row input[type="text"],
        .form-row select,
        .form-row textarea {
            width: auto; /* Hampir 100% dengan padding */
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        /* Gaya untuk input yang readonly/disabled agar terlihat berbeda */
        .form-row input[readonly],
        .form-row input[disabled] {
            background-color: #e9ecef; /* Warna abu-abu terang */
            cursor: default; /* Kursor tidak berubah menjadi teks */
        }

        .btn-feedback {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px; /* Jarak atas untuk tombol */
        }

        .btn-feedback:hover {
            background-color: #111;
        }

        /* Gaya untuk rating bintang */
        #star-rating .star {
            font-size: 24px;
            color: #ccc; /* Warna default bintang (abu-abu) */
            cursor: pointer;
            transition: color 0.2s; /* Transisi warna agar lebih halus */
        }

        #star-rating .star.selected {
            color: gold; /* Warna bintang terpilih */
        }

        /* Gaya pesan error dari validasi Laravel */
        .error-message {
            color: red;
            font-size: 0.8em;
            margin-top: 5px;
        }

        /* Sesuaikan posisi .main agar tidak tertutup navigation-bar */

    </style>
</head>
<body>

    {{-- Struktur navigation-bar, header, dan profil dari home-mahasiswa.blade.php Anda --}}
    <div class="navigation-bar">
        <div class="row">
            <img src="{{ asset('images/mindmeet.png') }}" alt="MindMeet Logo" class="logo">
            <h2>MindMeet</h2>
        </div>

        <ul>
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('schedule') }}">Schedule</a></li>
            <li><a href="{{ route('appointment') }}">Appointment</a></li>
            <li><a href="{{ route('feedback') }}" class="active">Feedback</a></li>
            <li style="margin-top: 131%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        <h2>Hello {{ $user->name ?? 'User' }},</h2>
        <h1>Give Feedback</h1> {{-- Judul halaman ini --}}
    </div>

    <div class="main">
        {{-- Konten utama form feedback --}}
        <div class="feedback-form-box">
            <form action="{{ route('feedback.store') }}" method="POST">
                @csrf

                <div class="form-row">
                    <label for="booking_id">Pilih Riwayat Konseling (Completed):</label>
                    <select id="booking_id" name="booking_id" required>
                        <option value="">-- Pilih Konseling yang Selesai --</option>
                        @forelse($completedBookings as $booking)
                            <option value="{{ $booking->id }}"
                                data-konselor-name="{{ $booking->jadwal->konselor->user->name ?? 'N/A' }}"
                                data-jam-pertemuan="{{ \Carbon\Carbon::parse($booking->jadwal->waktu)->format('H:i') }}"
                                {{ old('booking_id') == $booking->id ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($booking->tanggal)->format('d M Y') }} -
                                {{ \Carbon\Carbon::parse($booking->jadwal->waktu)->format('H:i') }} -
                                {{ $booking->jadwal->konselor->user->name ?? 'N/A' }}
                            </option>
                        @empty
                            <option value="" disabled>Tidak ada riwayat konseling yang siap diberi feedback.</option>
                        @endforelse
                    </select>
                    @error('booking_id')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Input baca-saja untuk menampilkan info konselor dan jam --}}
                <div class="form-row">
                    <label for="display_konselor_name">Konselor Terpilih:</label>
                    <input type="text" id="display_konselor_name" readonly disabled placeholder="Pilih riwayat konseling di atas">
                </div>

                <div class="form-row">
                    <label for="display_jam_pertemuan">Jam Konseling:</label>
                    <input type="text" id="display_jam_pertemuan" readonly disabled placeholder="Pilih riwayat konseling di atas">
                </div>

                <button type="button" class="btn-feedback" id="show-feedback">Berikan Feedback</button>

                <div id="feedback-extra" style="display: none; margin-top: 20px;">
                    <div class="form-row">
                        <label for="comment">Komentar:</label>
                        <textarea id="comment" name="comment" rows="4" placeholder="Tulis komentarmu...">{{ old('comment') }}</textarea>
                        @error('comment')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <label for="rating">Rating:</label>
                        <div id="star-rating">
                            <span class="star" data-value="1">&#9733;</span>
                            <span class="star" data-value="2">&#9733;</span>
                            <span class="star" data-value="3">&#9733;</span>
                            <span class="star" data-value="4">&#9733;</span>
                            <span class="star" data-value="5">&#9733;</span>
                        </div>
                        <input type="hidden" id="rating" name="rating" value="{{ old('rating', 0) }}">
                        @error('rating')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-feedback">Kirim Feedback</button>
                </div>
            </form>
        </div>
    </div>

    <div class="profil">
        <img src="{{ asset('images/profil.jpg') }}" alt="Profile Picture">
        <div class="profile-info">
            <h3>{{ $user->name ?? 'Nama Lengkap' }}</h3>
            <p>{{ $user->mahasiswa->major ?? 'Program Studi' }}</p>
            <p>{{ $user->mahasiswa->nim ?? 'NIM' }}</p>
            <p>{{ $user->email ?? 'Email' }}</p>
        </div>
    </div>

    {{-- Script SweetAlert2 dan JavaScript Kustom --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const showButton = document.getElementById('show-feedback');
            const feedbackExtra = document.getElementById('feedback-extra');
            const bookingSelect = document.getElementById('booking_id');
            const displayKonselorNameInput = document.getElementById('display_konselor_name');
            const displayJamPertemuanInput = document.getElementById('display_jam_pertemuan');

            // Fungsi untuk mengupdate input konselor dan jam
            function updateDisplayInputs() {
                const selectedOption = bookingSelect.options[bookingSelect.selectedIndex];
                if (selectedOption && selectedOption.value !== "") {
                    displayKonselorNameInput.value = selectedOption.getAttribute('data-konselor-name');
                    displayJamPertemuanInput.value = selectedOption.getAttribute('data-jam-pertemuan');
                } else {
                    displayKonselorNameInput.value = '';
                    displayJamPertemuanInput.value = '';
                }
            }

            // Panggil saat halaman dimuat (untuk kasus old() error atau refresh)
            updateDisplayInputs();

            // Event listener saat pilihan booking berubah
            bookingSelect.addEventListener('change', updateDisplayInputs);

            showButton.addEventListener('click', function (event) {
                event.preventDefault();
                if (bookingSelect.value === "") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: 'Silakan pilih riwayat konseling yang sudah selesai terlebih dahulu.',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                feedbackExtra.style.display = 'block';
                showButton.style.display = 'none';
            });

            // Bagian JavaScript untuk Rating Bintang
            const stars = document.querySelectorAll('.star');
            const ratingInput = document.getElementById('rating');

            stars.forEach(star => {
                star.addEventListener('click', () => {
                    const value = star.getAttribute('data-value');
                    ratingInput.value = value;

                    stars.forEach(s => {
                        s.classList.toggle('selected', s.getAttribute('data-value') <= value);
                    });
                });
            });

            // SweetAlert2 untuk pesan sukses/error
            @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
            @endif

            @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                html: '{!! implode('<br>', $errors->all()) !!}',
                confirmButtonColor: '#d33',
                confirmButtonText: 'OK'
            });
            @endif
        });
    </script>
</body>
</html>