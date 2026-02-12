<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: none;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #248907;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
            color: #333333;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #555555;
        }
        .code-box {
            background-color: #f0fdf4;
            border: 2px dashed #248907;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            display: inline-block;
        }
        .code {
            font-size: 36px;
            font-weight: 700;
            color: #248907;
            letter-spacing: 8px;
            margin: 0;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #888888;
            border-top: 1px solid #eeeeee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Password Reset</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>We received a request to reset the password for your account.</p>
            <p>Please use the following verification code to complete the process:</p>
            
            <div class="code-box">
                <div class="code">{{ $code }}</div>
            </div>

            <p>This code is valid for <strong>10 minutes</strong>.</p>
            <p style="font-size: 14px; color: #999;">If you didn't request this, you can safely ignore this email.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Travel App. All rights reserved.
        </div>
    </div>
</body>
</html>
