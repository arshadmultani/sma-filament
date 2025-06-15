<!-- If using Vite (recommended for Laravel 9+): -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@php
    $doctorCount = \App\Models\Doctor::count();
    $chemistCount = \App\Models\Chemist::count();
    $totalCustomers = $doctorCount + $chemistCount;
@endphp

<div class="mt-10 flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="mb-4">
            <div class="rounded-2xl border p-6 flex items-center justify-between mb-4 bg-gradient-to-r from-green-700 to-emerald-500">
                <div>
                    <div class="text-1xl font-medium text-white leading-tight">Total<br/>Customers</div>
                </div>
                <div class="text-white text-4xl font-bold">{{ $totalCustomers }}</div>
            </div>
            <div class="flex gap-4 mb-4 justify-between">
                <x-stat-card :value="$doctorCount" label="Doctors" color="text-white" labelColor="text-white" bgColor="bg-gradient-to-r from-cyan-500 to-blue-500" class="flex-1" />
                <x-stat-card :value="$chemistCount" label="Chemists" color="text-white" labelColor="text-white" bgColor="bg-gradient-to-r from-blue-500 to-cyan-500" class="flex-1" />
            </div>
            <hr class="my-4" style="margin:50px" />
            <div class="flex gap-4">
                <x-generic-action-button label="Doctor"  bgColor="bg-gradient-to-r from-green-700 to-emerald-500" leftIcon="heroicon-o-plus" action="window.location.href='{{ route('filament.admin.resources.doctors.create') }}'" />
                <x-generic-action-button label="Doctor" bgColor="bg-gradient-to-r from-green-700 to-emerald-500" leftIcon="heroicon-o-eye" action="window.location.href='{{ route('filament.admin.resources.doctors.index') }}'" />
            </div>

            <div class="flex gap-4 mt-4">
            <x-generic-action-button label=" Chemist" bgColor="bg-gradient-to-r from-green-700 to-emerald-500" leftIcon="heroicon-o-plus" action="window.location.href='{{ route('filament.admin.resources.chemists.create') }}'" />

                <x-generic-action-button label="Chemist" bgColor="bg-gradient-to-r from-green-700 to-emerald-500" leftIcon="heroicon-o-eye" action="window.location.href='{{ route('filament.admin.resources.chemists.index') }}'" />
            </div>
        </div>
    </div>
</div>
