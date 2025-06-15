<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa Baru</title>
    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}"> {{-- Pastikan path CSS ini benar --}}
    <style>
        /* Gaya dasar form */
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .form-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"],
        .form-group input[type="date"],
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box; /* Penting untuk konsistensi padding */
        }
        .form-group select {
            appearance: none; /* Hilangkan gaya default select */
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23000000%22%20d%3D%22M287%2C186.2L146.2%2C32.4c-3.1-3.1-8.2-3.1-11.3,0l-140.7,153.9c-3.1,3.1-3.1,8.2,0,11.3s8.2,3.1,11.3,0l135.1-147.9c1.6-1.6,4.1-1.6,5.7,0l135.1,147.9c3.1,3.1,8.2,3.1,11.3,0C290.1,194.4,290.1,189.3,287,186.2z%22%2F%3E%3C%2Fsvg%3E'); /* Tambah icon panah */
            background-repeat: no-repeat;
            background-position: right 10px top 50%;
            background-size: 12px auto;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.85em;
            margin-top: 5px;
        }
        .submit-button {
            width: 100%;
            padding: 12px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .submit-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Daftar Mahasiswa Baru</h1>

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
        @if ($errors->any())
            <div style="color: red; margin-bottom: 10px; padding: 10px; border: 1px solid red; background-color: #ffebeb; border-radius: 5px;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('mahasiswa.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Nama Lengkap (untuk Login & Mahasiswa):</label>
                <input type="text" name="name" id="name" required value="{{ old('name') }}">
                @error('name') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="email">Email (untuk Login & Mahasiswa):</label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}">
                @error('email') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
                @error('password') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password:</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>

            <div class="form-group">
                <label for="nim">NIM (maksimal 10 digit):</label>
                <input type="text" name="nim" id="nim" required value="{{ old('nim') }}" maxlength="10">
                @error('nim') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="tanggal">Tanggal (Lahir/Lainnya):</label>
                <input type="date" name="tanggal" id="tanggal" required value="{{ old('tanggal') }}">
                @error('tanggal') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="jadwal_id">Pilih Jadwal (Opsional jika tidak perlu di awal):</label>
                <select name="jadwal_id" id="jadwal_id" required>
                    <option value="">Pilih Jadwal</option>
                    {{-- Anda perlu memastikan $jadwalsTersedia dilewatkan ke view ini --}}
                    @foreach($jadwalsTersedia as $jadwal)
                        <option value="{{ $jadwal->id }}" {{ old('jadwal_id') == $jadwal->id ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($jadwal->hari)->format('d M Y') }} - {{ \Carbon\Carbon::parse($jadwal->waktu)->format('H:i') }} (Konselor: {{ $jadwal->konselor->nama ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
                @error('jadwal_id') <div class="error-message">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="submit-button">Daftar Mahasiswa</button>
        </form>
    </div>
</body>
</html>