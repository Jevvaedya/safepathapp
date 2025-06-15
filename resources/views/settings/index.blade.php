<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-500 leading-tight">
            {{ $pageTitle ?? __('Settings') }}
        </h2>
    </x-slot>

    {{-- Elemen Audio untuk SOS Alarm --}}
    <audio id="sosAlertSound" src="{{ asset('sounds/sos_alarm.mp3') }}" preload="auto"></audio>

    <div class="py-6 sm:py-10">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 sm:p-8 text-gray-900">
                    <h3 class="text-xl sm:text-2xl font-semibold text-black mb-6">
                        {{ __('Voice Command Settings') }}
                    </h3>

                    {{-- Form untuk mengubah kata kunci --}}
                    <div class="mb-8">
                        <h4 class="text-md font-semibold text-gray-800 mb-2">{{ __('Custom SOS Keyword') }}</h4>
                        <form id="keywordForm">
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
                                    {{ __('Current keyword: ') }}<strong id="currentKeywordDisplay">{{ $currentKeyword ?? 'tolong' }}</strong>. {{ __('Min 3, Max 20 characters. Alphanumeric and spaces only.') }}
                                </p>
                            </div>
                            <button type="submit" id="saveKeywordButton" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium shadow-sm">
                                {{ __('Save Keyword') }}
                            </button>
                        </form>
                        <p id="keywordSaveStatus" class="text-xs mt-2"></p>
                    </div>

                    <hr class="my-8">

                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-2">{{ __('Activate Voice Trigger') }}</h4>
                        <p class="text-sm text-gray-600 mb-3">
                            {{ __('Activate voice commands to trigger SOS Alerts by saying your custom keyword. Make sure your microphone is enabled and allowed by the browser.') }}
                        </p>
                        <button id="voiceCommandButton" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium shadow-md transition ease-in-out duration-150">
                            {{ __('Aktifkan Perintah Suara') }} <span id="voiceCommandButtonKeywordDisplay" class="ms-1">("{{ $currentKeyword ?? 'tolong' }}")</span>
                        </button>
                        <p id="voiceStatus" class="text-xs text-gray-500 mt-2"></p>
                    </div>

                    {{-- DIV untuk status SOS inline lama, bisa digunakan untuk pesan setelah modal ditutup --}}
                    <div id="settingsSosStatus" class="mt-6 text-center">
                        {{-- Pesan SOS akan muncul di sini --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal SOS --}}
    <div id="sosModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden p-4">
        <div id="sosModalContent" class="bg-white rounded-lg shadow-xl w-full max-w-lg">
            {{-- Konten modal akan diisi oleh JavaScript --}}
        </div>
    </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sosSoundElement = document.getElementById('sosAlertSound');
    const settingsSosStatusDiv = document.getElementById('settingsSosStatus'); // Untuk pesan setelah modal
    const sosModal = document.getElementById('sosModal');
    const sosModalContent = document.getElementById('sosModalContent');

    let currentSosKeyword = "{{ strtolower(trim( $currentKeyword ?? 'tolong' )) }}";
    const currentKeywordDisplaySpan = document.getElementById('currentKeywordDisplay');
    const voiceCommandButtonKeywordDisplay = document.getElementById('voiceCommandButtonKeywordDisplay');

    function hideSosModal() {
        if (sosModal) {
            sosModal.classList.add('hidden');
        }
        if (sosSoundElement) {
            sosSoundElement.pause();
            sosSoundElement.currentTime = 0;
        }
        // Bisa tambahkan pesan di settingsSosStatusDiv bahwa SOS dihentikan dari modal
        if (settingsSosStatusDiv) {
            settingsSosStatusDiv.innerHTML = `
                <div class="p-4 bg-gray-100 border border-gray-300 text-gray-700 rounded-md shadow-sm">
                    <p class="font-bold">{{ __('SOS Deactivated') }}</p>
                    <p class="text-sm">{{ __('The SOS alert was stopped.') }}</p>
                </div>`;
        }
    }

    function showSosModal() {
        if (!sosModal || !sosModalContent) return;

        // Konten untuk modal, mirip dengan yang sebelumnya di settingsSosStatusDiv
        sosModalContent.innerHTML = `
            <div class="p-4 sm:p-6 text-center">
                <h3 class="text-xl sm:text-2xl font-bold text-red-600 mb-2">{{ __('SOS ALERT ACTIVATED!') }}</h3>
                <div class="border-2 border-red-400 rounded-lg p-3 sm:p-4 bg-pink-50">
                    <p class="text-sm sm:text-md text-red-500 mb-3">
                        {{ __('IMPORTANT: Immediately call 112 or your nearest local authorities using your phone.') }}
                    </p>
                    <p class="text-md sm:text-lg text-gray-700 mb-1">{{ __('Immediately Call:') }}</p>
                    <p class="text-4xl sm:text-6xl font-bold text-red-600 mb-1">112</p>
                    <p class="text-xs sm:text-sm text-gray-500">{{ __('National Emergency Number for Indonesia.') }}</p>
                    <p class="text-xs sm:text-sm text-gray-500 mb-3">{{ __('Or contact your nearest local authorities.') }}</p>
                    
                    {{-- Tempat untuk status pengiriman email di dalam modal --}}
                    <div id="modalSosEmailStatusContainer" class="my-3 text-sm">
                        <p class="italic text-gray-600">{{ __('Attempting to send email with location...') }}</p>
                    </div>

                    <button id="stopSosButtonInModal" class="mt-2 px-5 py-2 sm:px-6 sm:py-3 bg-yellow-400 hover:bg-yellow-500 text-black font-semibold rounded-md shadow-md text-sm sm:text-base">
                        {{ __('Stop SOS') }}
                    </button>
                </div>
            </div>`;
        
        sosModal.classList.remove('hidden');

        const stopSosButtonInModal = document.getElementById('stopSosButtonInModal');
        if (stopSosButtonInModal) {
            stopSosButtonInModal.addEventListener('click', hideSosModal);
        }
    }


    function triggerSOSAlerts() {
        console.log("SOS Alerts Terpicu!");
        if (sosSoundElement) {
            sosSoundElement.currentTime = 0;
            sosSoundElement.play().catch(error => console.error("Gagal memutar suara SOS:", error));
        } else { console.warn("Elemen audio 'sosAlertSound' tidak ditemukan."); }

        // Kosongkan status inline lama jika ada
        if (settingsSosStatusDiv) settingsSosStatusDiv.innerHTML = ''; 
        
        showSosModal(); // Panggil fungsi untuk menampilkan modal

        // Geolocation dan pengiriman email
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
                    console.warn("Tidak bisa mendapatkan lokasi untuk notifikasi SOS: ", error.message);
                    sendSosNotificationEmailRequest(null);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
            );
        } else {
            console.warn("Geolocation tidak didukung, mengirim notifikasi email tanpa lokasi.");
            sendSosNotificationEmailRequest(null);
        }
    }

    function sendSosNotificationEmailRequest(locationData) {
        const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenElement) {
            console.error('CSRF token meta tag not found!');
            // Update status di dalam modal jika CSRF error
            const modalEmailStatusDiv = document.getElementById('modalSosEmailStatusContainer');
            if (modalEmailStatusDiv) modalEmailStatusDiv.innerHTML = `<p class="text-red-600 text-sm mt-2">{{ __('Error: Page configuration error for sending email.') }}</p>`;
            return;
        }
        const csrfToken = csrfTokenElement.getAttribute('content');
        
        // Target untuk pesan status email di dalam MODAL
        const modalEmailStatusDiv = document.getElementById('modalSosEmailStatusContainer');

        fetch("{{ route('sos.notifyContact') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ location: locationData })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (modalEmailStatusDiv) { // Selalu update di dalam modal
                if (data.success) {
                    console.log("Notifikasi email SOS:", data.message);
                    modalEmailStatusDiv.innerHTML = `
                        <div class="p-3 bg-green-100 border border-green-300 text-green-700 rounded-md shadow-sm text-sm">
                            <p class="font-semibold">{{ __('Email Notification Sent!') }}</p>
                            <p>${data.message}</p>
                        </div>`;
                } else {
                    console.error("Gagal mengirim notifikasi email SOS:", data.message);
                    modalEmailStatusDiv.innerHTML = `
                        <div class="p-3 bg-red-100 border border-red-300 text-red-700 rounded-md shadow-sm text-sm">
                            <p class="font-semibold">{{ __('Email Notification Failed') }}</p>
                            <p>${data.message || 'Unknown error occurred.'}</p>
                        </div>`;
                }
            }
        })
        .catch(error => {
            console.error('Error saat mengirim permintaan notifikasi email:', error);
            let errorMessage = "{{ __('Terjadi error saat mengirim notifikasi email.') }}";
            if (error && error.message) { errorMessage = error.message; }
            else if (typeof error === 'string') { errorMessage = error; }
            
            if (modalEmailStatusDiv) { // Selalu update di dalam modal
                modalEmailStatusDiv.innerHTML = `
                    <div class="p-3 bg-red-100 border border-red-300 text-red-700 rounded-md shadow-sm text-sm">
                        <p class="font-semibold">{{ __('Email Notification Error') }}</p>
                        <p>${errorMessage}</p>
                    </div>`;
            }
        });
    }


    // --- Logika untuk menyimpan kata kunci kustom (TIDAK BERUBAH BANYAK) ---
    const keywordForm = document.getElementById('keywordForm');
    const customKeywordInput = document.getElementById('customKeywordInput');
    const keywordSaveStatus = document.getElementById('keywordSaveStatus');
    const saveKeywordButton = document.getElementById('saveKeywordButton');

    if (keywordForm && customKeywordInput && keywordSaveStatus && saveKeywordButton) {
        keywordForm.addEventListener('submit', function(event) {
            event.preventDefault();
            // ... (logika penyimpanan keyword tetap sama) ...
            const newKeywordValue = customKeywordInput.value.trim();

            if (!newKeywordValue || newKeywordValue.length < 3 || newKeywordValue.length > 20 || !/^[a-zA-Z0-9\s]+$/.test(newKeywordValue)) {
                keywordSaveStatus.textContent = "{{ __('Kata kunci tidak valid. Min 3, Max 20 karakter, hanya huruf, angka, dan spasi.') }}";
                keywordSaveStatus.className = 'text-xs mt-2 text-red-600';
                return;
            }

            saveKeywordButton.disabled = true;
            keywordSaveStatus.textContent = "{{ __('Menyimpan...') }}";
            keywordSaveStatus.className = 'text-xs mt-2 text-blue-600';

            const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
            if (!csrfTokenElement) {
                console.error('CSRF token meta tag not found!');
                keywordSaveStatus.textContent = "{{ __('Error: Konfigurasi halaman tidak lengkap. Coba refresh.') }}";
                keywordSaveStatus.className = 'text-xs mt-2 text-red-600';
                saveKeywordButton.disabled = false;
                return;
            }
            const csrfToken = csrfTokenElement.getAttribute('content');

            fetch("{{ route('settings.updateKeyword') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ keyword: newKeywordValue })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    currentSosKeyword = data.newKeyword.toLowerCase().trim();
                    customKeywordInput.value = data.newKeyword;
                    if(currentKeywordDisplaySpan) currentKeywordDisplaySpan.textContent = data.newKeyword;
                    if(voiceCommandButtonKeywordDisplay) voiceCommandButtonKeywordDisplay.textContent = `("${data.newKeyword}")`;
                    if(voiceCmdButton) voiceCmdButton.textContent = "{{ __('Aktifkan Perintah Suara') }} " + `("${data.newKeyword}")`;

                    keywordSaveStatus.textContent = data.message;
                    keywordSaveStatus.className = 'text-xs mt-2 text-green-600';

                    if (shouldBeListening) {
                        recognition.stop(); 
                        shouldBeListening = false; 
                        alert("{{ __('Kata kunci diperbarui. Perintah suara dinonaktifkan, silakan aktifkan kembali jika diinginkan.') }}");
                        if(voiceCmdButton) voiceCmdButton.textContent = "{{ __('Aktifkan Perintah Suara') }} " + `("${currentSosKeyword}")`;
                        if(voiceStatusP) voiceStatusP.textContent = "{{ __('Perintah suara dinonaktifkan karena kata kunci berubah.') }}";
                    }

                } else {
                    let errorMessage = data.message || "{{ __('Gagal menyimpan kata kunci.') }}";
                    if (data.errors && data.errors.keyword) {
                        errorMessage = data.errors.keyword[0];
                    }
                    keywordSaveStatus.textContent = errorMessage;
                    keywordSaveStatus.className = 'text-xs mt-2 text-red-600';
                }
            })
            .catch(error => {
                console.error('Error saving keyword:', error);
                let errorMessage = "{{ __('Terjadi error saat menyimpan. Coba lagi.') }}";
                 if (error && error.message) {
                    errorMessage = error.message;
                    if (error.errors && error.errors.keyword) {
                         errorMessage = error.errors.keyword[0];
                    }
                }
                keywordSaveStatus.textContent = errorMessage;
                keywordSaveStatus.className = 'text-xs mt-2 text-red-600';
            })
            .finally(() => {
                saveKeywordButton.disabled = false;
            });
        });
    }

    // --- Logika Perintah Suara dengan kata kunci dinamis (TIDAK BERUBAH BANYAK) ---
    const voiceCmdButton = document.getElementById('voiceCommandButton');
    const voiceStatusP = document.getElementById('voiceStatus');

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    let recognition;
    let shouldBeListening = false;

    if (SpeechRecognition && voiceCmdButton) {
        recognition = new SpeechRecognition();
        recognition.lang = 'id-ID'; 
        recognition.continuous = true; 
        recognition.interimResults = false;

        recognition.onstart = () => {
            console.log("Pengenalan suara (Settings) dimulai.");
            if (voiceStatusP && shouldBeListening) voiceStatusP.textContent = "{{ __('Status: Mendengarkan...') }}";
             // Saat listening dimulai, pastikan modal SOS (jika ada) disembunyikan dan status inline dibersihkan.
            hideSosModal(); // Sembunyikan modal jika masih terbuka dari sesi sebelumnya
            if(settingsSosStatusDiv) settingsSosStatusDiv.innerHTML = ''; // Bersihkan status inline juga
        };

        recognition.onresult = (event) => {
            // ... (logika onresult tetap sama) ...
            let transcript = '';
            for (let i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) {
                    transcript += event.results[i][0].transcript;
                }
            }
            const recognizedText = transcript.toLowerCase().trim();
            console.log("Teks dikenali (Settings): " + recognizedText + " | Kata Kunci Aktif: " + currentSosKeyword);

            if (currentSosKeyword && recognizedText.includes(currentSosKeyword)) {
                console.log("Kata kunci '" + currentSosKeyword + "' terdeteksi (Settings)!");
                if (voiceStatusP) voiceStatusP.textContent = `{{ __('Kata kunci "${currentSosKeyword}" terdeteksi! Mengaktifkan SOS...') }}`;
                triggerSOSAlerts(); // Ini akan menampilkan modal
            }
        };

        recognition.onerror = (event) => {
            // ... (logika onerror tetap sama) ...
            console.error("Error pengenalan suara (Settings): ", event.error);
            let userMessage = "{{ __('Error pengenalan suara: ') }}" + event.error;
            if (event.error === 'not-allowed' || event.error === 'service-not-allowed') {
                userMessage = "{{ __('Akses mikrofon ditolak atau tidak diizinkan oleh browser/sistem. Pastikan izin telah diberikan.') }}";
                alert(userMessage); 
                shouldBeListening = false; 
                voiceCmdButton.textContent = "{{ __('Aktifkan Perintah Suara') }} " + `("${currentSosKeyword}")`;
            } else if (event.error === 'no-speech') {
                userMessage = "{{ __('Tidak ada suara terdeteksi. Coba lagi.') }}";
            } else if (event.error === 'network') {
                userMessage = "{{ __('Masalah jaringan saat pengenalan suara.') }}";
            } else if (event.error === 'audio-capture') {
                userMessage = "{{ __('Gagal menangkap audio. Pastikan mikrofon berfungsi.') }}";
            }
            if (voiceStatusP) voiceStatusP.textContent = userMessage;
        };

        recognition.onend = () => {
            // ... (logika onend tetap sama) ...
            console.log("Pengenalan suara (Settings) berakhir.");
            if (shouldBeListening) { 
                console.log("Sesi listening (Settings) berakhir, mencoba memulai ulang...");
                setTimeout(() => {
                    if (shouldBeListening) { 
                        try { 
                            recognition.start(); 
                        } catch(e) {
                            console.error("Gagal me-restart recognition (Settings) setelah onend:", e);
                            if (voiceStatusP) voiceStatusP.textContent = "{{ __('Sesi listening terhenti. Coba aktifkan manual.') }}";
                            shouldBeListening = false; 
                            voiceCmdButton.textContent = "{{ __('Aktifkan Perintah Suara') }} " + `("${currentSosKeyword}")`;
                        }
                    }
                }, 500); 
            } else { 
                if (voiceStatusP) voiceStatusP.textContent = "{{ __('Perintah suara dinonaktifkan.') }}";
                voiceCmdButton.textContent = "{{ __('Aktifkan Perintah Suara') }} " + `("${currentSosKeyword}")`;
            }
        };

        voiceCmdButton.addEventListener('click', () => {
            // ... (logika click voiceCmdButton tetap sama, pastikan memanggil hideSosModal jika perlu) ...
            if (!shouldBeListening) {
                try {
                    hideSosModal(); // Pastikan modal tersembunyi saat mengaktifkan voice command
                    if (settingsSosStatusDiv) settingsSosStatusDiv.innerHTML = ''; // Bersihkan juga status inline
                    
                    recognition.start();
                    shouldBeListening = true;
                    voiceCmdButton.textContent = "{{ __('Nonaktifkan Perintah Suara') }}";
                    if (voiceStatusP) voiceStatusP.textContent = "{{ __('Meminta izin mikrofon & memulai listening...') }}";
                } catch (e) {
                    console.error("Gagal memulai recognition (Settings) dari klik:", e);
                    if (voiceStatusP) voiceStatusP.textContent = "{{ __('Gagal memulai. Cek izin mikrofon & konsol.') }}";
                     shouldBeListening = false; 
                }
            } else {
                shouldBeListening = false; 
                recognition.stop();
            }
        });

    } else if (voiceCmdButton) { 
        voiceCmdButton.disabled = true;
        voiceCmdButton.textContent = "{{ __('Perintah Suara Tidak Didukung') }}";
        if (voiceStatusP) voiceStatusP.textContent = "{{ __('Browser Anda tidak mendukung fitur pengenalan suara.') }}";
        console.warn("Web Speech API tidak didukung oleh browser ini (Settings).");
    }
});
</script>
@endpush
</x-app-layout>