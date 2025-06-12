{{-- resources/views/appointments/home.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Home - Today's Appointment</title>
    <style>
        /* CSS sama seperti sebelumnya */
        body {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            grid-template-rows: repeat(5, 1fr);
            grid-column-gap: 8px;
            grid-row-gap: 8px;
            height: 97.5vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
        }

        .navigation-bar {
            grid-area: 1 / 1 / 6 / 2;
            width: 300px;
            background-color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .navigation-bar .row {
            display: flex; 
            align-items: center;
        }

        .navigation-bar img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 20px;
            margin-top: 20px;
        }

        .navigation-bar h2 {
            margin-bottom: 20px;
            color: #555;
            margin-top: 30px;
        }

        .navigation-bar ul {
            list-style-type: none;
            width: 100%;
        }

        .navigation-bar li {
            margin-bottom: 10px;
        }

        .navigation-bar a {
            text-decoration: none;
            display: block;
            width: 80%;
            padding: 10px 10px;
            color: #333;
            border-radius: 5px;
            transition: 0.3s;
        }

        .navigation-bar a:hover {
            background-color: #555;
            color: white;
        }

        .header {
            grid-area: 1 / 2 / 2 / 6;
            background-color: white;
            padding: 20px;
            margin-bottom: 5px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calendar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .calendar-header h2 {
            margin-right: 550px;
            font-size: 24px;
        }

        .calendar-nav button {
            padding: 10px 20px;
            border: 1px solid #ccc;
            background-color: #fff;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .calendar-nav button:hover {
            background-color: #bbb;
        }

        .calendar-nav {
            justify-content: space-between;
            display: flex;
            gap: 10px;
        }

        .main {
            grid-area: 2 / 2 / 6 / 6;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: grid;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
        }

        .day-header, .day-cell {
            border: 1px solid black;
            padding: 5px;
            text-align: center;
        }

        .day-header {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .day-cell {
            min-height: 50px;
            position: relative;
        }

        .day-cell .event {
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            background-color: #ffeb3b;
            padding: 5px;
            border-radius: 3px;
            font-size: 12px;
            text-align: left;
        }

        .event.green {
            background-color: #4caf50;
            color: white;
        }

        .event.orange {
            background-color: #ff9800;
            color: white;
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
            <li><a href="{{ route('booking.index') }}">Manage Booking</a></li>
            <li><a href="{{ route('counselor.index') }}">Manage Counselor</a></li>
            <li><a href="{{ route('schedule.index') }}">Manage Schedule</a></li>
            <li><a href="{{ route('student.index') }}">Manage Student</a></li>
            <li style="margin-top: 114%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        <div class="calendar-header">
            <h2>September 2025</h2>
            <div class="calendar-nav">
                <button>Month</button>
                <button>Week</button>
                <button>Day</button>
                <button>List</button>
            </div>
        </div>
    </div>

    <div class="main">
        <div class="calendar-grid">
            {{-- Day Headers --}}
            @foreach(['MON','TUE','WED','THU','FRI','SAT','SUN'] as $day)
                <div class="day-header">{{ $day }}</div>
            @endforeach

            {{-- Sample static days and events --}}
            @php
                $events = [
                    8 => ['9:00 (2 hours)', '13:00 (60 min)'],
                    17 => ['20:00 (60 min)'],
                    18 => ['13:00 (60 min)'],
                    21 => ['13:00 (60 min)'],
                    24 => ['13:00 (60 min)'],
                ];
            @endphp

            @for ($i = 29; $i <= 31; $i++)
                <div class="day-cell">{{ $i }}</div>
            @endfor

            @for ($i = 1; $i <= 30; $i++)
                <div class="day-cell">
                    {{ $i }}
                    @if(isset($events[$i]))
                        @foreach($events[$i] as $event)
                            <div class="event green">{{ $event }}</div>
                        @endforeach
                    @endif
                </div>
            @endfor
        </div>
    </div>

</body>
</html>
