<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $pageTitle ?? __('Custom Keywords') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Form untuk menambah keyword --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Keyword Baru</h3>
                        
                        @if (session('success'))
                            <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded-md">{{ session('success') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="mb-4 p-4 bg-red-100 text-red-700 border border-red-300 rounded-md">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('keywords.store') }}" method="POST">
                            @csrf
                            <div class="flex items-center space-x-4">
                                <input type="text" name="keyword" id="keyword" class="mt-1 block w-full sm:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: bahaya" required>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700">
                                    Simpan
                                </button>
                            </div>
                            @error('keyword')
                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                            @enderror
                        </form>
                    </div>

                    {{-- Daftar keyword yang sudah ada --}}
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Keyword Anda Saat Ini</h3>
                        @if ($keywords->isEmpty())
                            <p class="text-gray-500">Anda belum memiliki keyword kustom.</p>
                        @else
                            <ul class="space-y-3">
                                @foreach ($keywords as $kw)
                                    <li class="flex items-center justify-between p-3 bg-gray-50 rounded-md border">
                                        <span class="font-mono text-gray-700">{{ $kw->keyword }}</span>
                                        <form action="{{ route('keywords.destroy', $kw->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-semibold">
                                                Hapus
                                            </button>
                                        </form>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>