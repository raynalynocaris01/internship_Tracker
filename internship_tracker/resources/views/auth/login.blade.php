<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Internship Tracker</title>
    @vite(['resources/js/app.js'])
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="card-header">
                <h2>Internship Tracker</h2>
                <p>Student, Teacher & Admin Portal</p>
            </div>
            <div class="card-body">
                
                @if ($errors->any())
                    <div class="alert-danger">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                
                @if (session('success'))
                    <div class="alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if (request()->get('redirect') === 'qr')
                    <div class="qr-info">📱 Please login to complete your time request.</div>
                @endif
                
                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus autocomplete="username">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password">
                    </div>

                    <button type="submit" class="btn-login">Login</button>
                </form>
                
                <!-- REMOVED REGISTER LINK -->
                <!-- Only show for admins or remove completely -->
                
                <div class="footer-note">
                    <small>Accounts are managed by your administrator</small>
                    <br>
                    <small class="text-muted">© {{ date('Y') }} Internship Tracker System</small>
                </div>
            </div>
        </div>
    </div>

    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .login-container {
            width: 100%;
            max-width: 450px;
            margin: 100px auto;
            padding: 20px;
            box-sizing: border-box;
        }
        .login-card {
            background: white;
            border-radius: 28px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        .card-header {
            background: #216699;
            color: #ede432;
            padding: 30px 20px;
            text-align: center;
            border-bottom: 3px solid #ede432;
        }
        .card-header h2 {
            font-size: 1.8rem;
            margin: 0 0 8px 0;
            font-weight: bold;
        }
        .card-header p {
            margin: 0;
            opacity: 0.9;
        }
        .card-body {
            padding: 35px 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #216699;
        }
        .form-control {
            width: 100%;
            padding: 12px 18px;
            border: 1px solid #ccc;
            border-radius: 40px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: #216699;
            box-shadow: 0 0 5px rgba(33, 102, 153, 0.3);
        }
        .btn-login {
            width: 100%;
            background: #216699;
            color: white;
            border: 2px solid #ede432;
            border-radius: 40px;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .btn-login:hover {
            background: #174b73;
        }
        .alert-danger {
            background-color: #fde8e8;
            color: #e53e3e;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .alert-success {
            background-color: #def5e5;
            color: #1e7e34;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .qr-info {
            background-color: #ebf8ff;
            color: #2b6cb0;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: center;
        }
        .footer-note {
            text-align: center;
            color: #777;
            font-size: 0.85rem;
            margin-top: 25px;
        }
        .text-muted {
            color: #999;
        }
    </style>
</body>
</html>