@props(['label', 'action' => null, 'leftIcon' => null, 'rightIcon' => null, 'bgColor' => 'bg-white'])
<div type="button" onclick="{{ $action }}" class="w-full flex items-center justify-center gap-3 rounded-2xl py-4 px-4 text-md font-semibold text-white hover:bg-gray-50 transition mb-4 {{ $bgColor }}">
    @if($leftIcon)
        <x-dynamic-component :component="$leftIcon" class="text-white w-6 h-6" />
    @endif
    <span>{{ $label }}</span>
    @if($rightIcon)
        <x-dynamic-component :component="$rightIcon" class="text-white w-7 h-7" />
    @endif
</div> 