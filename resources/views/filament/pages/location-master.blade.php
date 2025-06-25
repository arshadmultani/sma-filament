<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Render the form --}}
        {{ $this->form }}

        <x-filament::button wire:click="create" type="button" color="primary">
            Create Location Hierarchy
        </x-filament::button>

        <hr />

        {{-- Render the table --}}
        {{ $this->table }}
    </div>
</x-filament-panels::page>
