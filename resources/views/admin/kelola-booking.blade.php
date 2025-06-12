<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Home - List Appointment</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .appointment-info {
            flex: 1;
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

        .button-group {
            display: flex;
            gap: 10px;
        }

        .button-group button {
            padding: 6px 14px;
            border: none;
            border-radius: 20px;
            font-size: 14px;
            background-color: #ddd;
            color: #333;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .button-group button:hover {
            background-color: #ccc;
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
            <li style="margin-top: 114%;"><a href="{{ route('logout') }}">Log Out</a></li>
        </ul>
    </div>

    <div class="header">
        <h2>Hello Rindu,</h2>
        <h1>Good Morning!</h1>
    </div>

    <div class="main">
        <h2 style="margin-bottom: 20px; margin-top: 5px;">List Appointment</h2>

        {{-- Looping data appointment dari controller --}}
        @foreach ($appointments as $appointment)
            <div class="appointment-card">
                <div class="appointment-info">
                    <p>ID: {{ $appointment->id }}</p>
                    <p>Time: {{ $appointment->start_time }} - {{ $appointment->end_time }}</p>
                </div>
                <div class="button-group">
                    <a href="{{ route('appointments.edit', $appointment->id) }}">
                        <button style="background-color: rgb(45, 177, 33); color: white;">Edit</button>
                    </a>
                    <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this appointment?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background-color: rgb(225, 61, 61); color: white;">Hapus</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <div class="profil">
        <img src="{{ asset('images/profil.jpg') }}" alt="Foto Profil">
        <div class="profile-info">
            <h3>Rindu Naella Darmawan</h3>
            <p>Rindu@mindmeet.com</p>
        </div>
    </div>

</body>
</html>
