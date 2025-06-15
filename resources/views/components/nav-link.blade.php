@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-primary shadow-md text-sm font-extrabold leading-5 text-white focus:outline-none focus:border-white focus:ring-0 transition duration-150 ease-in-out' // Aktif: ditambahkan focus:ring-0
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-300 hover:text-white hover:border-gray-300 focus:outline-none focus:text-white focus:border-gray-300 focus:ring-0 transition duration-150 ease-in-out'; // Tidak aktif: ditambahkan focus:ring-0
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>