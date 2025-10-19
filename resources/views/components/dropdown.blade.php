@props(['align' => 'right', 'width' => '48', 'contentClasses' => ''])

@php
    $alignmentClasses = match($align) {
        'left' => 'origin-top-left left-0',
        'right' => 'origin-top-right right-0',
        default => 'origin-top',
    };

    $widthClasses = match($width) {
        '48' => 'w-48',
        '56' => 'w-56',
        '64' => 'w-64',
        default => 'w-48',
    };
@endphp

<div x-data="{ open: false }" class="relative">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div
        x-show="open"
        x-transition.origin.top.right
        @click.away="open = false"
        @keydown.escape.window="open = false"
        class="absolute z-50 mt-2 {{ $widthClasses }} {{ $alignmentClasses }}"
        style="display: none;"
    >
        <div class="rounded-xl bg-white shadow-xl ring-1 ring-slate-900/10 border border-slate-100 overflow-hidden">
            {{ $content }}
        </div>
    </div>
</div>
