<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Travel App Dashboard Login</title>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.5.95/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f7f7f7;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            display: flex;
            width: 900px;
            min-height: 500px;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 28px rgba(0, 0, 0, 0.14);
            overflow: hidden;
        }

        .left-section {
            background: #249722;
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .left-content {
            color: #fff;
            max-width: 320px;
            text-align: center;
        }

        .left-content h1 {
            font-size: 2.2rem;
            margin-bottom: 18px;
            font-weight: 800;
        }

        .left-content p {
            font-size: 1.14rem;
            line-height: 1.7;
        }

        .right-section {
            width: 50%;
            padding: 60px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-title {
            font-size: 2.1rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: #222;
        }

        .login-subtitle {
            color: #333;
            font-size: 1.07rem;
            margin-bottom: 36px;
        }

        .input-group {
            margin-bottom: 26px;
        }

        .input-group input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 8px;
            border: 1px solid #c8c8c8;
            font-size: 1rem;
            transition: border .24s;
            box-sizing: border-box;
        }

        .input-group input:focus {
            border-color: #249722;
            outline: none;
        }

        .forgot-password {
            margin-bottom: 30px;
        }

        .forgot-password a {
            color: #249722;
            font-size: 15px;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-password a:hover {
            text-decoration: underline;
            color: #206d1c;
        }

        .login-btn {
            width: 100%;
            height: 48px;
            background: #249722;
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 8px;
            transition: background 0.18s;
            font-weight: 600;
        }

        .login-btn:hover {
            background: #21871d;
        }

        .login-form {
            width: 100%;
        }

        /* Error message styling */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background-color: #fee;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        .alert-success {
            background-color: #e8f5e8;
            border: 1px solid #c3e6cb;
            color: #155724;
        }

        @media (max-width: 820px) {
            .login-container {
                flex-direction: column;
                width: 96vw;
                min-width: 320px;
            }

            .left-section,
            .right-section {
                width: 100%;
                min-height: 220px;
            }

            .right-section {
                padding: 42px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="left-section">
            <div class="left-content">
                <h1>Welcome Back!</h1>
                <p>Bla bla, you can write anything you want here.<br>
                    For example: <br>
                    • Experience hassle-free travel booking.<br>
                    • Manage your trips and explore new destinations.<br>
                    • Secure and convenient access.</p>
            </div>
        </div>
        <div class="right-section">
            <div class="login-title">Travel App</div>
            <div class="login-subtitle">Dashboard Login</div>

            <!-- Error Messages -->
            @if (session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.loginSubmit') }}" class="login-form">
                @csrf
                <div class="input-group">
                    <input type="email" name="email" placeholder="Email Address" value="{{ old('email') }}"
                        required>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="forgot-password">
                    <a href="#">Forgot Password?</a>
                </div>
                <button type="submit" class="login-btn">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</body>

</html>
