<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-500 leading-tight">
            {{ __('Emergency Contacts') }}
        </h2>
    </x-slot>

    <div class="py-7">
        <div class="max-w-7xl mx-auto sm:px- lg:px-8">
            <div class="bg-white overflow-hidden sm:rounded-lg">
                <div class="pt-0 px-10 pb-12 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-black">
                            {{ __('Add New Emergency Contact') }}
                        </h3>
                    </div>

                    {{-- Formulir --}}
                    <form method="POST" action="{{ route('emergency-contacts.store') }}">
                        @csrf {{-- Token Keamanan Laravel, WAJIB ADA untuk form POST --}}

                        {{-- Name --}}
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-black">{{ __('Name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" value="{{ old('name') }}" required>
                            {{-- Nanti di sini bisa ditambahkan pesan error validasi --}}
                        </div>

                        {{-- Email (Opsional) --}}
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-black">{{ __('Email Address') }} <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 @error('email') border-red-500 @enderror" 
                                value="{{ old('email') }}" required> {{-- Tambahkan 'required' --}}
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-black">{{ __('Phone Number') }} <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" id="phone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" value="{{ old('phone') }}" required>
                        </div>

                        

                        {{-- Relationship (Opsional) --}}
                        <div class="mb-4">
                            <label for="relationship" class="block text-sm font-medium text-black">{{ __('Relationship (Optional)') }}</label>
                            <input type="text" name="relationship" id="relationship" placeholder="E.g., Family, Friend, Colleague" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" value="{{ old('relationship') }}">
                        </div>

                        {{-- Is Primary Contact (Checkbox) --}}
                        <div class="mb-6">
                            <label for="is_primary" class="flex items-center">
                                <input type="checkbox" name="is_primary" id="is_primary" value="1" 
                                    class="rounded border-gray-300 text-green-600" 
                                    {{-- text-primary akan mengubah warna centang/lingkaran dalam menjadi ungu --}}
                                    {{-- focus:ring-primary akan mengubah warna cincin fokus menjadi ungu --}}
                                    {{ old('is_primary', $emergencyContact->is_primary ?? false) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Set as primary contact') }}</span>
                            </label>
                        </div>

                        {{-- Tombol Submit dan Batal --}}
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('emergency-contacts.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancel') }}</a>
                            <button type="submit" class="px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Save Contact') }}
                            </button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 