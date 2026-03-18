<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel App | Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.5.95/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #248907;
            --primary-dark: #1a6d05;
            --bg-color: #f0f4f8;
            --text-main: #1a1a1a;
            --text-muted: #64748b;
            --white: #ffffff;
            --border-color: #e2e8f0;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            background-image: radial-gradient(circle at 2px 2px, #d1d5db 1px, transparent 0);
            background-size: 40px 40px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            display: flex;
            max-width: 1000px;
            width: 100%;
            min-height: 600px;
            background: var(--white);
            border-radius: 24px;
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .left-panel {
            flex: 1.2;
            background: linear-gradient(135deg, var(--primary-color) 0%, #1a6d05 100%);
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: var(--white);
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }

        .left-panel h1 {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 20px;
            line-height: 1.1;
        }

        .main-desc {
            font-size: 16px;
            line-height: 1.6;
            opacity: 0.95;
            margin-bottom: 40px;
        }

        .features-list {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .feature-item {
            display: flex;
            align-items: center;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 1.8rem;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .feature-info h3 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .feature-info p {
            font-size: 0.9rem !important;
            opacity: 0.8 !important;
            line-height: 1.4 !important;
        }

        .bottom-branding {
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.85rem;
            opacity: 0.6;
        }

        .right-panel {
            flex: 1;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: var(--white);
        }

        .brand-name {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 40px;
            display: flex;
            align-items: center;
        }

        .brand-name i {
            margin-right: 10px;
        }

        .login-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .login-header p {
            color: var(--text-muted);
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i.prefix-icon {
            position: absolute;
            left: 16px;
            color: var(--text-muted);
            font-size: 1.2rem;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background: #f8fafc;
            border: 2px solid #f1f5f9;
            border-radius: 12px;
            font-size: 1rem;
            color: var(--text-main);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(36, 137, 7, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            cursor: pointer;
            color: var(--text-muted);
            font-size: 1.2rem;
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: var(--primary-color);
        }

        .submit-btn {
            width: 100%;
            padding: 16px;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .submit-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(36, 137, 7, 0.3);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 0.95rem;
            display: flex;
            align-items: flex-start;
        }

        .alert-error {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        .alert-success {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        @media (max-width: 900px) {
            .left-panel {
                display: none;
            }
            .login-card {
                max-width: 500px;
            }
        }

        @media (max-width: 480px) {
            .right-panel {
                padding: 40px 24px;
            }
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="left-panel">
            <div class="left-content-wrapper">
                <h1>Travel App Dashboard</h1>
                <p class="main-desc">Admin Control Center. Empowering your travel business with a sleek, powerful, and efficient management suite.</p>
                
                <div class="features-list">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="mdi mdi-shield-check-outline"></i>
                        </div>
                        <div class="feature-info">
                            <h3>Secure Access</h3>
                            <p>Military-grade security for your data and transactions.</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="mdi mdi-trending-up"></i>
                        </div>
                        <div class="feature-info">
                            <h3>Smart Analytics</h3>
                            <p>Deep insights into your bookings and revenue trends.</p>
                        </div>
                    </div>

                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="mdi mdi-account-group-outline"></i>
                        </div>
                        <div class="feature-info">
                            <h3>User Management</h3>
                            <p>Easily manage riders, drivers, and support tickets.</p>
                        </div>
                    </div>

                </div>
            </div>
            
            <div class="bottom-branding">
                <p>&copy; 2024 Travel App Inc. All rights reserved.</p>
            </div>
        </div>
        <div class="right-panel">
            <div class="brand-name">
                <i class="mdi mdi-map-marker-path"></i>
                TRAVEL APP
            </div>
            
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Please enter your details to sign in.</p>
            </div>

            @if (session('error'))
                <div class="alert alert-error">
                    <i class="mdi mdi-alert-circle-outline" style="margin-right: 10px;"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    <i class="mdi mdi-check-circle-outline" style="margin-right: 10px;"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <i class="mdi mdi-alert-circle-outline" style="margin-right: 10px;"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.loginSubmit') }}">
                @csrf
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrapper">
                        <i class="mdi mdi-email-outline prefix-icon"></i>
                        <input type="email" name="email" class="form-control" placeholder="admin@example.com" value="{{ old('email') }}" required autoFocus>
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="mdi mdi-lock-outline prefix-icon"></i>
                        <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                        <i class="mdi mdi-eye-off-outline toggle-password" id="togglePassword"></i>
                    </div>
                </div>

                <button type="submit" class="submit-btn">
                    Sign In to Dashboard
                </button>
            </form>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function (e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the eye / eye slash icon
            this.classList.toggle('mdi-eye-outline');
            this.classList.toggle('mdi-eye-off-outline');
        });
    </script>
</body>

</html>
