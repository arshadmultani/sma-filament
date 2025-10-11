<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. {{ $microsite->doctor->name }}'s Microsite</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Funnel+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f7da;
            /* font-family: Lora, sans-serif; */

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

<body class="font-[Funnel Sans]">

    @if ($microsite->is_active)
        <div class="max-w-md mx-auto my-0">
            <div
                class="bg-gradient-to-br from-sky-100 via-white to-sky-200
        rounded-xl
        m-2
        p-2 shadow-sm">
                <div class="w-full flex flex-col items-center justify-around h-full">
                    <div class="p-2">
                        <img src="{{ $microsite->doctor->profile_photo_url }}" alt="{{ $microsite->doctor->name }}"
                            class="rounded-full w-24 h-24 sm:w-32 sm:h-32 object-cover mb-2" />
                    </div>

                    <p class="p-2 text-bold text-2xl sm:text-3xl">{{ $microsite->doctor->name }}</p>

                    <div
                        class="bg-white/40 backdrop-blur-md border border-white/30 shadow-md
                w-[90%] rounded-xl p-3 m-3 mx-auto max-w-sm
                flex justify-around text-center">
                        <div>
                            <p class="text-base sm:text-lg font-semibold drop-shadow-sm">5 yrs</p>
                            <p class="text-xs sm:text-sm text-gray-700/80">Experience</p>
                        </div>
                        <div>
                            <p class="text-base sm:text-lg font-semibold drop-shadow-sm">
                                {{ $microsite->doctor->qualification->name }}</p>
                            <p class="text-xs sm:text-sm text-gray-700/80">Qualification</p>
                        </div>
                        <div>
                            <p class="text-base sm:text-lg font-semibold drop-shadow-sm">{{ $microsite->doctor->town }}
                            </p>
                            <p class="text-xs sm:text-sm text-gray-700/80">Town</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SHOWCASES --}}
            <div class="p-2">
                <div
                    class="bg-gradient-to-br from-sky-100 via-white to-sky-200 border-white/30 rounded-2xl shadow-xl p-4 sm:p-6 space-y-6 w-full">

                    <h2 class="text-2xl text-center font-bold text-gray-800 border-b border-white/50 pb-2">
                        About Dr. {{ $microsite->doctor->name }}
                    </h2>

                    {{-- 1. Section for TEXT content --}}
                    @if (isset($groupedShowcases['text']))
                        <div class="space-y-4">
                            <h3 class="text-xl font-semibold text-blue-900/80">Information</h3>
                            @foreach ($groupedShowcases['text'] as $showcase)
                                <div
                                    class="bg-white/40 backdrop-blur-md rounded-xl p-3 shadow-md prose max-w-none text-gray-700">
                                    <div class="font-semibold text-xl">{{ $showcase->title }}</div>
                                    <div class="overflow-auto max-h-40">
                                        {!! str($showcase->description)->sanitizeHtml() !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- 2. Section for IMAGE content (displayed in a grid) --}}
                    @if (isset($groupedShowcases['image']))
                        <div class="space-y-4 w-full">
                            <h3 class="text-xl font-semibold text-blue-900/80">Gallery</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                @foreach ($groupedShowcases['image'] as $showcase)
                                    <div class="bg-white/40 backdrop-blur-md rounded-xl shadow-md overflow-hidden">
                                        <img src="{{ $showcase->media_file_url }}"
                                            alt="{{ $showcase->title ?? 'Doctor showcase image' }}"
                                            class="w-full h-40 object-cover transition-transform duration-200 hover:scale-105">
                                        @if ($showcase->description)
                                            <p class="text-xs text-gray-600 mt-1 px-2 py-1">{!! str($showcase->description)->sanitizeHtml() !!}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- 3. Section for VIDEO content --}}
                    @if (isset($groupedShowcases['video']))
                        <div class="space-y-4 w-full">
                            <h3 class="text-xl font-semibold text-blue-900/80">Videos</h3>
                            @foreach ($groupedShowcases['video'] as $showcase)
                                <div class="bg-white/40 backdrop-blur-md rounded-xl shadow-md overflow-hidden">
                                    <video src="{{ $showcase->media_file_url }}" controls
                                        class="w-full h-60 object-cover rounded-lg">
                                        Your browser does not support the video tag.
                                    </video>
                                    @if ($showcase->description)
                                        <p class="text-xs text-gray-600 mt-1 px-2 py-1">{!! str($showcase->description)->sanitizeHtml() !!}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Message if no content --}}
                    @if ($groupedShowcases->isEmpty())
                        <p class="text-gray-600 text-center">More information will be added soon.</p>
                    @endif
                </div>
            </div>


        </div>

        @include('components.microsite.floating-action-menu', ['microsite' => $microsite])



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
