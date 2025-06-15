<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Students</title>
    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}" />
    <style>
        .table-container { padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; vertical-align: middle; }
        th { background-color: #f4f4f4; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }

        /* Gaya umum untuk tombol Aksi (Edit, Delete) */
        .student-actions { /* Ganti nama kelas container agar spesifik */
            display: flex;
            gap: 8px;
            flex-wrap: nowrap;
        }
        .student-actions .button { /* Sesuaikan nama kelas */
            background-color: #007bff;
            color: white;
            border: 1px solid transparent;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            
            display: inline-flex;
            align-items: center;
            justify-content: center;
            
            font-size: 16px;
            font-family: inherit;
            line-height: 1.2;
            height: auto;
            box-sizing: border-box;
            white-space: nowrap;
            min-width: 70px;
            text-align: center;
            
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            margin: 0;
            outline: none;

            transition: background-color 0.2s ease, transform 0.1s ease, box-shadow 0.2s ease;
        }
        .student-actions .button.delete {
            background-color: #dc3545;
        }
        .student-actions .button:hover {
            filter: brightness(110%);
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .student-actions .button:active {
            transform: translateY(0);
            box-shadow: none;
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
            <li style="margin-top: 114%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        <h2>Hello {{ Auth::user()->name }},</h2>
        <h1>Manage All Students</h1>
    </div>

    <div class="main">
        <div class="table-container">
            <h2>All Students List</h2>

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

            <table>
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Aksi</th> {{-- TAMBAHKAN KOLOM AKSI --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                        <tr>
                            <td>{{ $student->nim }}</td>
                            <td>{{ $student->nama }}</td>
                            <td>{{ $student->email_user ?? $student->email ?? 'N/A' }}</td> {{-- Gunakan accessor atau kolom email di Mahasiswa --}}
                            <td>
                                <div class="student-actions">
                                    <a href="{{ route('manage.student.edit', $student->nim) }}" class="button">Edit</a>

                                    <form action="{{ route('manage.student.destroy', $student->nim) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mahasiswa ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button delete">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center;">Tidak ada mahasiswa yang ditemukan.</td> {{-- UBAH COLSPAN MENJADI 4 --}}
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div> 

    <div class="profil">
        <img src="{{ asset('images/profil.jpg') }}" alt="Profil">
        <div class="profile-info">
            <h3>{{ Auth::user()->name }}</h3>
            <p>{{ Auth::user()->email }}</p>
        </div>
    </div>

</body>
</html>