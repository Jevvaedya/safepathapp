<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg sm:text-xl text-gray-500 leading-tight"> {{-- Ukuran font header disesuaikan --}}
            {{ __('Safe Walk') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-7"> {{-- Padding vertikal disesuaikan --}}
        {{-- Kontainer utama dengan max-width dan padding horizontal untuk mobile --}}
        <div class="max-w-2xl mx-auto px-4 sm:px-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                {{-- Wrapper konten di dalam card dengan padding yang konsisten --}}
                <div class="p-4 sm:p-6 text-gray-900">
                    <div class="text-center">
                        <h3 class="text-xl sm:text-2xl font-semibold text-black mb-3 sm:mb-4"> {{-- Ukuran font dan margin disesuaikan --}}
                            {{ __('Activate Safe Walk Mode') }}
                        </h3>

                        <p class="text-sm text-gray-500 mb-6 sm:mb-7">
                            {{ __('Let your emergency contacts know you are on your way and track your journey for a set duration.') }}
                        </p>

                        <div class="mb-6 sm:mb-8 max-w-xs sm:max-w-sm mx-auto"> {{-- max-w disesuaikan, margin bawah disesuaikan --}}
                            <label for="safeWalkDuration" class="block text-sm font-medium text-black mb-1 text-left">
                                {{ __('Set Duration (in minutes):') }}
                            </label>
                            <input type="number" name="duration" id="safeWalkDuration" min="5" step="5" value="30"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-center text-base sm:text-lg"> {{-- Ukuran font input disesuaikan --}}
                            <p class="mt-1 text-xs text-gray-500 text-left">
                                {{ __('E.g., 30 for 30 minutes. Minimum 5 minutes, in steps of 5.') }}
                            </p>
                        </div>

                        {{-- Tombol Start SafeWalk --}}
                        <div class="text-center">
                            <button id="startSafeWalkButton" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 text-base sm:px-8 sm:py-3 sm:text-lg bg-black hover:bg-gray-700 active:bg-gray-900 text-white font-bold rounded-lg shadow-md transition duration-150 ease-in-out"> {{-- Ukuran tombol, padding, dan font disesuaikan, hover/active color diganti --}}
                                {{ __('Start Safe Walk') }}
                            </button>
                        </div>

                        {{-- Status dan timer --}}
                        <div id="safeWalkStatusArea" class="mt-6 sm:mt-8 pb-4 sm:pb-6"> {{-- Margin atas dan bawah disesuaikan --}}
                            {{-- Konten status akan dimuat di sini oleh JavaScript --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    // Ambil elemen-elemen dari halaman
    const startButton = document.getElementById('startSafeWalkButton');
    const durationInput = document.getElementById('safeWalkDuration');
    const statusArea = document.getElementById('safeWalkStatusArea');

    let safeWalkTimerInterval = null;
    let activeSafeWalkSessionId = null;
    let leafletMap = null; // Variabel leafletMap sudah ada
    let originalStartButtonText = '{{ __("Start Safe Walk") }}';

    // Variabel BARU untuk Path Tracking
    let pathWatchId = null; // ID untuk watchPosition path tracking
    let pathCoordinates = []; // Array untuk menyimpan koordinat jejak
    let pathPolyline = null; // Objek L.Polyline untuk jejak biru

    if (startButton) {
        originalStartButtonText = startButton.innerText;
    }

    function resetSafeWalkState() {
        console.log('Resetting Safe Walk state...');
        if (safeWalkTimerInterval) { clearInterval(safeWalkTimerInterval); safeWalkTimerInterval = null; }

        // Hentikan dan bersihkan path tracking
        stopPathTracking(); // Fungsi baru untuk menghentikan watchPosition jejak
        if (pathPolyline && leafletMap) { // Pastikan leafletMap ada sebelum removeLayer
            try { leafletMap.removeLayer(pathPolyline); } catch(e) { console.warn("Error removing path polyline:", e); }
        }
        pathPolyline = null;
        pathCoordinates = [];

        if (leafletMap) { try { leafletMap.remove(); } catch(e) { console.warn("Error removing map:", e); } leafletMap = null; }
        activeSafeWalkSessionId = null;

        if (statusArea) {
            statusArea.innerHTML = '';
        }

        if(startButton) {
            startButton.disabled = false;
            startButton.innerText = originalStartButtonText;
            startButton.classList.remove('bg-red-500', 'hover:bg-red-700');
            startButton.classList.add('bg-black', 'hover:bg-gray-700');
        }
        if(durationInput) {
            durationInput.disabled = false;
        }
    }

    if (startButton) {
        startButton.addEventListener('click', function() {
            if (startButton.innerText === '{{ __("Stop Safe Walk") }}') {
                stopSafeWalk(); // Fungsi stopSafeWalk Anda yang sudah ada
                return;
            }
            if (leafletMap) { try { leafletMap.remove(); } catch(e) { console.warn("Error removing map on new start:", e); } leafletMap = null; }
            resetSafeWalkState();

            if (statusArea) statusArea.innerHTML = '<p class="text-blue-600 p-4 text-center animate-pulse">{{ __("Attempting to get your location...") }}</p>';
            startButton.disabled = true;
            if(durationInput) durationInput.disabled = true;

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPositionAndStartSafeWalk, showError, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
            } else {
                if (statusArea) statusArea.innerHTML = '<p class="text-red-600 p-4 text-center">{{ __("Geolocation is not supported by this browser.") }}</p>';
                resetSafeWalkState();
            }
        });
    }

    function showPositionAndStartSafeWalk(position) {
        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;
        const currentDuration = parseInt(durationInput.value);

        if (statusArea) statusArea.innerHTML = '<p class="text-blue-600 p-4 text-center animate-pulse">{{ __("Activating Safe Walk...") }}</p>';

        const safeWalkData = { latitude, longitude, duration: currentDuration };
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenMeta) {
            console.error('CSRF token meta tag not found!');
            if (statusArea) statusArea.innerHTML = `<p class="text-red-600 p-4 text-center">{{ __('Error: CSRF token not found. Please refresh.') }}</p>`;
            resetSafeWalkState();
            return;
        }
        const csrfToken = csrfTokenMeta.getAttribute('content');

        fetch("{{ route('safewalk.start') }}", { /* ...konfigurasi fetch Anda... */
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(safeWalkData)
        })
        .then(response => { /* ... penanganan response Anda ... */
            if (!response.ok) {
                return response.json().then(errData => {
                    let serverMessage = errData.message;
                    if (errData.errors && errData.errors.duration) { serverMessage = errData.errors.duration[0]; }
                    throw new Error(serverMessage || '{{ __("Server error occurred.") }}');
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Server response (Safe Walk started):', data);
            if (data.session_id) activeSafeWalkSessionId = data.session_id;

            if (statusArea) { /* ... pembuatan statusContent Anda ... */
                const statusContent = document.createElement('div');
                statusContent.className = 'p-3 sm:p-4 rounded-md bg-green-50 border border-green-200 text-center';
                statusContent.innerHTML = `
                    <p class="text-green-700 font-semibold text-base sm:text-lg">{{ __('Safe Walk Activated!') }}</p>
                    <div id="safeWalkTimerDisplay" class="text-xl sm:text-2xl font-bold my-1 sm:my-2 text-green-700"></div>
                    <p class="text-xs sm:text-sm text-gray-700">{{ __('Tracking for:') }} ${currentDuration} {{ __('minutes') }}</p>
                    <p class="text-xs sm:text-sm text-gray-700">{{ __('Initial Location:') }} Lat: ${latitude.toFixed(4)}, Lon: ${longitude.toFixed(4)}</p>
                    <div id="leafletMapContainer" style="height: 200px; sm:height: 250px; width: 100%; margin-top: 0.75rem; sm:margin-top: 1rem; border-radius: 0.375rem; sm:border-radius: 0.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);"></div>
                    <p class="mt-2 sm:mt-3">
                        <a href="https://maps.google.com/?q=${latitude},${longitude}" target="_blank" class="text-blue-600 hover:text-blue-800 underline text-xs sm:text-sm">
                            {{ __('View on Google Maps') }}
                        </a>
                    </p>`;
                statusArea.innerHTML = ''; statusArea.appendChild(statusContent);
            }

            const timerDisplayElement = document.getElementById('safeWalkTimerDisplay');
            if (timerDisplayElement) startTimer(currentDuration, timerDisplayElement); // Fungsi startTimer Anda yang sudah ada

            if (document.getElementById('leafletMapContainer')) {
                if (leafletMap) { try { leafletMap.remove(); } catch(e) { console.warn("Error removing existing map:", e); } leafletMap = null; }
                try {
                    if (typeof L === 'undefined') { throw new Error('Leaflet library (L) not loaded.'); }
                    leafletMap = L.map('leafletMapContainer').setView([latitude, longitude], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { /* ... opsi tileLayer Anda ... */
                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                        maxZoom: 18, tileSize: 512, zoomOffset: -1
                    }).addTo(leafletMap);
                    L.marker([latitude, longitude]).addTo(leafletMap)
                        .bindPopup('<b>{{ __("Your Starting Point!") }}</b><br>{{ __("Safe Walk Activated.") }}')
                        .openPopup();
                    console.log('Leaflet map initialized.');

                    // === MULAI PATH TRACKING DI SINI ===
                    startPathTracking(); // Panggil fungsi baru untuk mulai menggambar jejak

                } catch(e) { /* ... penanganan error map Anda ... */
                    console.error('Error initializing Leaflet map:', e);
                    const mapContainer = document.getElementById('leafletMapContainer');
                    if(mapContainer) { mapContainer.innerHTML = `<p class="text-red-500 text-center p-2 text-sm">{{ __("Could not load map: ") }}${e.message}</p>`;}
                }
            } else { console.error('Error: Map container #leafletMapContainer not found for Leaflet.'); }

            if(startButton) { /* ... update tombol startButton Anda ... */
                startButton.innerText = '{{ __("Stop Safe Walk") }}';
                startButton.disabled = false;
                startButton.classList.remove('bg-black', 'hover:bg-gray-700');
                startButton.classList.add('bg-red-500', 'hover:bg-red-700');
            }
        })
        .catch(error => { /* ... penanganan error fetch Anda ... */
            console.error('Error starting Safe Walk:', error);
            if(statusArea) statusArea.innerHTML = `<p class="text-red-600 p-4 text-center">{{ __('Failed to activate Safe Walk: ') }}${error.message}</p>`;
            resetSafeWalkState();
        });
    }

    // === FUNGSI-FUNGSI BARU UNTUK PATH TRACKING ===
    function startPathTracking() {
        if (!navigator.geolocation) {
            console.warn("Geolocation is not supported for path tracking.");
            return;
        }
        if (!leafletMap) { // Pastikan peta sudah ada
            console.warn("Map not initialized, cannot start path tracking.");
            return;
        }

        // Reset jejak sebelumnya
        pathCoordinates = [];
        if (pathPolyline) {
            try { leafletMap.removeLayer(pathPolyline); } catch(e) { console.warn("Error removing old path polyline:", e); }
            pathPolyline = null;
        }

        const watchOptions = { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 };
        console.log('Starting path tracking (watchPosition)...');
        pathWatchId = navigator.geolocation.watchPosition(updatePathOnMap, handlePathLocationError, watchOptions);
    }

    function updatePathOnMap(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        const newPoint = L.latLng(lat, lng);

        console.log('Path tracking - New position:', lat, lng);
        pathCoordinates.push(newPoint);

        if (!leafletMap) { // Double check jika map hilang
            console.error("Map is not available to draw path.");
            return;
        }

        if (pathCoordinates.length >= 1) { // Cukup 1 titik untuk mulai (atau >1 untuk garis)
            if (!pathPolyline) {
                pathPolyline = L.polyline(pathCoordinates, {
                    color: 'blue',
                    weight: 5,
                    opacity: 0.75
                }).addTo(leafletMap);
            } else {
                pathPolyline.setLatLngs(pathCoordinates);
            }
        }
        // Pindahkan view map ke posisi baru, jika diinginkan agar selalu tengah
        // leafletMap.panTo(newPoint); // Atau setView jika ingin mengubah zoom juga
    }

    function stopPathTracking() {
        if (pathWatchId !== null) {
            navigator.geolocation.clearWatch(pathWatchId);
            pathWatchId = null;
            console.log('Path tracking stopped.');
        }
        // Jejak biru (pathPolyline) akan dihapus dari peta saat resetSafeWalkState
    }

    function handlePathLocationError(error) {
        console.error("Path Tracking Geolocation Error Code " + error.code + ": " + error.message);
        // Anda bisa menambahkan notifikasi ke pengguna jika perlu
        // stopPathTracking(); // Opsional: hentikan jika ada error berkelanjutan
    }
    // === AKHIR FUNGSI-FUNGSI BARU UNTUK PATH TRACKING ===

    function startTimer(durationInMinutes, timerDisplayEl) { /* ... fungsi startTimer Anda ... */
        if (!timerDisplayEl) { console.error('Timer display element was not provided!'); return; }
        let secondsRemaining = durationInMinutes * 60;
        if (safeWalkTimerInterval) clearInterval(safeWalkTimerInterval);

        safeWalkTimerInterval = setInterval(() => {
            if (secondsRemaining < 0) {
                clearInterval(safeWalkTimerInterval); safeWalkTimerInterval = null;
                if (timerDisplayEl) { /* ... update timerDisplayEl ... */
                    timerDisplayEl.innerHTML = "{{ __('Time is up!') }}";
                    timerDisplayEl.classList.remove('text-green-700');
                    timerDisplayEl.classList.add('text-orange-600');
                }
                const currentStatusArea = document.getElementById('safeWalkStatusArea');
                if (currentStatusArea) { /* ... update currentStatusArea ... */
                    const statusWrapper = currentStatusArea.querySelector('.p-3.sm\\:p-4.rounded-md.bg-green-50');
                    const timeUpMessage = document.createElement('p');
                    timeUpMessage.className = 'text-orange-600 font-semibold mt-2 text-center text-sm sm:text-base';
                    timeUpMessage.innerText = '{{ __("Safe Walk duration ended.") }}';
                    if (statusWrapper) { statusWrapper.appendChild(timeUpMessage); }
                    else { currentStatusArea.innerHTML = ''; currentStatusArea.appendChild(timeUpMessage); }
                }

                // HENTIKAN PATH TRACKING KETIKA TIMER HABIS
                stopPathTracking();

                if (activeSafeWalkSessionId) { /* ... fetch ke safewalk.expire ... */
                    console.log('Timer ended. Attempting to notify server for session ID:', activeSafeWalkSessionId);
                    const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
                    if (!csrfTokenMeta) { /* ... penanganan error CSRF ... */
                        console.error('CSRF token meta tag not found for session expire!');
                        setTimeout(() => { resetSafeWalkState(); }, 2000); return;
                    }
                    const csrfToken = csrfTokenMeta.getAttribute('content');
                    fetch("{{ route('safewalk.expire') }}", { /* ...konfigurasi fetch Anda... */
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify({ session_id: activeSafeWalkSessionId })
                    })
                    .then(response => { /* ... penanganan response Anda ... */
                        if (!response.ok) return response.json().then(errData => { throw new Error(errData.message || '{{ __("Server error on session expire.") }}'); });
                        return response.json();
                    })
                    .then(data => console.log('Server notified of session expiry:', data) )
                    .catch(error => { /* ... penanganan error fetch ... */
                        console.error('Error notifying server of session expiry:', error);
                        if (statusArea) {
                            const statusDiv = statusArea.querySelector('.p-3.sm\\:p-4.rounded-md.bg-green-50') || statusArea;
                            if(statusDiv) { statusDiv.innerHTML += `<p class="text-xs text-red-500 mt-1 text-center">{{ __('Failed to notify server about session end: ') }}${error.message}</p>`; }
                        }
                    })
                    .finally(() => { setTimeout(() => { resetSafeWalkState(); }, 3000); });
                } else {
                    console.warn('Timer ended, but no active session ID. Resetting UI directly.');
                    setTimeout(() => { resetSafeWalkState(); }, 2000);
                }
            } else { /* ... update timerDisplayEl ... */
                const minutes = Math.floor(secondsRemaining / 60);
                const seconds = secondsRemaining % 60;
                timerDisplayEl.innerHTML = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                secondsRemaining--;
            }
        }, 1000);
    }

    function stopSafeWalk() { /* ... fungsi stopSafeWalk Anda ... */
        if (!activeSafeWalkSessionId) { /* ... penanganan jika tidak ada sesi aktif ... */
            console.warn('No active Safe Walk session ID to stop. Resetting UI.');
            resetSafeWalkState(); return;
        }
        if(startButton) startButton.disabled = true;
        if (statusArea) statusArea.innerHTML = '<p class="text-blue-600 p-4 text-center animate-pulse">{{ __("Stopping Safe Walk...") }}</p>';

        // HENTIKAN PATH TRACKING SAAT STOP MANUAL
        stopPathTracking();

        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfTokenMeta) { /* ... penanganan error CSRF ... */
            console.error('CSRF token meta tag not found for stopping session!');
            if (statusArea) statusArea.innerHTML = `<p class="text-red-600 p-4 text-center">{{ __('Error: CSRF token not found. Please refresh.') }}</p>`;
            if(startButton) { startButton.innerText = '{{ __("Stop Safe Walk") }}'; startButton.disabled = false; } return;
        }
        const csrfToken = csrfTokenMeta.getAttribute('content');
        fetch("{{ route('safewalk.stop') }}", { /* ...konfigurasi fetch Anda... */
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ session_id: activeSafeWalkSessionId })
        })
        .then(response => { /* ... penanganan response Anda ... */
            if (!response.ok) return response.json().then(errData => { throw new Error(errData.message || '{{ __("Server error occurred while stopping.") }}'); });
            return response.json();
        })
        .then(data => { /* ... penanganan data sukses ... */
            console.log('Safe Walk stopped via server:', data);
            if (statusArea) statusArea.innerHTML = `<p class="text-green-600 p-4 text-center font-semibold">${data.message || '{{ __("Safe Walk successfully stopped.") }}'}</p>`;
            setTimeout(() => { resetSafeWalkState(); }, 2000);
        })
        .catch(error => { /* ... penanganan error fetch ... */
            console.error('Error stopping Safe Walk:', error);
            if (statusArea) statusArea.innerHTML = `<p class="text-red-600 p-4 text-center">{{ __('Failed to stop Safe Walk: ') }}${error.message}</p>`;
            if(startButton) {
                startButton.innerText = '{{ __("Stop Safe Walk") }}'; startButton.disabled = false;
                startButton.classList.remove('bg-black', 'hover:bg-gray-700');
                startButton.classList.add('bg-red-500', 'hover:bg-red-700');
            }
        });
    }

    function showError(error) { /* ... fungsi showError Anda ... */
        let errorMessage = '{{ __("An unknown error occurred while retrieving location.") }}';
        switch(error.code) {
            case error.PERMISSION_DENIED: errorMessage = "{{ __('User denied the request for Geolocation.') }}"; break;
            case error.POSITION_UNAVAILABLE: errorMessage = "{{ __('Location information is unavailable.') }}"; break;
            case error.TIMEOUT: errorMessage = "{{ __('The request to get user location timed out.') }}"; break;
        }
        if (statusArea) statusArea.innerHTML = `<p class="text-red-600 p-4 text-center">${errorMessage}</p>`;
        console.error("Geolocation Error Code " + error.code + ": " + errorMessage, error);
        resetSafeWalkState();
    }

    if (!document.querySelector('meta[name="csrf-token"]')) { /* ... penanganan jika CSRF token tidak ada saat load ... */
        console.error('CRITICAL: CSRF token meta tag not found on page load!');
        if (statusArea && !statusArea.innerHTML.includes('text-red-600')) {
            statusArea.innerHTML = `<p class="text-red-600 p-4 text-center">{{ __('Page configuration error: CSRF token missing. Please refresh or contact support.') }}</p>`;
        }
        if (startButton) { startButton.disabled = true; }
    }
</script>
@endpush
</x-app-layout>