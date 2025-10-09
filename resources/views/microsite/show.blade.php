<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. {{ $microsite->doctor->name }}'s Microsite</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f4f4f7da;
            /* gray-200 */
        }

        .mobile-container {
            max-width: 420px;
            margin: 0 auto;
            background-color: #F9FAFB;
            /* gray-50 */
            min-height: 100vh;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .tab-active {
            border-bottom: 3px solid #3B82F6;
            /* blue-500 */
            color: #3B82F6;
            font-weight: 600;
        }

        .tab {
            transition: all 0.2s ease-in-out;
        }
    </style>
</head>

<body class="font-sans">

    @if ($microsite->is_active)
        <div class="max-w-md mx-auto my-0 ">
            <div
                class="bg-gradient-to-r from-amber-200 to-yellow-500 
            rounded-xl 
            m-2 
            p-2
            h-[30vh]
            flex flex-col items-center justify-center ">

                <img src="{{ $microsite->doctor->profile_photo_url }}" alt="{{ $microsite->doctor->name }}"
                    class="rounded-full w-24 h-24 object-cover mb-2" />
                <h1>{{ $microsite->doctor->profile_photo_url }}</h1>
                <p>{{ $microsite->doctor->qualification->name }}</p>
            </div>
            <div>Stats</div>
            <div>About</div>
        </div>






        </div>
    @else
        <div class="mobile-container flex flex-col bg-red-100 text-red-800 p-4 text-center">
            <main class="flex-grow flex items-center justify-center">
                <div class="font-semibold">This site is currently inactive.</div>
            </main>
            <footer>
                {{ config('app.name') }}
            </footer>
        </div>
    @endif

</body>

</html>
