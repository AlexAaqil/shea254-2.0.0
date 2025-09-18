<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="{{ asset('assets/images/shea254-app-logo.ico') }}" type="image/x-icon">

    @vite(['resources/css/guest-layout.css', 'resources/js/app.js'])

    <title>{{ config('app.name') }} | Skin Care Experts</title>

    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #f9fafb, #f3f4f6);
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .maintenance-box {
            display: grid;
            justify-content: center;
            text-align: center;
            max-width: 480px;
            padding: 2rem;
            border-radius: 1rem;
            background: white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }
        .maintenance-box img {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            margin: 0 auto 1rem;
        }
        .maintenance-box h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .maintenance-box p {
            font-size: 1rem;
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="maintenance-box">
        <h1>We’ll be back soon 😊</h1>
        <p>{{ config('app.name') }} is currently under maintenance.<br>
           Thank you for your patience!</p>
    </div>
</body>
</html>
