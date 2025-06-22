<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. {{ $microsite->doctor->name }}'s Microsite</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="w-full">
        <!-- Header Section -->
        <header class="relative text-white overflow-hidden" style="background-color: #0F766E;">
            <div class="container mx-auto px-6 pt-8 pb-20 md:pb-32 z-10 relative">
                <div class="flex flex-col md:flex-row items-center">
                    <!-- Profile Picture -->
                    <div class="flex-shrink-0 w-28 h-28 md:w-48 md:h-48 mb-6 md:mb-0 md:mr-10">
                        <img class="rounded-full w-full h-full object-cover border-4 border-white shadow-lg" 
                             src="https://ui-avatars.com/api/?name={{ urlencode($microsite->doctor->name) }}&color=FFFFFF&background=0F766E&size=160" 
                             alt="Dr. {{ $microsite->doctor->name }}">
                    </div>
                    <!-- Doctor Info -->
                    <div class="text-center pb-3 md:text-left">
                        <h1 class="text-lg md:text-5xl font-bold">Dr. {{ $microsite->doctor->name }}</h1>
                        <!-- <p class="text-lg md:text-xl opacity-90 mt-1">{{ $microsite->doctor->qualification->name ?? 'Degree' }}</p> -->
                    </div>
                </div>
            </div>

            <!-- Div-based Wave Shape -->
            <div class="absolute bottom-0 left-0 w-full">
                <!-- Lighter Teal Wave -->
                <div class="absolute bottom-0 w-full h-24" style="transform: scaleX(1.5);">
                    <div class="h-full w-full" style="background-color: #14B8A6; border-radius: 100% 100% 0 0;"></div>
                </div>
                <!-- Page Background Wave -->
                <div class="absolute bottom-0 w-full h-20" style="transform: scaleX(1.5);">
                    <div class="h-full w-full" style="background-color: #F9FAFB; border-radius: 100% 100% 0 0;"></div>
                </div>
            </div>
        </header>

        <!-- Content Section -->
        <main class="bg-gray-50">
            <div class="container mx-auto px-3 py-8">
                <!-- Action Button & Details -->
                <div class="bg-white rounded-lg shadow-lg p-3 -mt-20 relative z-20">
                    <div class="flex justify-start mb-6">
                         <a href="#about" class="text-white font-bold text-sm px-2 py-2 border rounded-lg shadow-md transform hover:scale-105 transition-transform duration-300" style="border-color: #0F766E; color: #0F766E;">
                            ABOUT ME
                        </a>
                    </div>
                    <div class="text-gray-700">
                        <ul class="list-disc list-inside text-left font-semibold text-md">
                            <li>{{ $microsite->doctor->qualification->name ?? 'Degree' }}</li>
                            <li>{{ $microsite->doctor->specialty->name ?? 'Specialization' }}</li>
                            <li>Expertise</li>
                        </ul>
                    </div>
                </div>
                
                <!-- About Section -->
                <div id="about" class="bg-white rounded-lg shadow-lg p-8 mt-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">About Dr. {{ $microsite->doctor->name }}</h2>
                    <p class="text-gray-600 leading-relaxed max-w-3xl">
                        Detailed biography and professional information about the doctor will be displayed here. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed neque elit, tristique placerat feugiat ac, facilisis vitae arcu.
                    </p>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 
