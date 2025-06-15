<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-black hover:bg-gray-900 border border-transparent rounded-md font-bold text-sm text-white tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-dark transition ease-in-out duration-150']) }}>
    {{-- Warna latar: bg-primary (ungu #cb5bff) --}}
    {{-- Warna hover: bg-primary-dark (ungu lebih gelap) --}}
    {{-- Warna teks: text-text-main (hitam #000000) untuk kontras yang baik dengan ungu terang --}}
    {{-- Focus ring: focus:ring-primary-dark --}}
    {{ $slot }}
</button>