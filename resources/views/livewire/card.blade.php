<style>
    /* .ping-animation {
        position: absolute;
        display: inline-flex;
        width: 100%;
        height: 100%;
        border-radius: 9999px;
        opacity: 0.75;
        animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
    }
    .ping-dot-green { background-color: #48bb78; }
    .ping-dot-red { background-color: #f56565; }
    .ping-bg-green { background-color: #68d391; }
    .ping-bg-red { background-color: #fc8181; }

    @keyframes ping {
        75%, 100% {
            transform: scale(2);
            opacity: 0;
        } */
    } */
</style>

@if ($campaign)
    <div class="w-full -mt-8">
        <div class="bg-white shadow-sm overflow-hidden rounded-lg border border-warning-200">
            <div class="p-4 md:p-6 relative">
                
                <div class="flex m-4 justify-between items-center">
                <p class="text-xs">0 days left</p>    
                <p class="text-xs">Live</p>
                </div>

                <div class="mt-4 flex flex-col md:flex-row items-center justify-between">
                    <div>
                        <h2 class="text-md md:text-2xl font-bold text-info-900">{{ $campaign->name }}</h2>
                        <!-- <p class="text-xs md:text-sm text-gray-600">
                            {{ $campaign->is_active ? 'Active Campaign' : 'Inactive Campaign' }} &bull; Healthcare
                        </p> -->
                    </div>
                    <!-- <div class="flex-shrink-0 mt-4 md:mt-0">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100 text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div> -->
                </div>

                <div class="mt-6 flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mt-4 mb-2 md:mt-0 w-full md:w-auto">
                        <a href="#" class="block text-center rounded-lg bg-primary-200 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-300 w-full">View Details</a>
                    </div>    
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2">
                        <div class="flex items-center text-gray-800">
                            <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.368 2.448a1 1 0 00-.364 1.118l1.287 3.957c.3.921-.755 1.688-1.54 1.118l-3.368-2.448a1 1 0 00-1.176 0l-3.368 2.448c-.784.57-1.838-.197-1.539-1.118l1.287-3.957a1 1 0 00-.364-1.118L2.35 9.384c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69L9.049 2.927z"></path></svg>
                            <span class="ml-1 text-xs md:text-sm">4.8</span>
                        </div>
                        <div class="text-xs md:text-sm text-gray-500 hidden md:block">&bull;</div>
                        <div class="text-xs md:text-sm text-gray-800">
                            <span class="font-semibold">{{ $participants }}</span> participants
                        </div>
                        <div class="text-xs md:text-sm text-gray-500 hidden md:block">&bull;</div>
                        <div class="text-xs md:text-sm text-gray-800">
                            <span class="font-semibold">{{ $entriesToday }}</span> entries today
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@else
    <div class="rounded-lg bg-white p-6 text-center shadow-md">
        <p class="text-gray-500">No active campaigns at the moment.</p>
    </div>
@endif
