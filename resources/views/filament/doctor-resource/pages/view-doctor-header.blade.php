@php
    $icon = match ($record->status) {
        'Pending' => '<p>Pending</p>',
        'Approved' => '<p>Approved</p>',
        'Rejected' => '<p>Rejected</p>',
        default => '',
    };
   @endphp

<div class="flex items-center justify-between w-full">
    <div>
        <p class="text-2xl font-bold leading-7">
            Dr. {{ $record->name }}
        </p>
        <span class="text-sm">{!! $icon !!}</span>
    </div>
    <div>
        @foreach ($actions as $action)
            {{ $action }}
        @endforeach
    </div>
</div>