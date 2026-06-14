@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-500 text-sm font-medium leading-5 text-[#111110] dark:text-white focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-[#6b6b66] dark:text-[#a8a8a2] hover:text-[#111110] dark:hover:text-white hover:border-[#e5e5e0] dark:hover:border-[#3a3a38] focus:outline-none focus:text-[#111110] dark:focus:text-white focus:border-[#e5e5e0] dark:focus:border-[#3a3a38] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
