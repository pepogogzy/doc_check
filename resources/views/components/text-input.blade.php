@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-[#e5e5e0] dark:border-[#3a3a38] bg-white dark:bg-[#161615] text-[#111110] dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
