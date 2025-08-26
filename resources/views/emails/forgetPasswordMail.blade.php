<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: linear-gradient(135deg, #ffffff, #f0f4ff);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(to right, #6b21a8, #4c1d95);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .content {
            padding: 30px;
            text-align: center;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin: 0 0 20px;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(to right, #ee004f, #fc0075);
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
        }
        .button:hover {
            background: linear-gradient(to right, #4c1d95, #6b21a8);
        }
        .footer {
            background: #f4f4f9;
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        .footer p {
            margin: 0;
        }
        @media (max-width: 600px) {
            .container {
                margin: 20px;
            }
            .header h1 {
                font-size: 24px;
            }
            .button {
                padding: 12px 24px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reset Your Password</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>We received a request to reset your password for your Seru Training Course account. Click the button below to set a new password.</p>
            <a href="{{ $resetLink }}" class="button">Reset Password</a>
            <p>If you did not request a password reset, please ignore this email. This link will expire in 60 minutes for security reasons.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Seru Training Course. All rights reserved.</p>
            <p>If you have any questions, contact us at <a href="mailto:support@serutrainingcourse.co.uk">support@serutrainingcourse.co.uk</a>.</p>
        </div>
    </div>
</body>
</html>