@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-indigo-500 text-start text-base font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-300 focus:bg-indigo-100 dark:focus:bg-indigo-900/30 focus:border-indigo-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-[#6b6b66] dark:text-[#a8a8a2] hover:text-[#111110] dark:hover:text-white hover:bg-gray-50 dark:hover:bg-[#1a1a18] hover:border-[#e5e5e0] dark:hover:border-[#3a3a38] focus:outline-none focus:text-[#111110] dark:focus:text-white focus:bg-gray-50 dark:focus:bg-[#1a1a18] focus:border-[#e5e5e0] dark:focus:border-[#3a3a38] transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
