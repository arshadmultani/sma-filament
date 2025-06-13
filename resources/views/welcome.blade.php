<?php
use Illuminate\Foundation\Inspiring;
$quote = Inspiring::quote();
$quote = preg_replace('/<[^>]+>/', '', $quote);
$quote = str_replace('“', '', $quote);
$quote = str_replace('”', '', $quote);
$quote = str_replace('“', '', $quote);
$quote = str_replace('”', '', $quote);
$quote = str_replace('“', '', $quote);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Charak SMA</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh;
            background: #f8fafc;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .welcome-card {
            background: #fff;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            padding: 2.5rem 1.5rem 2rem 1.5rem;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            max-width: 370px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .welcome-title {
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2rem;
            color: #079669;
        }
     
        .welcome-desc {
            color: #666;
            font-size: 1rem;
            text-align: center;
            font-style: italic;
            margin: 3rem 0 3rem 0;

        }
        .progress-dots {
            display: flex;
            gap: 0.4rem;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #e2e8f0;
        }
        .dot.active {
            background: #22c55e;
        }
        .welcome-btn {
            width: 100%;
            background: #079669;
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            padding: 0.9rem 0;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .welcome-btn:hover {
            background: #079669;
        }
        .skip-link {
            color: #22c55e;
            text-align: center;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            display: block;
        }
        @media (min-width: 600px) {
            .welcome-card {
                padding: 3rem 2.5rem 2.5rem 2.5rem;
                max-width: 400px;
            }
            .welcome-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    
    <div class="welcome-card">
        <div class="welcome-title">Welcome Aboard</div>
        <img src="/gifs/rocket.gif" alt="Rocket animation" style="width: 25%; max-width: 300px; margin: 1rem auto; display: block;">
        <div class="welcome-desc">
           {{ $quote }}
        </div>
       
        <form method="GET" action="{{ route('filament.admin.auth.login') }}" style="width:100%">
            <button type="submit" class="welcome-btn">Let's Go</button>
        </form>
    </div>
</body>
</html>
