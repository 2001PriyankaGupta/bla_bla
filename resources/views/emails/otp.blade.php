<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your OTP Code</title>
    <style>
        body {
            font-family: 'Inter', Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .header {
            background-color: #248907;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .content h2 {
            font-size: 22px;
            color: #222222;
            margin-bottom: 20px;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            color: #555555;
            margin-bottom: 30px;
        }
        .otp-box {
            background-color: #f0fdf4;
            border: 2px dashed #6EC16E;
            padding: 20px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 30px;
        }
        .otp-text {
            font-size: 36px;
            font-weight: bold;
            letter-spacing: 5px;
            color: #248907;
            margin: 0;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #eeeeee;
            font-size: 14px;
            color: #888888;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Travel App</h1>
        </div>
        <div class="content">
            <h2>Welcome! You're almost there.</h2>
            <p>Please use the following One-Time Password (OTP) to verify your account or login. The code is valid for the next 1 minute.</p>
            
            <div class="otp-box">
                <p class="otp-text">{{ $otp }}</p>
            </div>
            
            <p>If you did not request this OTP, please ignore this email or contact support.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Travel App. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
