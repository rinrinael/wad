<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Form</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #cf3f3f;
            background-image: url("{{ asset('images/tult-telyu.png') }}");
            background-size: cover;
            color: #cf3f3f;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            display: flex;
            background-color: #eee;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            padding: 50px;
            width: 800px;
            height: 45%;
        }

        .image-section {
            flex: 1;
            background: url('{{ asset('images/mindmeet.png') }}') no-repeat center center;
            background-size: 90%;
        }

        .form-section {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin-left: 100px;
        }

        .form-section h2 {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            width: 86%;
            justify-content: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input {
            width: 80%;
            padding: 10px;
            border: none;
            background-color: #eee;
            border-bottom-style: solid;
            border-color: #b3b5b7;
            color: #303841;
        }

        .form-group input::placeholder {
            color: #30384146;
        }

        .form-group input:focus {
            outline: 2px solid #eee;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            color: #585a5c;
        }

        .checkbox-group input {
            margin-right: 10px;
        }

        .checkbox-group a {
            color: #cf3f3f;
            text-decoration: none;
        }

        .checkbox-group a:hover {
            text-decoration: underline;
        }

        .submit-button {
            background-color: #cf3f3f;
            color: #eee;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 86%;
            margin-bottom: 10px;
        }

        .alt-login {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            width: 86%;
            justify-content: center;
        }

        .alt-login label {
            color: #585a5c;
            font-size: 14px;
        }

        .alt-login a {
            color: #cf3f3f;
            text-decoration: none;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="image-section"></div>
        <div class="form-section">
            <h2>MindMeet Tel-U</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>

                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <button type="submit" class="submit-button">Login</button>
            </form>

            <div class="alt-login">
                <label for="login">Belum memiliki akun? <a href="{{ route('register') }}">Register</a></label>
            </div>
        </div>
    </div>
</body>
</html>
