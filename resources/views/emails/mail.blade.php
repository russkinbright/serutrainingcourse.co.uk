<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>🚀 Your Course Purchase Confirmation</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background-color: white;
            padding: 25px;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .course-card {
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .course-title {
            color: #2c3e50;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .highlight {
            color: #e74c3c;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #7f8c8d;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: bold;
        }
        .login-credentials {
            margin-top: 25px;
            padding: 15px;
            background-color: #f1f1f1;
            border-left: 4px solid #2ecc71;
            border-radius: 4px;
        }
        .login-credentials h3 {
            margin-top: 0;
            color: #2ecc71;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚀 Welcome to Seru Training Course!</h1>
    </div>

    <div class="content">
        <h2>Hey {{ $billing['fullName'] ?? 'Learner' }},</h2>

        <p>🎉 <strong>Your course purchase was successful!</strong> Thank you for registering with Seru Training.</p>

        <p>Here’s what you’ve purchased:</p>

        @foreach($cartItems as $item)
            <div class="course-card">
                <div class="course-title">{{ $item['name'] ?? 'Untitled Course' }}</div>
                <div>🎟 <strong>Quantity:</strong> {{ $item['quantity'] ?? 1 }}</div>
                💷 Price: £{{ number_format($item['price'] ?? 0, 2) }} x {{ $item['quantity'] ?? 1 }} = £{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}
            </div>
        @endforeach

        <div class="login-credentials">
            <h3>🔐 Your Login Credentials</h3>
            <p><strong>Email:</strong> {{ $billing['email'] }}</p>

            @if(Str::startsWith($plainPassword, 'Use your'))
                <p><strong>Password:</strong> You already have an account. Please use your existing password to log in.</p>
            @else
                <p><strong>Password:</strong> {{ $plainPassword }}</p>
            @endif

            <p><a href="https://serutrainingcourse.co.uk/learner/login" class="button">Login to Your Account</a></p>
        </div>

        <p>We’re excited to help you kickstart your career with <strong>Seru Training Course</strong>!</p>

        <p>Want to see our full course list? <a href="https://serutrainingcourse.co.uk/" class="button">Visit Our Site</a></p>

        <p>Got questions? We're here for you at <span class="highlight">support@serutrainingcourse.co.uk</span></p>

        <div class="footer">
            <p>See you in the Learning Portal<br>
                <strong>Seru Training Course Team</strong></p>
            <p>🔒 Your training journey starts here</p>
        </div>
    </div>
</body>
</html>
