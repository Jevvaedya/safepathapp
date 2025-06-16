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
                        <p id="voiceStatus" class="text-sm text-gray-500 mt-2 min-h-[20px]">
                            {{-- Status akan diisi oleh JavaScript --}}
                        </p>
                        <div id="activeKeywordsArea" class="text-xs text-gray-500 mt-1 hidden">
                            <span class="font-semibold">Kata Kunci Aktif:</span>
                            <span id="keywordsList"></span>
                        </div>
                    </div>
                </div>
                {{-- ... sisa halaman tidak berubah ... --}}
            </div>
        </div>
    </div>

    {{-- Modal/Pop-up SOS --}}
    <div id="sosModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 hidden">
        {{-- ... isi modal tidak berubah ... --}}
    </div>


    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ... definisi variabel dan fungsi inti tidak berubah ...
        const sosButton = document.getElementById('sosButton');
        // `voiceListenButton` sudah tidak ada, jadi kita hapus
        
        // ===================================================================
        // [2] PERUBAHAN JAVASCRIPT: Menyesuaikan dengan logika Toggle
        // ===================================================================
        let recognition;
        let isListening = false;
        
        // Fungsi baru untuk dipanggil oleh Alpine.js dari toggle
        function toggleVoiceRecognition(isActive) {
            // Jangan lakukan apapun jika fitur tidak didukung
            if (!window.SpeechRecognition) return;

            if (isActive) {
                // Jika toggle di-ON-kan
                try {
                    recognition.start();
                } catch (e) {
                    console.error("Gagal memulai recognition:", e);
                    voiceStatus.textContent = "Gagal memulai. Coba refresh.";
                    // Matikan kembali toggle jika gagal start
                    document.querySelector('[x-data]').__x.$data.isVoiceActive = false;
                }
            } else {
                // Jika toggle di-OFF-kan
                recognition.stop();
            }
        }
        window.toggleVoiceRecognition = toggleVoiceRecognition; // Membuat fungsi ini global agar bisa diakses Alpine

        // Inisialisasi Speech Recognition
        window.SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!window.SpeechRecognition) {
            document.getElementById('voiceStatus').innerHTML = "<strong>Fitur suara tidak didukung di browser ini.</strong>";
        } else {
            // ... (logika speech recognition lainnya tetap sama)
            recognition = new SpeechRecognition();
            recognition.lang = 'id-ID';
            recognition.continuous = true;
            recognition.interimResults = false;

            recognition.onstart = () => {
                isListening = true;
                voiceStatus.textContent = "Mendengarkan...";
            };

            recognition.onend = () => {
                isListening = false;
                const alpineData = document.querySelector('[x-data]').__x.$data;
                // Hanya reset jika SOS tidak aktif
                if (!sosButton.disabled) {
                    voiceStatus.textContent = "";
                    // Matikan toggle jika proses berhenti sendiri (misal karena hening lama)
                    if (alpineData.isVoiceActive) {
                        alpineData.isVoiceActive = false;
                    }
                }
            };
            
            // ... (onresult, onerror, keyword processing tidak berubah) ...
            recognition.onresult = (event) => { if (sosButton.disabled) return; const lastResultIndex = event.results.length - 1; const transcript = event.results[lastResultIndex][0].transcript.trim().toLowerCase(); console.log('Terdengar:', transcript); voiceStatus.textContent = `Terdengar: "${transcript}"`; const allEmergencyWords = [...new Set([...['tolong', 'bantu', 'darurat', 'sos'], ...(@json($userKeywords ?? []))].filter(word => word))]; for (const word of allEmergencyWords) { if (transcript.includes(word)) { voiceStatus.textContent = `Kata kunci "${word}" terdeteksi! Mengaktifkan SOS...`; triggerDashboardSOS(); break; } } };
            recognition.onerror = (event) => { console.error('Speech recognition error:', event.error); voiceStatus.textContent = `Error: ${event.error}`; };
        }

        // ... Sisa kode (playSound, triggerDashboardSOS, dll.) tidak perlu diubah ...
        // Anda hanya perlu memastikan referensi ke `voiceListenButton` yang lama sudah dihapus dari fungsi-fungsi tersebut.
        // Saya sudah pastikan kode di bawah ini bersih dari referensi lama.
        const stopSosButton = document.getElementById('stopSosButton');
        const sosModal = document.getElementById('sosModal');
        const dashboardSosSoundElement = document.getElementById('dashboardSosAlertSound');
        const keywordsList = document.getElementById('keywordsList');
        const activeKeywordsArea = document.getElementById('activeKeywordsArea');
        function playSound() { if (dashboardSosSoundElement) { dashboardSosSoundElement.currentTime = 0; const playPromise = dashboardSosSoundElement.play(); if (playPromise !== undefined) { playPromise.catch(error => { console.error("Error memutar audio SOS:", error);}); } } }
        function stopSound() { if (dashboardSosSoundElement) { dashboardSosSoundElement.pause(); dashboardSosSoundElement.currentTime = 0; } }
        function triggerDashboardSOS() { if (window.recognition && isListening) { recognition.stop(); } console.log("Dashboard SOS Terpicu!"); playSound(); if (sosModal) { sosModal.classList.remove('hidden'); } if(sosButton) sosButton.disabled = true; document.querySelector('[x-data]').__x.$data.isVoiceActive = false; }
        function stopDashboardSOS() { console.log("Dashboard SOS Dihentikan!"); stopSound(); if (sosModal) { sosModal.classList.add('hidden'); } if (sosButton) { sosButton.disabled = false; } }
        if (sosButton) { sosButton.addEventListener('click', triggerDashboardSOS); }
        if (stopSosButton) { stopSosButton.addEventListener('click', stopDashboardSOS); }
        const userKeywords = @json($userKeywords ?? []);
        const allEmergencyWords = [...new Set([...'tolong', 'bantu', 'darurat', 'sos', ...userKeywords].filter(word => word))];
        if (allEmergencyWords.length > 0 && keywordsList) { keywordsList.textContent = allEmergencyWords.join(', '); if (activeKeywordsArea) activeKeywordsArea.classList.remove('hidden'); }
    });
    </script>
    @endpush
</x-app-layout>