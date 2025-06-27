<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>500 - Internal Server Error</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Filament CSS (adjust path if needed) --}}
    <link rel="stylesheet" href="{{ asset('css/filament/app.css') }}">
    <style>
        body {
            background: #f9fafb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Inter, sans-serif;
        }
        .error-container {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.07);
            padding: 3rem 2.5rem;
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .error-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #12B981;
            margin-bottom: 0.5rem;
        }
        .error-message {
            font-size: 1.2rem;
            color: #374151;
            margin-bottom: 2rem;
        }
        .dashboard-btn {
            display: inline-block;
            background: #12B981;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.2s;
        }
        .dashboard-btn:hover {
            background: #12B981;
        }
    </style>
</head>
<body>
    <div class="error-container">
    <div class="error-title">500</div>

        <img src="{{ asset('gifs/rocket.gif') }}" alt="Rocket" style="display:block;margin:1.5rem auto 1rem auto;transform:rotate(180deg) scaleX(-1);width:80px;height:auto;">
        <div class="error-message">
            Oops! Something went wrong on our end.<br>
            Please contact the administrator or try again later.
        </div>
        <a href="{{ route('filament.admin.pages.dashboard') }}" class="dashboard-btn">
            ← Back to Dashboard
        </a>
    </div>
</body>
</html>