@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-s-4 border-kk-nav-fg bg-white/10 py-2 pe-4 ps-3 text-start text-base font-medium text-white focus:outline-none focus:bg-white/15 transition duration-150 ease-in-out'
            : 'block w-full border-s-4 border-transparent py-2 pe-4 ps-3 text-start text-base font-medium text-kk-nav-muted transition duration-150 ease-in-out hover:border-white/20 hover:bg-white/5 hover:text-kk-nav-fg focus:outline-none focus:bg-white/5';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
