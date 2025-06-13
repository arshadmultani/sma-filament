@props(['tabs' => []])
<div class="w-full border-b bg-white flex items-center justify-between px-4" style="min-height: 56px;">
    <div class="flex flex-1">
        @foreach ($tabs as $tab)
            <a href="{{ $tab['url'] }}"
               class="flex flex-row items-center gap-2 justify-center px-4 py-2 transition-colors duration-150 min-w-[80px] rounded-t-md
                   {{ $tab['active'] ? 'bg-emerald-600 text-white font-semibold' : 'bg-white text-emerald-600 hover:bg-emerald-50' }}"
            >
                @if (!empty($tab['icon']))
                    <x-dynamic-component :component="$tab['icon']" class="w-5 h-5 {{ $tab['active'] ? 'text-white' : 'text-emerald-600' }}" />
                @endif
                <span class="text-xs">{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>
</div> 