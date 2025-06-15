<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-500 leading-tight">
            {{ __('Emergency Contacts') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">
                    {{-- Pesan sukses ini sudah sangat baik, menggunakan Alpine.js untuk menghilang otomatis --}}
                    @if (session('success'))
                        <div class="mb-6 p-3 sm:p-4 bg-green-100 border border-green-300 text-green-700 rounded-md shadow-sm transition-opacity duration-500 ease-out text-sm sm:text-base" role="alert" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                            {{ session('success') }}
                            <button type="button" @click="show = false" class="ml-3 sm:ml-4 text-green-800 hover:text-green-900 focus:outline-none text-lg sm:text-xl font-normal">&times;</button>
                        </div>
                    @endif

                    <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                        <h3 class="text-lg sm:text-xl font-bold text-black">
                            {{ __('Saved Contacts') }}
                        </h3>
                        {{-- Tombol ini sudah benar mengarah ke route 'create' --}}
                        <a href="{{ route('emergency-contacts.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-black border border-transparent rounded-md font-semibold text-sm text-white hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Add New Contact') }}
                        </a>
                    </div>

                    {{-- AWAL BAGIAN DAFTAR KONTAK --}}
                    {{-- Pengecekan jika kontak kosong adalah praktik yang sangat baik --}}
                    @if ($contacts->isEmpty())
                        <p class="text-gray-600 py-4 text-sm sm:text-base">{{ __('You have not added any emergency contacts yet.') }}</p>
                    @else
                        <div class="border-t border-gray-200">
                            {{-- Menggunakan <ul> untuk daftar adalah pilihan semantik yang bagus --}}
                            <ul role="list" class="divide-y divide-gray-200">
                                @foreach ($contacts as $contact)
                                    <li class="py-4">
                                        {{-- Layout responsif (flex-col di mobile, flex-row di desktop) sudah diimplementasikan dengan benar --}}
                                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                                            {{-- Bagian Informasi Kontak --}}
                                            <div class="min-w-0 flex-1 mb-3 sm:mb-0">
                                                <p class="text-base font-semibold text-black truncate">
                                                    {{ $contact->name }}
                                                </p>
                                                {{-- Semua detail kontak ditampilkan dengan rapi --}}
                                                <p class="text-sm text-gray-500 truncate">
                                                    <span class="font-medium">{{ __('Phone:') }}</span> {{ $contact->phone }}
                                                </p>
                                                @if ($contact->email)
                                                    <p class="text-sm text-gray-500 truncate">
                                                        <span class="font-medium">{{ __('Email:') }}</span> {{ $contact->email }}
                                                    </p>
                                                @endif
                                                @if ($contact->relationship)
                                                    <p class="text-sm text-gray-500 truncate">
                                                        <span class="font-medium">{{ __('Relationship:') }}</span> {{ $contact->relationship }}
                                                    </p>
                                                @endif
                                                {{-- Penanda kontak 'Primary' juga sudah bagus --}}
                                                @if($contact->is_primary)
                                                    <div class="mt-2">
                                                        <span class="px-2 py-0.5 inline-block text-xs leading-4 font-semibold rounded-full bg-green-100 text-green-800">
                                                            {{ __('Primary') }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Bagian Tombol Aksi --}}
                                            <div class="flex-shrink-0 flex flex-row sm:flex-col space-x-2 sm:space-x-0 sm:space-y-2 w-full sm:w-auto sm:items-end mt-2 sm:mt-0">
                                                {{-- Tombol Edit mengarah ke route 'edit' dengan parameter ID --}}
                                                <a href="{{ route('emergency-contacts.edit', $contact->id) }}"
                                                   class="flex-1 sm:flex-none inline-flex items-center justify-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                                    {{ __('Edit') }}
                                                </a>
                                                {{-- Form untuk delete adalah cara yang tepat untuk keamanan (mencegah CSRF) --}}
                                                <form action="{{ route('emergency-contacts.destroy', $contact->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this contact: ') . addslashes($contact->name) . '?' }}');" class="inline-block flex-1 sm:flex-none w-full sm:w-auto">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="w-full inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-500 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        
                        {{-- INI TAMBAHANNYA: Mengaktifkan link pagination --}}
                        {{-- Pastikan di Controller Anda menggunakan ->paginate() BUKAN ->get() atau ->all() --}}
                        <div class="mt-6">
                             {{ $contacts->links() }}
                        </div>

                    @endif
                    {{-- AKHIR BAGIAN DAFTAR KONTAK --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>