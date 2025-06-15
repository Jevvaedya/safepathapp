<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-500 leading-tight">
            {{ __('Emergency Contacts') }}
        </h2>
    </x-slot>

    <div class="py-7">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 sm:p-10 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-black">
                            {{ __('Edit Emergency Contact') }}
                        </h3>
                    </div>
                    
                    {{-- Formulir dimulai di sini --}}
                    {{-- Variabel yang digunakan di sini adalah $emergencyContact, pastikan konsisten dengan yang dikirim dari Controller --}}
                    <form method="POST" action="{{ route('emergency-contacts.update', $emergencyContact->id) }}">
                        @method('PUT')  {{-- Memberitahu Laravel ini adalah request PUT untuk update --}}
                        @csrf          {{-- Token Keamanan Laravel, WAJIB ADA --}}

                        {{-- Name --}}
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-black">{{ __('Name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 @error('name') border-red-500 @enderror" 
                                   value="{{ old('name', $emergencyContact->name) }}" required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-black">{{ __('Email Address') }} <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 @error('email') border-red-500 @enderror" 
                                   value="{{ old('email', $emergencyContact->email) }}" required>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div class="mb-4">
                            <label for="phone" class="block text-sm font-medium text-black">{{ __('Phone Number') }} <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" id="phone" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 @error('phone') border-red-500 @enderror" 
                                   value="{{ old('phone', $emergencyContact->phone) }}" required>
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        {{-- Relationship (Opsional) --}}
                        <div class="mb-4">
                            <label for="relationship" class="block text-sm font-medium text-black">{{ __('Relationship (Optional)') }}</label>
                            <input type="text" name="relationship" id="relationship" placeholder="E.g., Family, Friend, Colleague" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" 
                                   value="{{ old('relationship', $emergencyContact->relationship) }}">
                        </div>

                        {{-- Is Primary Contact (Checkbox) --}}
                        <div class="mb-6">
                            <label for="is_primary" class="flex items-center">
                                <input type="checkbox" name="is_primary" id="is_primary" value="1" 
                                       class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500"
                                       {{-- Logika untuk checkbox sudah benar, menggunakan fallback ke data database --}}
                                       {{ old('is_primary', $emergencyContact->is_primary) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-black">{{ __('Set as primary contact') }}</span>
                            </label>
                        </div>

                        {{-- Tombol Submit dan Batal --}}
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('emergency-contacts.index') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancel') }}</a>
                            <button type="submit" class="px-4 py-2 bg-black border border-transparent rounded-md font-semibold text-sm text-white hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Save Changes') }}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>