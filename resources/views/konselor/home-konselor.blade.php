<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Konselor Dashboard - My Appointments</title> {{-- Ubah judul halaman --}}

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
            cursor: default;
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

        /* --- Gaya untuk Kalender Dinamis --- */
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .calendar-header h2 {
            margin: 0;
            color: #333;
        }
        .calendar-nav button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 5px;
            transition: background-color 0.3s ease;
        }
        .calendar-nav button:hover {
            background-color: #0056b3;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            background-color: #fff;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .day-header, .day-cell {
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            min-height: 80px;
            position: relative;
            box-sizing: border-box;
        }
        .day-header {
            font-weight: bold;
            background-color: #e0e0e0;
            color: #555;
        }
        .day-cell {
            border: 1px solid #eee;
            background-color: #fafafa;
        }
        .day-cell.current-month {
            background-color: #fff;
        }
        .day-cell.today {
            background-color: #e6f7ff;
            border-color: #91d5ff;
            font-weight: bold;
        }
        .day-number {
            font-size: 1.2em;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }
        .event {
            font-size: 0.75em;
            padding: 2px 5px;
            margin-top: 3px;
            border-radius: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .event:hover {
            opacity: 0.9;
        }
        .event.booking-event {
            background-color: #d4edda; /* Warna hijau untuk booking */
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .event.no-booking {
            background-color: #f8d7da; /* Warna merah muda untuk slot tanpa booking (jika ditampilkan) */
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

    <div class="image-section"></div>

    <div class="navigation-bar">
        <div class="row">
            <img src="{{ asset('images/mindmeet.png') }}" alt="MindMeet Logo" class="logo">
            <h2>MindMeet</h2>
        </div>

        <ul>
            <li><a href="{{ route('konselor.dashboard') }}">Home</a></li>
            <li><a href="{{ route('konselor.my_schedules.create') }}">Input Jadwal</a></li>
            <li><a href="{{ route('konselor.my_schedules') }}">My Schedules</a></li>
            <li style="margin-top: 131%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        <h2>Hello {{ $user->name ?? $konselor->nama ?? 'Konselor' }},</h2>
        <h1>Good Morning!</h1>

        <div class="calendar-header">
            <h2 id="current-month-display">{{ $currentMonth }}</h2>
            <div class="calendar-nav">
                <button id="prevMonth">Previous Month</button>
                <button id="nextMonth">Next Month</button>
            </div>
        </div>
    </div>

    <div class="main">
        <h2 style="margin-bottom: 20px; margin-top: 3px;">My Appointments</h2> {{-- TEKS INI DIUBAH --}}

        @forelse($todaysBookings as $booking)
            <div class="appointment-card">
                <p><strong>ID Booking:</strong> {{ $booking->id }}</p>
                <p><strong>Mahasiswa:</strong> {{ $booking->mahasiswa->nama ?? 'N/A' }} (NIM: {{ $booking->mahasiswa->nim ?? 'N/A' }})</p>
                <p><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($booking->jadwal->waktu)->format('H:i') }}</p>
                <p><strong>Status:</strong> {{ $booking->status }}</p>
            </div>
        @empty
            <p>Tidak ada janji temu hari ini.</p>
        @endforelse

        <div id="calendar-grid-container" class="calendar-grid">
            <!-- Day Headers -->
            @foreach (['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'] as $day)
                <div class="day-header">{{ $day }}</div>
            @endforeach
            <!-- Cells will be generated by JS -->
        </div>
    </div>

    <div class="profil">
        <img src="{{ asset('images/profil.jpg') }}" alt="Profil Konselor">
        <div class="profile-info">
            <h3>{{ $konselor->nama ?? $user->name ?? 'Nama Konselor' }}</h3>
            <p>{{ $konselor->spesialisasi ?? 'Spesialisasi Konselor' }}</p>
            <p>{{ $user->email ?? 'Email Konselor' }}</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarGridContainer = document.getElementById('calendar-grid-container');
            const currentMonthDisplay = document.getElementById('current-month-display');
            const prevMonthBtn = document.getElementById('prevMonth');
            const nextMonthBtn = document.getElementById('nextMonth');

            const allBookings = @json($allKonselorBookings);
            
            let currentCalendarDate = new Date();

            function renderCalendar() {
                while (calendarGridContainer.children.length > 7) {
                    calendarGridContainer.removeChild(calendarGridContainer.lastChild);
                }

                const monthYearFormatter = new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' });
                currentMonthDisplay.textContent = monthYearFormatter.format(currentCalendarDate);

                const year = currentCalendarDate.getFullYear();
                const month = currentCalendarDate.getMonth();

                const firstDayOfMonth = new Date(year, month, 1);
                const startingDay = (firstDayOfMonth.getDay() === 0 ? 6 : firstDayOfMonth.getDay() - 1);

                const daysInMonth = new Date(year, month + 1, 0).getDate();

                const today = new Date();
                const isCurrentMonth = today.getFullYear() === year && today.getMonth() === month;

                for (let i = 0; i < startingDay; i++) {
                    const emptyCell = document.createElement('div');
                    emptyCell.classList.add('day-cell');
                    calendarGridContainer.appendChild(emptyCell);
                }

                for (let day = 1; day <= daysInMonth; day++) {
                    const dayCell = document.createElement('div');
                    dayCell.classList.add('day-cell');
                    dayCell.classList.add('current-month');

                    const dayNumberSpan = document.createElement('span');
                    dayNumberSpan.classList.add('day-number');
                    dayNumberSpan.textContent = day;
                    dayCell.appendChild(dayNumberSpan);

                    const currentDay = new Date(year, month, day);
                    const currentDayString = currentDay.toISOString().slice(0, 10);

                    if (isCurrentMonth && day === today.getDate()) {
                        dayCell.classList.add('today');
                    }

                    const bookingsForThisDay = allBookings.filter(booking => booking.date === currentDayString);
                    bookingsForThisDay.forEach(booking => {
                        const eventDiv = document.createElement('div');
                        eventDiv.classList.add('event', 'booking-event');
                        eventDiv.textContent = `${booking.time} - ${booking.mahasiswa_name}`;
                        eventDiv.title = `Booking: ${booking.time} with ${booking.mahasiswa_name}`;
                        dayCell.appendChild(eventDiv);
                    });

                    calendarGridContainer.appendChild(dayCell);
                }
            }

            prevMonthBtn.addEventListener('click', function() {
                currentCalendarDate.setMonth(currentCalendarDate.getMonth() - 1);
                renderCalendar();
            });

            nextMonthBtn.addEventListener('click', function() {
                currentCalendarDate.setMonth(currentCalendarDate.getMonth() + 1);
                renderCalendar();
            });

            renderCalendar();
        });
    </script>
</body>
</html>