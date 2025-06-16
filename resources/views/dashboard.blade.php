{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-500 leading-tight">
            {{ $pageTitle ?? __('SOS Alerts') }}
        </h2>
    </x-slot>

    {{-- Elemen Audio untuk SOS Alarm --}}
    <audio id="dashboardSosAlertSound" src="{{ asset('audio/sos_alarm.mp3') }}" preload="auto"></audio>

    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="space-y-8">

                {{-- Bagian 1: Konten SOS Utama dengan Toggle Switch --}}
                <div class="p-4 sm:p-6 text-gray-900 text-center bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-6 sm:mb-8 text-text-main">
                        {{ __("In an Emergency Situation?") }}
                    </h2>
                    
                    <button id="sosButton"
                            class="w-32 h-32 sm:w-36 sm:h-36 md:w-40 md:h-40 mx-auto
                                   bg-gradient-to-br from-accent-orange via-red-500 to-primary
                                   text-white font-bold text-2xl sm:text-3xl md:text-4xl
                                   rounded-full shadow-xl flex items-center justify-center
                                   transition duration-150 ease-in-out transform hover:scale-110 hover:opacity-90
                                   focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-primary-dark focus:ring-opacity-50">
                        SOS
                    </button>

                    <div x-data="{ isVoiceActive: false }" class="mt-8 flex flex-col items-center">
                        <div class="flex items-center space-x-3">
                            <span class="text-gray-600 font-medium">Aktivasi via Suara</span>
                            <button @click="isVoiceActive = !isVoiceActive; toggleVoiceRecognition(isVoiceActive)"
                                    type="button"
                                    class="relative inline-flex flex-shrink-0 h-7 w-12 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    :class="isVoiceActive ? 'bg-indigo-600' : 'bg-gray-300'"
                                    role="switch"
                                    :aria-checked="isVoiceActive.toString()">
                                <span aria-hidden="true"
                                      class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                      :class="{ 'translate-x-5': isVoiceActive, 'translate-x-0': !isVoiceActive }">
                                </span>
                            </button>
                        </div>
                        <p id="voiceStatus" class="text-sm text-gray-500 mt-2 min-h-[20px]"></p>
                        <div id="activeKeywordsArea" class="text-xs text-gray-500 mt-1 hidden">
                            <span class="font-semibold">Kata Kunci Aktif:</span>
                            <span id="keywordsList"></span>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium mb-4">{{ __('Set Your Emergency Keywords') }}</h3>

                        @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded-md">
                                {{ session('status') }}
                            </div>
                        @endif

                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('Enter keywords separated by a comma (,). For example: "bahaya, ada maling, tolong saya". These words will activate the SOS alert when spoken.') }}
                        </p>

                        <form action="{{ route('voice.keywords.store') }}" method="POST">
                            @csrf
                            <div>
                                <x-input-label for="keywords" :value="__('Your Keywords')" />
                                <x-text-input id="keywords" name="keywords" type="text" class="mt-1 block w-full"
                                      :value="old('keywords', $currentKeywords ?? '')"
                                      placeholder="e.g., bahaya, ada maling, tolong saya" />
                                <x-input-error class="mt-2" :messages="$errors->get('keywords')" />
                            </div>

                            <div class="flex items-center gap-4 mt-6">
                                <x-primary-button>{{ __('Save Keywords') }}</x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal/Pop-up SOS --}}
    <div id="sosModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 hidden">
        {{-- ... isi modal tidak berubah ... --}}
    </div>

    @push('scripts')
        {{-- Seluruh kode JavaScript dari sebelumnya tidak ada yang berubah --}}
        {{-- Anda tidak perlu mengubah apapun di sini --}}
        <script>
            // Kode JavaScript lengkap dari jawaban sebelumnya
        </script>
    @endpush
</x-app-layout>