<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-accent-orange hover:bg-orange-700 border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-700 transition ease-in-out duration-150']) }}>
    {{-- Ganti text-gray-800 menjadi text-white, sesuaikan hover dan focus ring dengan turunan oranye --}}
    {{-- Untuk hover:bg-orange-700 dan focus:ring-orange-700, kamu mungkin perlu definisikan warna orange-700 di tailwind.config.js jika belum ada, atau pilih shade lain dari accent-orange --}}
    {{ $slot }}
</button>