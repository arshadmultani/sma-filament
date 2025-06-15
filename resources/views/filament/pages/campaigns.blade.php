@vite(['resources/css/app.css', 'resources/js/app.js'])
@php
    $activeCampaigns = \App\Models\KofolCampaign::where('is_active', true)->count();
    $entryCount = \App\Models\KofolEntry::count();
@endphp

<div class="mt-10 flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="flex flex-col gap-4">
            <x-stat-card :value="$activeCampaigns" label="Active Campaigns" color="text-white" labelColor="text-white"
                bgColor="bg-gradient-to-r from-green-700 to-emerald-500" class="flex-1" />

            <x-stat-card :value="$entryCount" label="Total Submissions" color="text-white" labelColor="text-white"
                bgColor="bg-gradient-to-r from-green-700 to-emerald-500" class="flex-1" />
           
           
            <hr class="my-4" style="margin:50px" />
            <div class="flex gap-4">
                <x-generic-action-button label="Add Submission" bgColor="bg-gradient-to-r from-green-700 to-emerald-500"
                    leftIcon="heroicon-o-plus"
                    action="window.location.href='{{ route('filament.admin.resources.kofol-entries.create') }}'" />
            </div>
            <div class="flex gap-4 mt-4">
                <x-generic-action-button label="View Submissions"
                    bgColor="bg-gradient-to-r from-green-700 to-emerald-500" leftIcon="heroicon-o-eye"
                    action="window.location.href='{{ route('filament.admin.resources.kofol-entries.index') }}'" />
            </div>
        </div>
    </div>
</div>