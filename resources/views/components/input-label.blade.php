@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-[#111110] dark:text-white']) }}>
    {{ $value ?? $slot }}
</label>
