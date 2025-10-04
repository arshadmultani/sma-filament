<div class="p-6 bg-red-500">
    <div class="flex flex-col items-center text-center">
        <img class="w-28 h-28 rounded-full object-cover border-4 border-blue-100 shadow-lg"
            src="{{ $microsite->doctor->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($microsite->doctor->name) . '&color=7F9CF5&background=EBF4FF' }}"
            alt="Dr. {{ $microsite->doctor->name }}">
        <h1 class="text-3xl font-bold text-gray-800 mt-4">{{ $microsite->doctor->name }}</h1>
        {{-- <p class="text-md text-gray-500">{{ $microsite->doctor->qualification->name ?? 'MBBS, MD' }}</p> --}}
    </div>
</div>
