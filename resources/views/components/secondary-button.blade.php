<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-white dark:bg-[#161615] border border-[#e5e5e0] dark:border-[#3a3a38] rounded-md font-semibold text-xs text-[#6b6b66] dark:text-[#a8a8a2] uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-[#1a1a18] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-[#0a0a0a] disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
