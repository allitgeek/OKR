@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-accent-blue text-start text-base font-medium text-white bg-navy-700 focus:outline-none focus:text-white focus:bg-navy-700 focus:border-accent-blue transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-navy-100 hover:text-white hover:bg-navy-700 hover:border-navy-300 focus:outline-none focus:text-white focus:bg-navy-700 focus:border-navy-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
