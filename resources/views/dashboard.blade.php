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

                {{-- Bagian 1: Konten SOS Utama --}}
                <div class="p-4 sm:p-6 text-gray-900 text-center bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-6 sm:mb-8 text-gray-900 dark:text-gray-100">
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
                            <span class="text-gray-600 dark:text-gray-300 font-medium">Activate via Voice</span>
                            <button @click="isVoiceActive = !isVoiceActive; window.toggleVoiceRecognition(isVoiceActive)"
                                    type="button"
                                    class="relative inline-flex flex-shrink-0 h-7 w-12 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    :class="isVoiceActive ? 'bg-indigo-600' : 'bg-gray-400'"
                                    role="switch"
                                    :aria-checked="isVoiceActive.toString()">
                                <span aria-hidden="true"
                                      class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                      :class="{ 'translate-x-5': isVoiceActive, 'translate-x-0': !isVoiceActive }">
                                </span>
                            </button>
                        </div>
                        <p id="voiceStatus" class="text-sm text-gray-500 dark:text-gray-400 mt-2 min-h-[20px]"></p>
                        <div id="activeKeywordsArea" class="text-xs text-gray-500 dark:text-gray-400 mt-1 hidden">
                            <span class="font-semibold">Active Keywords:</span>
                            <span id="keywordsList"></span>
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Pengaturan Kata Kunci Kustom --}}
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-medium mb-4">{{ __('Set Your Emergency Keywords') }}</h3>
                        @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 dark:bg-green-900 dark:text-green-300 p-3 rounded-md">
                                {{ session('status') }}
                            </div>
                        @endif
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            {{ __('Enter keywords separated by a comma (,). For example: "bahaya, ada maling, tolong saya".') }}
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

    <div id="sosModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div id="dashboardSosMessageArea">
            <div class="p-6 md:p-8 bg-red-100 border-4 border-red-300 text-red-800 rounded-lg shadow-2xl max-w-lg w-full mx-4">
                <div class="sos-message-content text-center">
                    <p class="font-bold text-2xl md:text-3xl text-red-700">{{ __('SOS ALERT ACTIVATED!') }}</p>
                    <p class="text-md mt-4">{{ __('IMPORTANT: Immediately call 112 or your nearest local authorities using your phone.') }}</p>
                    <div class="mt-5">
                        <p class="font-semibold text-gray-800 text-lg">{{ __("Immediately Call:") }}</p>
                        <p class="text-5xl md:text-6xl font-bold text-red-600 my-1 tracking-wider">112</p>
                        <p class="text-xs sm:text-sm text-gray-700">
                            {{ __("National Emergency Number for Indonesia.") }}<br>
                            {{ __("Or contact your nearest local authorities.") }}
                        </p>
                    </div>
                    <p class="text-sm mt-5" id="dashboardEmailNotificationStatus">{{ __('If you have set up emergency contacts, we will attempt to notify them via email...') }}</p>
                </div>
                <div class="mt-6 text-center">
                    <button id="stopSosButton" class="px-8 py-3 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-md shadow-md transition ease-in-out duration-150 text-lg">
                        {{ __('Stop SOS') }}
                    </button>
                </div>
            </div>
            <p id="soundErrorMessage" class="text-xs text-red-200 mt-2 text-center hidden"></p>
        </div>
    </div>

    @push('scripts')
        {{-- Kode JavaScript di sini sudah benar dan tidak perlu diubah --}}
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const app = {
                sosButton: document.getElementById('sosButton'),
                stopSosButton: document.getElementById('stopSosButton'),
                sosModal: document.getElementById('sosModal'),
                sosSound: document.getElementById('dashboardSosAlertSound'),
                voiceStatus: document.getElementById('voiceStatus'),
                activeKeywordsArea: document.getElementById('activeKeywordsArea'),
                keywordsList: document.getElementById('keywordsList'),
                alpineToggle: document.querySelector('[x-data]'),
                recognition: null,
                isListening: false,
                isSupported: false,
                allEmergencyWords: [],
            };

            function init() {
                if (app.sosButton) app.sosButton.disabled = false;
                const defaultWords = ['tolong', 'bantu', 'darurat', 'sos'];
                const userKeywords = @json($userKeywords ?? []);
                app.allEmergencyWords = [...new Set([...defaultWords, ...userKeywords].filter(word => word))];
                if (app.allEmergencyWords.length > 0 && app.keywordsList) {
                    app.keywordsList.textContent = app.allEmergencyWords.join(', ');
                    if (app.activeKeywordsArea) app.activeKeywordsArea.classList.remove('hidden');
                }
                console.log('Daftar kata kunci yang aktif:', app.allEmergencyWords);
                const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                if (!SpeechRecognition) {
                    app.isSupported = false;
                    app.voiceStatus.innerHTML = "<strong>Fitur suara tidak didukung browser ini.</strong>";
                    if (app.alpineToggle) app.alpineToggle.querySelector('button[role="switch"]').disabled = true;
                } else {
                    app.isSupported = true;
                    app.recognition = new SpeechRecognition();
                    app.recognition.lang = 'id-ID';
                    app.recognition.continuous = true;
                    app.recognition.interimResults = false;
                    setupRecognitionHandlers();
                }
                if (app.sosButton) app.sosButton.addEventListener('click', triggerDashboardSOS);
                if (app.stopSosButton) app.stopSosButton.addEventListener('click', stopDashboardSOS);
                window.toggleVoiceRecognition = toggleVoiceRecognition;
            }

            function setupRecognitionHandlers() {
                app.recognition.onstart = () => { app.isListening = true; app.voiceStatus.textContent = "Mendengarkan..."; };
                app.recognition.onend = () => { app.isListening = false; if (!app.sosButton.disabled) { app.voiceStatus.textContent = ""; if (app.alpineToggle.__x.$data.isVoiceActive) { app.alpineToggle.__x.$data.isVoiceActive = false; } } };
                app.recognition.onresult = (event) => { if (app.sosButton.disabled) return; const transcript = event.results[event.results.length - 1][0].transcript.trim().toLowerCase(); console.log('Terdengar:', transcript); app.voiceStatus.textContent = `Terdengar: "${transcript}"`; for (const word of app.allEmergencyWords) { if (transcript.includes(word)) { app.voiceStatus.textContent = `Kata kunci "${word}" terdeteksi!`; triggerDashboardSOS(); break; } } };
                app.recognition.onerror = (event) => { console.error('Speech recognition error:', event.error); app.voiceStatus.textContent = `Error: ${event.error}`; if (app.alpineToggle.__x.$data.isVoiceActive) { app.alpineToggle.__x.$data.isVoiceActive = false; } };
            }

            function toggleVoiceRecognition(isActive) {
                if (!app.isSupported) return;
                if (isActive) {
                    try { app.recognition.start(); } catch (e) { console.error("Gagal memulai recognition:", e); app.voiceStatus.textContent = "Gagal memulai. Coba lagi."; app.alpineToggle.__x.$data.isVoiceActive = false; }
                } else {
                    app.recognition.stop();
                }
            }
            
            function triggerDashboardSOS() {
                if (app.isListening) { app.recognition.stop(); }
                console.log("Dashboard SOS Terpicu!");
                if (app.sosSound) app.sosSound.play().catch(e => console.error("Audio play error:", e));
                if (app.sosModal) app.sosModal.classList.remove('hidden');
                if (app.sosButton) app.sosButton.disabled = true;
                if (app.alpineToggle) app.alpineToggle.__x.$data.isVoiceActive = false;
            }

            function stopDashboardSOS() {
                console.log("Dashboard SOS Dihentikan!");
                if (app.sosSound) { app.sosSound.pause(); app.sosSound.currentTime = 0; }
                if (app.sosModal) app.sosModal.classList.add('hidden');
                if (app.sosButton) app.sosButton.disabled = false;
            }
            
            init();
        });
        </script>
    @endpush
</x-app-layout>