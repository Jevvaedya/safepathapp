<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-500 leading-tight">
            {{ __('SOS Alerts') }}
        </h2>
    </x-slot>

    {{-- Elemen Audio Terpadu untuk SOS Alarm --}}
    <audio id="sosAlarmSound" src="{{ asset('audio/sos_alarm.mp3') }}" preload="auto"></audio>

    <div class="py-6 sm:py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 text-center">
                    <h2 class="text-xl sm:text-3xl font-semibold mb-8 text-text-main">
                        {{ __("In an Emergency Situation?") }}
                    </h2>
                    <button id="sosButton"
                            class="w-36 h-36 md:w-40 md:h-40 mx-auto
                                   bg-gradient-to-br from-accent-orange via-red-500 to-primary
                                   text-white font-bold text-3xl md:text-4xl
                                   rounded-full shadow-xl flex items-center justify-center
                                   transition duration-150 ease-in-out transform hover:scale-110
                                   focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-primary-dark">
                        SOS
                    </button>
                    <p class="text-sm text-gray-500 mt-4">{{__('Press the button or use your voice command to activate the alert.')}}</p>
                    <div id="mainPageSosStatus" class="mt-6 text-center">
                        {{-- Status setelah modal ditutup akan muncul di sini --}}
                    </div>
                </div>
            </div>

            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 sm:p-8 text-gray-900">
                    <h3 class="text-xl sm:text-2xl font-semibold text-black mb-6">
                        {{ __('Voice Command Settings') }}
                    </h3>

                    {{-- Form untuk mengubah kata kunci --}}
                    <div class="mb-8">
                        <h4 class="text-md font-semibold text-gray-800 mb-2">{{ __('Custom SOS Keyword') }}</h4>
                        <form id="keywordForm" method="POST" action="{{ route('settings.updateKeyword') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="customKeywordInput" class="block text-sm font-medium text-gray-700">
                                    {{ __('Set your SOS keyword:') }}
                                </label>
                                <input type="text" id="customKeywordInput" name="keyword"
                                       value="{{ old('keyword', $currentKeyword ?? 'tolong') }}"
                                       class="mt-1 block w-full sm:w-2/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                       required minlength="3" maxlength="20" pattern="[a-zA-Z0-9\s]+"
                                       title="Hanya huruf, angka, dan spasi. Minimal 3 karakter.">
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('Current keyword: ') }}<strong id="currentKeywordDisplay">{{ $currentKeyword ?? 'tolong' }}</strong>.
                                </p>
                            </div>
                            <button type="submit" id="saveKeywordButton" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium shadow-sm">
                                {{ __('Save Keyword') }}
                            </button>
                        </form>
                        <p id="keywordSaveStatus" class="text-xs mt-2"></p>
                    </div>

                    <hr class="my-8">

                    <div>
                        <h4 class="text-md font-semibold text-gray-800 mb-2">{{ __('Activate Voice Trigger') }}</h4>
                        <p class="text-sm text-gray-600 mb-3">
                            {{ __('Activate voice commands to trigger SOS Alerts by saying your custom keyword.') }}
                        </p>
                        <button id="voiceCommandButton" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium shadow-md">
                            {{ __('Aktifkan Perintah Suara') }} <span id="voiceCommandButtonKeywordDisplay" class="ms-1">("{{ $currentKeyword ?? 'tolong' }}")</span>
                        </button>
                        <p id="voiceStatus" class="text-xs text-gray-500 mt-2"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="sosModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden p-4">
        <div id="sosModalContent" class="bg-white rounded-lg shadow-xl w-full max-w-lg">
            {{-- Konten modal akan diisi oleh JavaScript --}}
        </div>
    </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ===================================
    //  SETUP ELEMEN & VARIABEL
    // ===================================
    const sosButton = document.getElementById('sosButton'); // Tombol SOS besar
    const sosAlarmSound = document.getElementById('sosAlarmSound');
    const mainPageSosStatus = document.getElementById('mainPageSosStatus');
    const sosModal = document.getElementById('sosModal');
    const sosModalContent = document.getElementById('sosModalContent');
    
    // Elemen dari form settings
    const keywordForm = document.getElementById('keywordForm');
    const customKeywordInput = document.getElementById('customKeywordInput');
    const keywordSaveStatus = document.getElementById('keywordSaveStatus');
    const saveKeywordButton = document.getElementById('saveKeywordButton');
    const currentKeywordDisplay = document.getElementById('currentKeywordDisplay');
    const voiceCommandButtonKeywordDisplay = document.getElementById('voiceCommandButtonKeywordDisplay');
    const voiceCmdButton = document.getElementById('voiceCommandButton');
    const voiceStatusP = document.getElementById('voiceStatus');

    let currentSosKeyword = "{{ strtolower(trim( $currentKeyword ?? 'tolong' )) }}";
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    let recognition;
    let shouldBeListening = false;
    
    // ===================================
    //  FUNGSI UTAMA & KONSISTEN
    // ===================================

    function hideSosModal() {
        if (sosModal) sosModal.classList.add('hidden');
        if (sosAlarmSound) {
            sosAlarmSound.pause();
            sosAlarmSound.currentTime = 0;
        }
        if (mainPageSosStatus) {
            mainPageSosStatus.innerHTML = `<p class="text-sm text-gray-600">{{ __('SOS Deactivated.') }}</p>`;
            setTimeout(() => { mainPageSosStatus.innerHTML = ''; }, 4000);
        }
    }

    function showSosModal() {
        if (!sosModal || !sosModalContent) return;

        sosModalContent.innerHTML = `
            <div class="p-6 text-center">
                <h3 class="text-2xl font-bold text-red-600 mb-2">{{ __('SOS ALERT ACTIVATED!') }}</h3>
                <div class="border-2 border-red-400 rounded-lg p-4 bg-pink-50">
                    <p class="text-md text-red-500 mb-3">{{ __('IMPORTANT: Immediately call 112 or your nearest local authorities.') }}</p>
                    <p class="text-lg text-gray-700 mb-1">{{ __('Immediately Call:') }}</p>
                    <p class="text-6xl font-bold text-red-600 mb-1">112</p>
                    <div id="modalSosEmailStatusContainer" class="my-3 text-sm">
                        <p class="italic text-gray-600">{{ __('Attempting to send email with location...') }}</p>
                    </div>
                    <button id="stopSosButtonInModal" class="mt-2 px-6 py-3 bg-yellow-400 hover:bg-yellow-500 text-black font-semibold rounded-md shadow-md text-base">
                        {{ __('Stop SOS') }}
                    </button>
                </div>
            </div>`;
        
        sosModal.classList.remove('hidden');

        document.getElementById('stopSosButtonInModal').addEventListener('click', hideSosModal);
    }

    function triggerSOSAlerts() {
        console.log("SOS Alert Terpicu!");
        if (sosAlarmSound) {
            sosAlarmSound.play().catch(error => console.error("Gagal memutar suara SOS:", error));
        }
        
        if (mainPageSosStatus) mainPageSosStatus.innerHTML = ''; 
        showSosModal();

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const locationData = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };
                    sendSosNotificationEmailRequest(locationData);
                },
                (error) => {
                    console.warn("Tidak bisa mendapatkan lokasi: ", error.message);
                    sendSosNotificationEmailRequest(null); // Kirim tanpa lokasi jika gagal
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            console.warn("Geolocation tidak didukung.");
            sendSosNotificationEmailRequest(null); // Kirim tanpa lokasi jika tidak didukung
        }
    }

    function sendSosNotificationEmailRequest(locationData) {
        const modalEmailStatusDiv = document.getElementById('modalSosEmailStatusContainer');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch("{{ route('sos.notifyContact') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ location: locationData })
        })
        .then(response => response.ok ? response.json() : Promise.reject(response.json()))
        .then(data => {
            if (modalEmailStatusDiv) {
                const statusClass = data.success ? 'bg-green-100 border-green-300 text-green-700' : 'bg-red-100 border-red-300 text-red-700';
                const title = data.success ? '{{ __("Email Notification Sent!") }}' : '{{ __("Email Notification Failed") }}';
                modalEmailStatusDiv.innerHTML = `
                    <div class="p-3 ${statusClass} rounded-md shadow-sm text-sm">
                        <p class="font-semibold">${title}</p>
                        <p>${data.message || 'Unknown status.'}</p>
                    </div>`;
            }
        })
        .catch(errorPromise => {
            errorPromise.then(error => {
                 if (modalEmailStatusDiv) {
                    modalEmailStatusDiv.innerHTML = `
                        <div class="p-3 bg-red-100 border-red-300 text-red-700 rounded-md shadow-sm text-sm">
                            <p class="font-semibold">{{ __('Email Notification Error') }}</p>
                            <p>${error.message || 'An unknown error occurred.'}</p>
                        </div>`;
                }
            });
        });
    }

    // ===================================
    //  EVENT LISTENERS UNTUK FITUR
    // ===================================

    // 1. Tombol SOS Utama
    if (sosButton) {
        sosButton.addEventListener('click', triggerSOSAlerts);
    }
    
    // 2. Form Simpan Keyword
    if (keywordForm) {
        keywordForm.addEventListener('submit', function(event) {
            event.preventDefault();
            // Logika simpan keyword tetap sama seperti di file settings Anda
            // ... (Kode fetch untuk POST ke settings.updateKeyword)
        });
    }

    // 3. Tombol dan Logika Perintah Suara
    if (SpeechRecognition && voiceCmdButton) {
        recognition = new SpeechRecognition();
        recognition.lang = 'id-ID';
        recognition.continuous = true;
        recognition.interimResults = false;

        recognition.onresult = (event) => {
            let transcript = Array.from(event.results)
                .map(result => result[0])
                .map(result => result.transcript)
                .join('');
            
            const recognizedText = transcript.toLowerCase().trim();
            console.log("Dikenali:", recognizedText, "| Keyword:", currentSosKeyword);

            if (recognizedText.includes(currentSosKeyword)) {
                triggerSOSAlerts();
            }
        };
        
        recognition.onend = () => {
            if (shouldBeListening) {
                setTimeout(() => { if (shouldBeListening) recognition.start(); }, 500);
            }
        };

        recognition.onerror = (event) => {
            console.error("Error pengenalan suara:", event.error);
            voiceStatusP.textContent = `Error: ${event.error}. Pastikan izin mikrofon diberikan.`;
        };

        voiceCmdButton.addEventListener('click', () => {
            if (!shouldBeListening) {
                try {
                    recognition.start();
                    shouldBeListening = true;
                    voiceCmdButton.textContent = "{{ __('Nonaktifkan Perintah Suara') }}";
                    voiceStatusP.textContent = "{{ __('Status: Mendengarkan...') }}";
                } catch(e) {
                    console.error("Gagal memulai recognition:", e);
                    voiceStatusP.textContent = "{{ __('Gagal memulai. Cek izin mikrofon.') }}";
                }
            } else {
                shouldBeListening = false;
                recognition.stop();
                voiceCmdButton.textContent = `{{ __('Aktifkan Perintah Suara') }} ("${currentSosKeyword}")`;
                voiceStatusP.textContent = "{{ __('Perintah suara dinonaktifkan.') }}";
            }
        });
    } else if(voiceCmdButton) {
        voiceCmdButton.disabled = true;
        voiceStatusP.textContent = "{{ __('Browser tidak mendukung fitur ini.') }}";
    }
});
</script>
@endpush
</x-app-layout>