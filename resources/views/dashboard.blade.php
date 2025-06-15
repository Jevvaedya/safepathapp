<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-500 leading-tight"> {{-- Ukuran font header disesuaikan --}}
            @if (isset($pageTitle))
                {{ $pageTitle }} {{-- Ini akan menampilkan judul seperti "Emergency Contacts (Coming Soon)" --}}
            @else
                {{ __('SOS Alerts') }} {{-- Judul default jika kita di halaman dashboard utama --}}
            @endif
        </h2>
    </x-slot>

    {{-- Elemen Audio untuk SOS Alarm --}}
    {{-- PATH DIPERBARUI DI SINI --}}
    <audio id="dashboardSosAlertSound" src="{{ asset('audio/sos_alarm.mp3') }}" preload="auto"></audio>

    {{-- Ini adalah bagian konten utama halaman --}}
    <div class="py-6 sm:py-10"> {{-- Padding vertikal sedikit dikurangi untuk mobile --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"> {{-- Tambahkan px-4 untuk padding mobile di container utama --}}
     {{-- shadow-sm untuk efek bayangan yang lebih halus --}}
                {{-- Padding internal card disesuaikan untuk mobile dan layar lebih besar --}}
                <div class="p-4 sm:p-6 text-gray-900">
                    @if (isset($pageTitle))
                        {{-- Konten untuk halaman dengan pageTitle akan muncul di sini --}}
                        {{-- Pastikan konten ini juga dirancang responsif --}}
                    @else
                        {{-- Konten untuk Dashboard Utama (Tombol SOS) --}}
                        <div class="text-center">
                            <h2 class="text-xl sm:text-2xl md:text-3xl font-semibold mb-6 sm:mb-8 text-text-main"> {{-- Ukuran font dan margin bawah disesuaikan --}}
                                {{ __("In an Emergency Situation?") }}
                            </h2>
                            <button id="sosButton"
                                    class="w-32 h-32 sm:w-36 sm:h-36 md:w-40 md:h-40 mx-auto {{-- Ukuran tombol dibuat responsif --}}
                                        bg-gradient-to-br from-accent-orange via-red-500 to-primary
                                        text-white font-bold text-2xl sm:text-3xl md:text-4xl {{-- Ukuran font tombol disesuaikan --}}
                                        rounded-full shadow-xl
                                        flex items-center justify-center
                                        transition duration-150 ease-in-out transform hover:scale-110 hover:opacity-90
                                        focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-primary-dark focus:ring-opacity-50">
                                SOS
                            </button>

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
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sosButton = document.getElementById('sosButton');
        const dashboardSosMessageArea = document.getElementById('dashboardSosMessageArea');
        const dashboardSosSoundElement = document.getElementById('dashboardSosAlertSound');
        const stopSosButton = document.getElementById('stopSosButton'); // Tombol Stop SOS baru
        const soundErrorMessageP = document.getElementById('soundErrorMessage');

        function playSound() {
            if (dashboardSosSoundElement) {
                dashboardSosSoundElement.currentTime = 0; // Selalu mulai dari awal
                const playPromise = dashboardSosSoundElement.play();

                if (playPromise !== undefined) {
                    playPromise.then(_ => {
                        // Pemutaran audio berhasil dimulai
                        console.log("Audio SOS berhasil diputar.");
                        if(soundErrorMessageP) soundErrorMessageP.classList.add('hidden');
                    })
                    .catch(error => {
                        console.error("Error saat memutar audio SOS:", error);
                        if(soundErrorMessageP) {
                            soundErrorMessageP.textContent = `{{ __('Gagal memutar suara alarm: ') }}${error.name} - ${error.message}. Pastikan path file audio benar dan browser diizinkan memutar audio.`;
                            soundErrorMessageP.classList.remove('hidden');
                        }
                    });
                }
            } else {
                console.warn("Elemen audio 'dashboardSosAlertSound' tidak ditemukan.");
                 if(soundErrorMessageP) {
                    soundErrorMessageP.textContent = `{{ __('Elemen suara alarm tidak ditemukan.') }}`;
                    soundErrorMessageP.classList.remove('hidden');
                }
            }
        }

        function stopSound() {
            if (dashboardSosSoundElement) {
                dashboardSosSoundElement.pause();
                dashboardSosSoundElement.currentTime = 0; // Reset ke awal
                console.log("Audio SOS dihentikan.");
            }
        }

        function triggerDashboardSOS() {
            console.log("Dashboard SOS Terpicu!");
            playSound();

            if (dashboardSosMessageArea) {
                if(soundErrorMessageP) soundErrorMessageP.classList.add('hidden');
                dashboardSosMessageArea.classList.remove('hidden');
                dashboardSosMessageArea.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            if(sosButton) sosButton.disabled = true;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const locationData = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        };
                        updateDashboardEmailStatus("{{ __('Attempting to send email with location...') }}", "text-yellow-700");
                        console.log("Simulasi pengiriman email dari dashboard dengan lokasi:", locationData);
                        // Contoh: sendSosNotificationEmailRequest(locationData); // Anda perlu implementasi fungsi ini
                        // setTimeout(() => { updateDashboardEmailStatus("Notifikasi email berhasil dikirim.", "text-green-700"); }, 2000);
                    },
                    (error) => {
                        console.warn("Dashboard SOS: Tidak bisa mendapatkan lokasi untuk notifikasi: ", error.message);
                        updateDashboardEmailStatus("{{ __('Gagal mendapatkan lokasi. Notifikasi email dikirim tanpa lokasi...') }}", "text-orange-600");
                        // Contoh: sendSosNotificationEmailRequest(null);
                        // setTimeout(() => { updateDashboardEmailStatus("Notifikasi email berhasil dikirim (tanpa lokasi).", "text-green-700"); }, 2000);
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 }
                );
            } else {
                console.warn("Dashboard SOS: Geolocation tidak didukung, mengirim notifikasi tanpa lokasi.");
                updateDashboardEmailStatus("{{ __('Geolocation tidak didukung. Notifikasi email dikirim tanpa lokasi...') }}", "text-orange-600");
                // Contoh: sendSosNotificationEmailRequest(null);
                // setTimeout(() => { updateDashboardEmailStatus("Notifikasi email berhasil dikirim (tanpa lokasi).", "text-green-700"); }, 2000);
            }
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
            if(soundErrorMessageP) soundErrorMessageP.classList.add('hidden');
            updateDashboardEmailStatus("{{ __('If you have set up emergency contacts, we will attempt to notify them via email...') }}", "text-gray-500");
        }

        function updateDashboardEmailStatus(message, cssClass) {
            const emailStatusP = document.getElementById('dashboardEmailNotificationStatus');
            if (emailStatusP) {
                emailStatusP.textContent = message;
                emailStatusP.classList.remove('text-yellow-700', 'text-orange-600', 'text-green-700', 'text-gray-500');
                if(cssClass) emailStatusP.classList.add(...cssClass.split(' '));
            }
        }

        if (sosButton) {
            sosButton.addEventListener('click', triggerDashboardSOS);
        }

        if (stopSosButton) {
            stopSosButton.addEventListener('click', stopDashboardSOS);
        }
    });
    </script>
    @endpush
</x-app-layout>
