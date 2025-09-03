<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. {{ $microsite->doctor->name }}'s Microsite</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            background-color: #E5E7EB;
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

    <div class="mobile-container" x-data="{ activeTab: 'about' }">

        <!-- Header Section -->
        <header class="p-6 bg-white rounded-b-3xl shadow-md">
            <div class="flex flex-col items-center text-center">
                <img class="w-28 h-28 rounded-full object-cover border-4 border-blue-100 shadow-lg"
                    src="{{ $microsite->doctor->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($microsite->doctor->name) . '&color=7F9CF5&background=EBF4FF' }}"
                    alt="Dr. {{ $microsite->doctor->name }}">
                <h1 class="text-3xl font-bold text-gray-800 mt-4">{{ $microsite->doctor->name }}</h1>
                {{-- <p class="text-md text-gray-500">{{ $microsite->doctor->qualification->name ?? 'MBBS, MD' }}</p> --}}
            </div>
            <div class="grid grid-cols-2 gap-4 mt-6 text-center">
                <div class="bg-blue-50 p-3 rounded-xl">
                    <p class="font-bold text-blue-600 text-xl">{{ $microsite->doctor->experience ?? '10+' }}</p>
                    <p class="text-sm text-blue-500">Years Experience</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-xl">
                    <p class="font-bold text-blue-600 text-xl">{{ $microsite->doctor->specialty->name ?? 'Cardiology' }}
                    </p>
                    <p class="text-sm text-blue-500">Specialty</p>
                </div>
            </div>
            {{-- <div class="bg-gray-100 p-3 rounded-xl mt-4 text-center">
                <p class="font-semibold text-gray-700">{{ $microsite->doctor->clinic_name ?? 'City General Hospital' }}
                </p>
                <p class="text-sm text-gray-500">Clinic/Hospital</p>
            </div> --}}
        </header>

        <!-- Tabs Navigation -->
        <nav class="flex justify-around bg-white border-b border-gray-200 sticky top-0 z-10">
            <button @click="activeTab = 'about'" :class="{ 'tab-active': activeTab === 'about' }"
                class="flex-1 py-4 px-2 text-center text-gray-500 font-medium tab">
                About
            </button>
            <button @click="activeTab = 'reviews'" :class="{ 'tab-active': activeTab === 'reviews' }"
                class="flex-1 py-4 px-2 text-center text-gray-500 font-medium tab">
                Reviews
            </button>
        </nav>

        <!-- Tab Content -->
        <main class="p-6 pb-24">
            <!-- About Section -->
            <div x-show="activeTab === 'about'" x-transition>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">About Dr. {{ $microsite->doctor->name }}</h2>
                <p class="text-gray-600 leading-relaxed">
                    {{ $microsite->doctor->about ?? 'Dr. ' . $microsite->doctor->name . ' Excepteur ullamco magna eu commodo pariatur eu commodo voluptate qui ut cupidatat dolore.' }}
                </p>
            </div>

            <!-- Reviews Section -->
            <div x-show="activeTab === 'reviews'" x-transition>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Patient Reviews</h2>
                <div class="space-y-4">
                    @forelse ($microsite->doctor->reviews ?? [] as $review)
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                            <div class="flex items-center mb-2">
                                <div class="flex text-yellow-400">
                                    @for ($i = 0; $i < 5; $i++)
                                        <svg class="w-5 h-5 {{ $i < $review->rating ? 'fill-current' : 'text-gray-300' }}"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path
                                                d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z" />
                                        </svg>
                                    @endfor
                                </div>
                                <span class="ml-2 text-gray-600 font-semibold">{{ $review->rating }}.0</span>
                            </div>
                            <p class="text-gray-600">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500">No reviews yet.</p>
                    @endforelse
                </div>
            </div>
        </main>

        <!-- Floating Action Menu -->
        <x-microsite.floating-action-menu :microsite="$microsite" />

    </div>

</body>

</html>
