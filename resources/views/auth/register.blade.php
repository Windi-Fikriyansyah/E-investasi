<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register SmartNiuVolt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1e88e5, #0d47a1);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            color: #333;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header img {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
        }

        .login-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #0d47a1;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #1e88e5;
            box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.2);
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .remember-forgot a {
            color: #1e88e5;
            text-decoration: none;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #1e88e5;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color: #1565c0;
        }

        .signup-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .signup-link a {
            color: #1e88e5;
            text-decoration: none;
            font-weight: 500;
        }

        .error-message {
            color: #e53935;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
                margin: 0 15px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">

            <h1>Register to SmartNiuVolt</h1>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <form id="loginForm" method="POST" action="{{ route('register') }}" class="mb-3">
            @csrf
            <div class="form-group">
                <label for="phone">No Telepon</label>
                <input type="text" id="phone" name="phone" placeholder="Masukan No Telepon" required>
                <div class="error-message" id="email-error">Please enter a valid email</div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Masukan Password" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    placeholder="Masukan Konfirmasi Password" required>
            </div>

            <div class="form-group">
                <label for="referral_code">Referral Code (Optional)</label>
                <input type="text" name="referral_code" id="referral_code" placeholder="Referral Code"
                    value="{{ $prefilledReferralCode ?? old('referral_code') }}" readonly>
                <small class="text-muted">Leave blank if you don't have one</small>
            </div>



            <button type="submit" class="login-btn">Daftar</button>

            <div class="signup-link">
                Don't have an account? <a href="{{ route('login') }}">Login</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
