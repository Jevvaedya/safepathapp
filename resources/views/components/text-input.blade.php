@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => 'border-primary bg-gray-100 text-black focus:border-primary focus:ring-primary rounded-md shadow-sm w-full'
]) !!}>