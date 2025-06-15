<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Schedules</title>
    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}" />
    <style>
        .table-container { padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; vertical-align: middle;}
        th { background-color: #f4f4f4; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }

        /* Styling untuk status badge */
        .schedule-status {
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.85em;
            font-weight: bold;
            color: white;
            display: inline-block;
            text-transform: capitalize; /* Kapitalisasi huruf pertama */
        }
        .schedule-status.available { background-color: #28a745; } /* Hijau */
        .schedule-status.not_available { background-color: #ffc107; color: #333; } /* Kuning */
        .schedule-status.booked { background-color: #007bff; } /* Biru */
        .schedule-status.cancelled { background-color: #dc3545; } /* Merah */
        .schedule-status.completed { background-color: #6c757d; } /* Abu-abu gelap */


        /* Gaya umum untuk tombol Aksi (Edit, Delete) */
        .schedule-actions {
            display: flex;
            gap: 8px;
            flex-wrap: nowrap;
        }
        .schedule-actions .button {
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

        .schedule-actions .button.delete {
            background-color: #dc3545;
        }

        .schedule-actions .button:hover {
            filter: brightness(110%);
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .schedule-actions .button:active {
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
        <h1>Manage All Schedules</h1>
    </div>

    <div class="main">
        <div class="table-container">
            <h2>All Schedules List</h2>

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
                        <th>ID Jadwal</th>
                        <th>Nama Konselor</th>
                        <th>Hari</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->id }}</td>
                            <td>{{ $schedule->konselor->nama ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($schedule->hari)->format('d F Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($schedule->waktu)->format('H:i') }}</td>
                            <td>
                                {{-- Menampilkan status booking jika ada dan aktif, jika tidak, status jadwal --}}
                                @if ($schedule->booking && !in_array($schedule->booking->status, ['cancelled', 'completed']))
                                    <span class="schedule-status booked">Booked</span>
                                @else
                                    <span class="schedule-status {{ $schedule->status }}">{{ ucfirst($schedule->status) }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="schedule-actions">
                                    {{-- PERBAIKAN: Kondisi untuk menampilkan tombol Edit/Delete --}}
                                    @if ($schedule->status == 'available' && (!$schedule->booking || in_array($schedule->booking->status, ['cancelled', 'completed'])))
                                        <a href="{{ route('manage.schedule.edit', $schedule->id) }}" class="button">Edit</a>

                                        <form action="{{ route('manage.schedule.destroy', $schedule->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button delete">Delete</button>
                                        </form>
                                    @else
                                        <span style="color: #6c757d; font-size: 0.9em;">N/A</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center;">Tidak ada jadwal yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>