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
                <p class="text-md text-gray-500">{{ $microsite->doctor->qualifications ?? 'MBBS, MD' }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-6 text-center">
                <div class="bg-blue-50 p-3 rounded-xl">
                    <p class="font-bold text-blue-600 text-xl">{{ $microsite->doctor->experience_years ?? '10+' }}</p>
                    <p class="text-sm text-blue-500">Years Experience</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-xl">
                    <p class="font-bold text-blue-600 text-xl">{{ $microsite->doctor->specialties ?? 'Cardiology' }}</p>
                    <p class="text-sm text-blue-500">Specialty</p>
                </div>
            </div>
            <div class="bg-gray-100 p-3 rounded-xl mt-4 text-center">
                <p class="font-semibold text-gray-700">{{ $microsite->doctor->clinic_name ?? 'City General Hospital' }}
                </p>
                <p class="text-sm text-gray-500">Clinic/Hospital</p>
            </div>
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
                    {{ $microsite->doctor->about ??
                        'Dr. ' .
                            $microsite->doctor->name .
                            'Ex do quis ea id ex ad culpa eu aute reprehenderit nulla cupidatat ad. Pariatur aliqua Lorem qui ullamco culpa et excepteur ad tempor irure sunt minim. Ad sit do in cillum quis anim mollit exercitation duis ipsum reprehenderit aute ipsum. Proident eu ex mollit nulla quis nisi reprehenderit nisi labore Lorem occaecat sit in.
                    
                    Culpa laboris duis exercitation mollit. Deserunt sit ex veniam adipisicing nostrud et quis do ullamco irure aliquip amet laborum reprehenderit. Voluptate et nisi duis magna culpa quis aliqua aliquip nostrud culpa. Incididunt do nulla ullamco. Tempor officia occaecat dolor ad magna pariatur culpa eiusmod cupidatat reprehenderit Lorem in minim elit. Esse laboris incididunt veniam sunt ut minim duis exercitation est magna sunt. Amet sit aliqua pariatur nulla officia officia id ea mollit consectetur.
                    
                    Elit velit excepteur quis incididunt ipsum occaecat esse adipisicing consectetur officia non ea mollit. Magna eu laboris non. Consectetur non fugiat excepteur aliquip irure irure dolore proident et. Nostrud ad proident duis ad magna velit magna reprehenderit deserunt ex ipsum. Nisi minim nulla labore labore tempor.
                    
                    Ex enim enim incididunt labore. Ipsum dolore voluptate dolore duis laboris dolore ipsum non velit sint. In do fugiat velit deserunt adipisicing minim aute culpa sint laborum amet cillum veniam dolor. Nostrud consequat aute proident ad anim cillum aliqua.
                    
                    Quis ea nostrud tempor laboris sunt velit. Dolor voluptate eu tempor dolor laborum. Cupidatat consequat aliquip deserunt eiusmod exercitation dolor ea sunt commodo dolor. Occaecat ut non aliqua amet amet ullamco tempor consectetur id eu consectetur ipsum reprehenderit id. Adipisicing aliqua ut mollit anim ullamco veniam ex cillum tempor ex tempor culpa elit. Sint ullamco aute incididunt ipsum nisi exercitation esse velit. Lorem irure proident sint ullamco nisi tempor sunt elit ad ex adipisicing elit occaecat veniam aliquip. Nisi ut anim in dolore irure commodo ex.' }}
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
