<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Form - MindMeet</title>
    <style>
        /* --- General Body Styles --- */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #cf3f3f;
            background-image: url("{{ asset('images/tult-telyu.png') }}");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: #303841;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* --- Container for Login/Register Form --- */
        .container {
            display: flex;
            background-color: rgba(238, 238, 238, 0.95);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            padding: 50px;
            width: 75%;
            max-width: 900px;
            height: auto;
            box-sizing: border-box;
        }

        /* --- Left Image Section --- */
        .image-section {
            flex: 1;
            background: url('{{ asset('images/mindmeet.png') }}') no-repeat center center;
            background-size: contain;
            margin-right: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 200px;
        }

        /* --- Right Form Section --- */
        .form-section {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #303841;
        }

        /* --- Form Header --- */
        .form-section h2 {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            justify-content: center;
            color: #cf3f3f;
            font-size: 2em;
        }

        /* --- Form Group Styling (for input and select) --- */
        .form-group {
            margin-bottom: 18px;
            width: 100%;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: none;
            background-color: #eee;
            border-bottom: 2px solid #b3b5b7;
            color: #303841;
            box-sizing: border-box;
            border-radius: 5px 5px 0 0;
        }

        .form-group input::placeholder {
            color: #30384199;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #cf3f3f;
            box-shadow: 0 0 0 2px rgba(207, 63, 63, 0.3);
        }

        .form-group label {
            color: #303841;
            font-size: 0.9em;
            margin-bottom: 5px;
            display: block;
            font-weight: bold;
        }

        /* --- Submit Button --- */
        .submit-button {
            background-color: #cf3f3f;
            color: #eee;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            width: 100%;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }

        .submit-button:hover {
            background-color: #a02a2a;
        }

        /* --- Alternative Login Link --- */
        .alt-login {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 15px;
            width: 100%;
        }

        .alt-login label {
            color: #585a5c;
            font-size: 0.9em;
        }

        .alt-login a {
            color: #cf3f3f;
            text-decoration: none;
            margin-left: 5px;
            font-weight: bold;
        }

        /* --- Error Messages --- */
        .error-message {
            color: red;
            font-size: 0.8em;
            margin-top: 5px;
            display: block;
            text-align: left;
        }
        .validation-errors {
            color: red;
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid red;
            border-radius: 8px;
            background-color: #ffebeb;
            font-size: 0.9em;
            text-align: left;
        }
        .validation-errors ul {
            margin: 0;
            padding-left: 20px;
        }
        /* Style untuk input yang disembunyikan */
        .hidden-group {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-section"></div> {{-- Bagian gambar di kiri --}}
        <div class="form-section">
            <h2>MindMeet Tel-U</h2>

            {{-- --- BAGIAN INI MENAMPILKAN PESAN ERROR VALIDASI SECARA UMUM --- --}}
            @if ($errors->any())
                <div class="validation-errors">
                    <p>Terjadi kesalahan saat registrasi:</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{-- --- AKHIR BAGIAN PESAN ERROR UMUM --- --}}

            <form method="POST" action="{{ route('register.submit') }}">
                @csrf

                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" name="name" id="name" placeholder="Your Full Name" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" placeholder="Your Email Address" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="Min. 8 characters" required>
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password:</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm your password" required>
                    @error('password_confirmation')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                {{-- --- ELEMEN SELECT UNTUK MEMILIH ROLE --- --}}
                <div class="form-group">
                    <label for="role">Daftar sebagai:</label>
                    <select name="role" id="role" required>
                        <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="konselor" {{ old('role') == 'konselor' ? 'selected' : '' }}>Konselor</option>
                    </select>
                    @error('role')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                {{-- --- AKHIR ELEMEN SELECT UNTUK MEMILIH ROLE --- --}}

                {{-- --- INPUT TAMBAHAN UNTUK ROLE (akan disembunyikan/ditampilkan dengan JS) --- --}}
                <div id="additional-fields-mahasiswa" class="form-group hidden-group">
                    <label for="nim">NIM:</label>
                    {{-- Hapus name="nim" di sini --}}
                    <input type="text" id="nim" placeholder="Your Student ID (NIM)" value="{{ old('nim') }}">
                    @error('nim')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div id="additional-fields-konselor" class="form-group hidden-group">
                    <label for="spesialisasi">Spesialisasi:</label>
                    {{-- Hapus name="spesialisasi" di sini --}}
                    <input type="text" id="spesialisasi" placeholder="e.g., Psikolog Klinis" value="{{ old('spesialisasi') }}">
                    @error('spesialisasi')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                {{-- --- AKHIR INPUT TAMBAHAN --- --}}

                <button type="submit" class="submit-button">Register</button>
            </form>

            <div class="alt-login">
                <label>Sudah memiliki akun? <a href="{{ route('login') }}">Login</a></label>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const mahasiswaFields = document.getElementById('additional-fields-mahasiswa');
            const konselorFields = document.getElementById('additional-fields-konselor');
            const nimInput = document.getElementById('nim');
            const spesialisasiInput = document.getElementById('spesialisasi');

            function toggleAdditionalFields() {
                // Sembunyikan semua field tambahan
                mahasiswaFields.classList.add('hidden-group');
                konselorFields.classList.add('hidden-group');

                // --- PENTING: Atur atribut 'name' DAN 'required' ---
                // Hapus atribut 'name' dan 'required' dari semua input tambahan terlebih dahulu
                nimInput.removeAttribute('name');
                nimInput.removeAttribute('required');
                spesialisasiInput.removeAttribute('name');
                spesialisasiInput.removeAttribute('required');


                // Tampilkan field sesuai role yang dipilih dan tambahkan 'name' serta 'required'
                if (roleSelect.value === 'mahasiswa') {
                    mahasiswaFields.classList.remove('hidden-group');
                    nimInput.setAttribute('name', 'nim'); // <-- Tambahkan name untuk NIM
                    nimInput.setAttribute('required', 'required');
                } else if (roleSelect.value === 'konselor') {
                    konselorFields.classList.remove('hidden-group');
                    spesialisasiInput.setAttribute('name', 'spesialisasi'); // <-- Tambahkan name untuk spesialisasi
                    spesialisasiInput.setAttribute('required', 'required');
                }
            }

            // Panggil saat halaman dimuat (untuk old input atau default selected)
            toggleAdditionalFields();

            // Panggil setiap kali pilihan role berubah
            roleSelect.addEventListener('change', toggleAdditionalFields);

            // Jika ada old input, pastikan field yang relevan ditampilkan saat ada error validasi
            @if(old('role'))
                // Ini akan menginisialisasi kembali pilihan role dari old input
                // dan memicu toggleAdditionalFields agar form tampil benar
                roleSelect.value = "{{ old('role') }}";
                toggleAdditionalFields();
            @endif
        });
    </script>
</body>
</html>