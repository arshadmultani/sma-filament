<div>
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-x-2">
            <h2 class="text-sm font-semibold">{{ $title }}</h2>
            <!-- <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ $campaignCount }}
            </span> -->
            <x-filament::badge size="sm" color="success">
                {{ $campaignCount }}
            </x-filament::badge>
        </div>
        <!-- <div>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </div> -->
    </div>
</div>