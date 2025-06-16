{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-500 leading-tight">
            {{ $pageTitle ?? __('SOS Alerts') }}
        </h2>
    </x-slot>

    {{-- Elemen Audio untuk SOS Alarm --}}
    <audio id="dashboardSosAlertSound" src="{{ asset('audio/sos_alarm.mp3') }}" preload="auto"></audio>

    {{-- Ini adalah bagian konten utama halaman --}}
    <div class="py-6 sm:py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="space-y-8">

                {{-- ================================================================ --}}
                {{-- BAGIAN 1: KONTEN SOS UTAMA                                     --}}
                {{-- ================================================================ --}}
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

                    <div class="mt-8">
                        <button id="voiceListenButton" class="px-5 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-md shadow-md transition ease-in-out duration-150">
                            Aktivasi SOS via Suara
                        </button>
                        <p id="voiceStatus" class="text-sm text-gray-500 mt-2">Klik untuk mulai mendengarkan kata kunci darurat.</p>
                        {{-- Area untuk menampilkan kata kunci aktif --}}
                        <div id="activeKeywordsArea" class="text-xs text-gray-500 mt-3 hidden">
                            <span class="font-semibold">Kata Kunci Aktif:</span>
                            <span id="keywordsList"></span>
                        </div>
                    </div>
                    
                    {{-- Div untuk menampilkan pesan SOS dan tombol Stop --}}
                    <div id="dashboardSosMessageArea" class="mt-6 text-center hidden">
                        <div class="p-4 bg-red-100 border border-red-300 text-red-700 rounded-md shadow-sm">
                            <div class="sos-message-content">
                                <p class="font-bold text-lg">{{ __('SOS ALERT ACTIVATED!') }}</p>
                                <p class="text-md mt-2">{{ __('IMPORTANT: Immediately call 112 or your nearest local authorities using your phone.') }}</p>
                                <div class="mt-3">
                                    <p class="font-semibold text-gray-800 text-base">{{ __("Immediately Call:") }}</p>
                                    <p class="text-3xl sm:text-4xl font-bold text-red-600 my-1">112</p>
                                    <p class="text-xs sm:text-sm text-gray-600">
                                        {{ __("National Emergency Number for Indonesia.") }}<br>
                                        {{ __("Or contact your nearest local authorities.") }}
                                    </p>
                                </div>
                                <p class="text-sm mt-2" id="dashboardEmailNotificationStatus">{{ __('If you have set up emergency contacts, we will attempt to notify them via email...') }}</p>
                            </div>
                            <button id="stopSosButton" class="mt-4 px-6 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-md shadow-md transition ease-in-out duration-150">
                                {{ __('Stop SOS') }}
                            </button>
                        </div>
                        <p id="soundErrorMessage" class="text-xs text-red-500 mt-1 hidden"></p>
                    </div>
                </div>

                {{-- ================================================================ --}}
                {{-- BAGIAN 2: PENGATURAN KATA KUNCI                                --}}
                {{-- ================================================================ --}}
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

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // =================================================================
        // BAGIAN 1: PERBAIKAN TOMBOL TERSANGKUT (STUCK BUTTON FIX)
        // Memaksa tombol untuk selalu aktif saat halaman baru dimuat untuk menghindari cache browser
        // =================================================================
        const initialSosButton = document.getElementById('sosButton');
        const initialVoiceButton = document.getElementById('voiceListenButton');
        if (initialSosButton) initialSosButton.disabled = false;
        if (initialVoiceButton) initialVoiceButton.disabled = false;


        // =================================================================
        // BAGIAN 2: DEFINISI VARIABEL DAN FUNGSI INTI
        // =================================================================
        const sosButton = document.getElementById('sosButton');
        const dashboardSosMessageArea = document.getElementById('dashboardSosMessageArea');
        const dashboardSosSoundElement = document.getElementById('dashboardSosAlertSound');
        const stopSosButton = document.getElementById('stopSosButton');
        const soundErrorMessageP = document.getElementById('soundErrorMessage');
        const voiceListenButton = document.getElementById('voiceListenButton');
        const voiceStatus = document.getElementById('voiceStatus');
        const activeKeywordsArea = document.getElementById('activeKeywordsArea');
        const keywordsList = document.getElementById('keywordsList');

        function playSound() {
            if (dashboardSosSoundElement) {
                dashboardSosSoundElement.currentTime = 0;
                const playPromise = dashboardSosSoundElement.play();
                if (playPromise !== undefined) {
                    playPromise.catch(error => {
                        console.error("Error memutar audio SOS:", error);
                        if(soundErrorMessageP) {
                            soundErrorMessageP.textContent = `Gagal memutar suara alarm: Browser mungkin memblokir pemutaran otomatis.`;
                            soundErrorMessageP.classList.remove('hidden');
                        }
                    });
                }
            }
        }

        function stopSound() {
            if (dashboardSosSoundElement) {
                dashboardSosSoundElement.pause();
                dashboardSosSoundElement.currentTime = 0;
            }
        }

        function triggerDashboardSOS() {
            if (window.recognition && isListening) {
                recognition.stop();
            }
            console.log("Dashboard SOS Terpicu!");
            playSound();

            if (dashboardSosMessageArea) {
                if(soundErrorMessageP) soundErrorMessageP.classList.add('hidden');
                dashboardSosMessageArea.classList.remove('hidden');
                dashboardSosMessageArea.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            if(sosButton) sosButton.disabled = true;
            if(voiceListenButton) voiceListenButton.disabled = true;
            
            // Logika geolocation & notifikasi tetap sama
        }

        function stopDashboardSOS() {
            console.log("Dashboard SOS Dihentikan!");
            stopSound();
            if (dashboardSosMessageArea) {
                dashboardSosMessageArea.classList.add('hidden');
            }
            if (sosButton) {
                sosButton.disabled = false;
            }
            if (voiceListenButton) {
                // Hanya aktifkan kembali jika browser mendukungnya
                if (window.SpeechRecognition) {
                    voiceListenButton.disabled = false;
                }
            }
            if(soundErrorMessageP) soundErrorMessageP.classList.add('hidden');
        }

        if (sosButton) { sosButton.addEventListener('click', triggerDashboardSOS); }
        if (stopSosButton) { stopSosButton.addEventListener('click', stopDashboardSOS); }


        // =================================================================
        // BAGIAN 3: LOGIKA WEB SPEECH API
        // =================================================================
        window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        let recognition;
        let isListening = false;

        if (!window.SpeechRecognition) {
            if(voiceListenButton) {
                voiceListenButton.disabled = true;
                voiceStatus.innerHTML = "<strong>Fitur suara tidak didukung di browser ini.</strong><br>Gunakan Google Chrome di PC/Laptop.";
            }
        } else {
            recognition = new SpeechRecognition();
            recognition.lang = 'id-ID';
            recognition.continuous = true;
            recognition.interimResults = false;

            const defaultEmergencyWords = ['tolong', 'bantu', 'darurat', 'sos'];
            const userKeywords = @json($userKeywords ?? []);
            const allEmergencyWords = [...new Set([...defaultEmergencyWords, ...userKeywords].filter(word => word))];
            
            if (allEmergencyWords.length > 0 && keywordsList) {
                keywordsList.textContent = allEmergencyWords.join(', ');
                if (activeKeywordsArea) activeKeywordsArea.classList.remove('hidden');
            }
            console.log('Daftar kata kunci yang aktif:', allEmergencyWords);

            recognition.onresult = (event) => {
                if (sosButton.disabled) return;
                const lastResultIndex = event.results.length - 1;
                const transcript = event.results[lastResultIndex][0].transcript.trim().toLowerCase();
                console.log('Terdengar:', transcript);
                voiceStatus.textContent = `Terdengar: "${transcript}"`;
                for (const word of allEmergencyWords) {
                    const regex = new RegExp(`\\b${word}\\b`);
                    if (regex.test(transcript)) {
                        voiceStatus.textContent = `Kata kunci "${word}" terdeteksi! Mengaktifkan SOS...`;
                        triggerDashboardSOS();
                        break;
                    }
                }
            };

            recognition.onerror = (event) => {
                console.error('Speech recognition error:', event.error);
                voiceStatus.textContent = `Terjadi error pada pengenalan suara: ${event.error}`;
            };

            recognition.onstart = () => {
                isListening = true;
                voiceListenButton.textContent = "Sedang Mendengar... (Klik untuk Berhenti)";
                voiceListenButton.classList.add('bg-red-600', 'hover:bg-red-700');
                voiceStatus.textContent = "Ucapkan salah satu kata kunci darurat Anda.";
            };

            recognition.onend = () => {
                isListening = false;
                // Hanya ubah teks jika SOS tidak aktif
                if (!sosButton.disabled) {
                    voiceListenButton.textContent = "Aktivasi SOS via Suara";
                    voiceListenButton.classList.remove('bg-red-600', 'hover:bg-red-700');
                    voiceStatus.textContent = "Klik untuk mulai mendengarkan kata kunci darurat.";
                }
            };

            if (voiceListenButton) {
                voiceListenButton.addEventListener('click', () => {
                    if (isListening) {
                        recognition.stop();
                    } else {
                        // Jangan mulai jika SOS sudah aktif
                        if (!sosButton.disabled) {
                            try {
                                recognition.start();
                            } catch (e) {
                                console.error("Gagal memulai recognition:", e);
                                voiceStatus.textContent = "Gagal memulai. Coba refresh halaman.";
                            }
                        }
                    }
                });
            }
        }
    });
    </script>
    @endpush
</x-app-layout>