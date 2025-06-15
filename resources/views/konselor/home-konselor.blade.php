<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Konselor Dashboard - Calendar</title>

    <link rel="stylesheet" href="{{ asset('css/konselor.css') }}" />

    <style>
        h2 {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        body {
            height: auto; /* Diubah jadi lebih lebar */
        }

        /* --- Gaya untuk Kalender Dinamis --- */
        .calendar-section {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            padding: 25px;
            margin-top: 30px;
            /* --- PERUBAHAN DI SINI UNTUK LEBAR --- */
            max-width: 100%; /* Pastikan bisa selebar parent */
            margin-left: 0; /* Hapus auto margin untuk tidak di tengah */
            margin-right: 0; /* Hapus auto margin untuk tidak di tengah */
            width: 100%; /* Pastikan mengambil 100% lebar yang tersedia */
            box-sizing: border-box; /* Sertakan padding dalam perhitungan lebar */
            /* --- AKHIR PERUBAHAN --- */
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        .calendar-header h2 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .calendar-nav button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 7px;
            cursor: pointer;
            margin-left: 8px;
            transition: background-color 0.3s ease, transform 0.1s ease;
        }
        .calendar-nav button:hover {
            background-color: #0056b3;
            transform: translateY(-1px);
        }
        .calendar-nav button:active {
            transform: translateY(0);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }
        .day-header, .day-cell {
            text-align: center;
            padding: 12px 5px;
            border-radius: 8px;
            position: relative;
            box-sizing: border-box;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;

            min-height: 120px;
            max-height: 180px;
            overflow-y: auto;
        }

        .day-cell::-webkit-scrollbar {
            width: 6px;
        }
        .day-cell::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 3px;
        }
        .day-cell::-webkit-scrollbar-track {
            background-color: rgba(0,0,0,0.1);
        }

        .day-header {
            font-weight: bold;
            background-color: #e0e0e0;
            color: #555;
            font-size: 1.1em;
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
            font-size: 1.3em;
            color: #333;
            margin-bottom: 8px;
            display: block;
            font-weight: bold;
        }
        .event {
            font-size: 0.8em;
            padding: 3px 6px;
            margin-top: 2px;
            border-radius: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
            transition: background-color 0.2s ease;
            width: calc(100% - 10px);
            text-align: center;
        }
        .event:hover {
            opacity: 0.9;
        }
        .event.booking-event {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .event.no-booking {
            background-color: #f8d7da;
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
            <li><a href="{{ route('konselor.feedback') }}"> Feedback</a><li></li>
            <li style="margin-top: 131%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        <h2>Hello {{ Auth::user()->name ?? $konselor->nama ?? 'Konselor' }},</h2>
        <h1>Good Morning!</h1>
    </div>

    <div class="main">
        <div class="calendar-section">
            <div class="calendar-header">
                <h2 id="current-month-display">{{ $currentMonth }}</h2>
                <div class="calendar-nav">
                    <button id="prevMonth">Previous Month</button>
                    <button id="nextMonth">Next Month</button>
                </div>
            </div>
            <div id="calendar-grid-container" class="calendar-grid">
                @foreach (['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'] as $day)
                    <div class="day-header">{{ $day }}</div>
                @endforeach
                </div>
        </div>
    </div>

    <div class="profil">
        <img src="{{ asset('images/profil.jpg') }}" alt="Profil Konselor">
        <div class="profile-info">
            <h3>{{ $konselor->nama ?? Auth::user()->name ?? 'Nama Konselor' }}</h3>
            <p>{{ $konselor->spesialisasi ?? 'Spesialisasi Konselor' }}</p>
            <p>{{ Auth::user()->email ?? 'Email Konselor' }}</p>
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
                const isCurrentMonthViewed = today.getFullYear() === year && today.getMonth() === month;

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

                    if (isCurrentMonthViewed && day === today.getDate()) {
                        dayCell.classList.add('today');
                    }

                    const bookingsForThisDay = allBookings.filter(booking => {
                        return booking.date === currentDayString;
                    });

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