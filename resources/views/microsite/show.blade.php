<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. {{ $microsite->doctor->name }}'s Microsite</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Funnel+Sans:ital,wght@0,300..800;1,300..800&display=swap"
        rel="stylesheet">
    <style>
        body {
            background-color: {{ $microsite->bg_color ?? '#24669B' }};
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

<body class="font-[Funnel Sans] ">

    @if ($microsite->is_active)
        <div class="max-w-md mx-auto my-0">
            <div class="bg-white/60 backdrop-blur-md rounded-xl m-2 p-2 shadow-md">
                <div class="w-full flex flex-col items-center justify-around h-full">
                    <div class="p-2">
                        <img src="{{ $microsite->doctor->profile_photo_url }}" alt="{{ $microsite->doctor->name }}"
                            class="rounded-full w-24 h-24 sm:w-32 sm:h-32 object-cover mb-2 border-2 border-sky-200" />
                    </div>

                    <p class="p-2 font-semi-bold text-3xl sm:text-3xl">Dr. {{ $microsite->doctor->name }}</p>

                    <div
                        class="bg-white/20 backdrop-blur-md border border-white/30 shadow-md
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
                    class="bg-white/60 backdrop-blur-md border-white/30 rounded-2xl shadow-xl p-4 sm:p-6 space-y-6 w-full">

                    <h2
                        class="flex justify-center p-2 text-xl font-semibold shadow-md backdrop-blur-md rounded-xl text-gray-800">
                        About
                    </h2>

                    {{-- 1. Section for TEXT content --}}
                    @if (isset($groupedShowcases['text']))
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold border-b border-gray-200 mx-auto p-2">
                                Information</h3>
                            @foreach ($groupedShowcases['text'] as $showcase)
                                <div
                                    class="bg-white/40 backdrop-blur-md rounded-xl p-4 shadow-md prose max-w-none text-gray-700">
                                    <div class="font-medium text-lg mb-3 border-b">{{ $showcase->title }}</div>
                                    <div class="overflow-auto">
                                        {!! str($showcase->description)->sanitizeHtml() !!}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- 2. Section for IMAGE content (displayed in a grid) --}}
                    @if (isset($groupedShowcases['image']))
                        <div class="space-y-4 w-full">
                            <h3 class="text-lg font-semibold border-b border-gray-200 mx-auto p-2">
                                Gallery</h3>
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
                            <h3 class="text-lg font-semibold border-b border-gray-200 mx-auto p-2">
                                Videos</h3>
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

            {{-- REVIEWS --}}
            <div class="p-2">
                <div class="bg-white/60 backdrop-blur-md rounded-2xl shadow-xl p-4 sm:p-6 space-y-6 w-full">

                    <h2
                        class="flex justify-center p-2 text-xl font-semibold shadow-md backdrop-blur-md rounded-xl text-gray-800">
                        Reviews
                    </h2>
                    @if ($microsite->doctor->reviews->isEmpty())
                        <p class="text-gray-600 text-center">No reviews yet. Be the first to leave a review!</p>
                    @endif
                    @foreach ($microsite->doctor->reviews as $review)
                        <div class="border-b border-gray-200 p-4 bg-white/40 backdrop-blur-md rounded-lg  shadow-sm">
                            <div class="flex items-center justify-between my-2 py-2 border-b">
                                <h3 class="font-semibold">{{ $review->reviewer_name }}</h3>
                                <p class="font-light text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}
                                </p>
                            </div>

                            @if ($review->review_text)
                                <p class="text-sm text-gray-600">{{ $review->review_text }}</p>
                            @endif
                            @if ($review->media_type == 'image' && $review->media_file_url)
                                <img src="{{ $review->media_file_url }}" alt="Review Image"
                                    class="mt-2 w-full h-40 object-cover rounded-lg">
                            @elseif($review->media_type == 'video' && $review->media_file_url)
                                <video src="{{ $review->media_file_url }}" controls
                                    class="mt-2 w-full h-60 object-cover rounded-lg">
                                    Your browser does not support the video tag.
                                </video>
                            @endif

                        </div>
                    @endforeach

                </div>
            </div>
        </div>
        <div class="m-[80px]">
            @include('components.microsite.floating-action-menu', ['microsite' => $microsite])
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
