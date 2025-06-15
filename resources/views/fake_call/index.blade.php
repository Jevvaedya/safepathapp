<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-500 leading-tight">
            {{ __('Fake Call') }}
        </h2>
    </x-slot>

    <div class="py-7">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-0">
            <div class="bg-white overflow-hidden sm:rounded-lg">
                <div class="p-6 text-text-main">
                    <h3 class="text-2xl font-semibold text-text-main mb-6 text-center">
                        {{ __('Set Up a Fake Call') }}
                    </h3>

                    {{-- Pilihan Jenis Kelamin Suara (untuk AI Voice) --}}
                    <div class="mb-4">
                        <label for="voiceGender" class="block text-sm font-semibold text-text-main">{{ __('Caller Voice (for AI):') }}</label>
                        <select id="voiceGender" name="voice_gender" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">
                            <option value="female">{{ __('Female') }}</option>
                            <option value="male">{{ __('Male') }}</option>
                        </select>
                    </div>

                    {{-- Pilihan Topik Panggilan (untuk AI Voice & Caller Name) --}}
                    <div class="mb-4">
                        <label for="callTopic" class="block text-sm font-semibold text-text-main">{{ __('Call Topic:') }}</label>
                        <select id="callTopic" name="call_topic" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">
                            <option value="boss">{{ __('Urgent call from Boss') }}</option>
                            <option value="delivery">{{ __('Package Delivery Confirmation') }}</option>
                            <option value="friend_urgent">{{ __('Friend needs help urgently') }}</option>
                            <option value="family_checkin">{{ __('Family member checking in') }}</option>
                        </select>
                    </div>

                    {{-- Area Audio Kustom --}}
                    <div class="mb-8 p-4 border border-gray-200 rounded-lg">
                        <h4 class="text-md font-semibold text-gray-800 mb-2">{{ __('Use Your Own Recording') }}</h4>
                        
                        {{-- Dropdown untuk memilih audio kustom yang sudah diunggah --}}
                        <div class="mb-3">
                            <label for="selectCustomAudio" class="block text-sm font-semibold text-text-main">{{ __('Select Your Recording:') }}</label>
                            <div class="flex items-center space-x-2 mt-1">
                                <select id="selectCustomAudio" name="selected_custom_audio" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">
                                    <option value="">{{ __('-- Use AI Generated Voice --') }}</option>
                                    @if(isset($userCustomAudios) && $userCustomAudios->count() > 0)
                                        @foreach($userCustomAudios as $audio)
                                            <option value="{{ $audio->audio_url }}" data-id="{{ $audio->id }}">{{ $audio->file_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                
                                {{-- Tombol Play/Preview sudah DIHAPUS sesuai permintaan terakhir --}}
                                {{-- <button type="button" id="playSelectedCustomAudioButton" title="{{__('Play Selected Recording')}}" class="p-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md shadow-sm hidden">...</button> --}}

                                <button type="button" id="deleteSelectedCustomAudioButton" title="{{__('Delete Selected Recording')}}" class="p-2 bg-red-500 hover:bg-red-600 text-white rounded-md shadow-sm hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                         <p id="noCustomAudioMessage" class="text-sm text-gray-500 mb-3">{{ __('No custom recordings uploaded yet.') }}</p>

                        {{-- Form untuk mengunggah audio kustom baru --}}
                        <p class="text-sm text-gray-500 mb-1 mt-4">{{ __('Or, upload a new one:') }}</p>
                        <form id="uploadCustomAudioForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="customAudioFile" class="block text-sm font-semibold text-text-main">{{ __('Choose audio file (MP3, WAV, AAC, OGG - Max 5MB):') }}</label>
                                <input type="file" name="custom_audio" id="customAudioFile" accept=".mp3,.wav,.aac,.ogg"
                                       class="mt-1 block w-full px-3 py-2 text-base border border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary rounded-md shadow-sm text-sm text-gray-500 cursor-pointer
                                              file:mr-4 file:py-1 file:px-3
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700
                                              hover:file:bg-blue-100">
                            </div>
                            <button type="submit" id="uploadButton" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium shadow-sm">
                                {{ __('Upload Recording') }}
                            </button>
                        </form>
                        <p id="uploadStatus" class="text-xs mt-2"></p>
                    </div>

                    <hr class="my-8">

                    {{-- Pilihan Timer Tunda Panggilan --}}
                    <div class="mb-6">
                        <label for="callTimer" class="block text-sm font-semibold text-text-main">{{ __('Start Call After:') }}</label>
                        <select id="callTimer" name="call_timer" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">
                            <option value="5">{{ __('5 seconds') }}</option>
                            <option value="10">{{ __('10 seconds') }}</option>
                            <option value="30">{{ __('30 seconds') }}</option>
                            <option value="60">{{ __('1 minute') }}</option>
                        </select>
                    </div>

                    <div class="text-center">
                        <button id="startFakeCallButton" class="w-full sm:w-auto px-8 py-3 bg-black hover:bg-primary text-white font-bold rounded-lg text-lg shadow-md transition duration-150 ease-in-out">
                            {{ __('Start Fake Call') }}
                        </button>
                    </div>

                    <div id="fakeCallStatusArea" class="mt-8 text-center"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal untuk Countdown Timer --}}
    <div id="fakeCallTimerModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 hidden p-4">
        <div class="bg-white p-6 sm:p-8 rounded-lg shadow-xl text-center w-full max-w-sm mx-auto">
            <p id="fakeCallTimerMessage" class="text-lg text-gray-700 mb-2"></p>
            <div class="relative w-full h-2 bg-gray-200 rounded-full overflow-hidden mb-4">
                <div id="fakeCallTimerProgressBar" class="absolute top-0 left-0 h-full bg-primary transition-all duration-1000 ease-linear"></div>
            </div>
            <p id="fakeCallTimeRemaining" class="text-sm text-gray-500 mb-6"></p>
            <button id="cancelScheduledCallButton" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-medium shadow-sm focus:outline-none">
                {{ __('Cancel Scheduled Call') }}
            </button>
        </div>
    </div>

    {{-- Modal untuk Layar Panggilan --}}
    <div id="callScreenModal" class="fixed inset-0 bg-black flex items-center justify-center z-[60] hidden p-0 m-0">
        <div id="callScreenModalContent" class="bg-black w-full h-full sm:max-w-xs sm:max-h-[700px] sm:rounded-xl shadow-2xl overflow-hidden">
            {{-- Konten diisi JavaScript --}}
        </div>
    </div>

@push('scripts')
<script>
    // Elemen UI utama
    const voiceGenderSelect = document.getElementById('voiceGender');
    const callTopicSelect = document.getElementById('callTopic');
    const callTimerSelect = document.getElementById('callTimer');
    const startFakeCallButton = document.getElementById('startFakeCallButton');
    const fakeCallStatusArea = document.getElementById('fakeCallStatusArea');

    // Elemen Modal Timer
    const fakeCallTimerModal = document.getElementById('fakeCallTimerModal');
    const fakeCallTimerMessage = document.getElementById('fakeCallTimerMessage');
    const fakeCallTimerProgressBar = document.getElementById('fakeCallTimerProgressBar');
    const fakeCallTimeRemaining = document.getElementById('fakeCallTimeRemaining');
    const cancelScheduledCallButton = document.getElementById('cancelScheduledCallButton');

    // Elemen Modal Layar Panggilan
    const callScreenModal = document.getElementById('callScreenModal');
    const callScreenModalContent = document.getElementById('callScreenModalContent');

    // Elemen untuk Audio Kustom
    const selectCustomAudio = document.getElementById('selectCustomAudio');
    const deleteSelectedCustomAudioButton = document.getElementById('deleteSelectedCustomAudioButton');
    // const playSelectedCustomAudioButton = document.getElementById('playSelectedCustomAudioButton'); // Dihapus
    const uploadCustomAudioForm = document.getElementById('uploadCustomAudioForm');
    const customAudioFile = document.getElementById('customAudioFile');
    const uploadButton = document.getElementById('uploadButton');
    const uploadStatus = document.getElementById('uploadStatus');
    const noCustomAudioMessage = document.getElementById('noCustomAudioMessage');

    const loggedInUserName = @json($userName ?? Auth::user()->name);

    // Variabel state
    let currentRingtone = null;
    let callScheduleTimeoutId = null;
    let conversationIntervalId = null;
    let originalStartButtonText = '{{ __("Start Fake Call") }}';
    let currentConversationAudio = null;
    // let currentPreviewAudio = null; // Dihapus
    let countdownIntervalId = null;
    let activeCustomAudioUrlForCall = null;

    if (startFakeCallButton) {
        originalStartButtonText = startFakeCallButton.innerText;
    }

    function updateCustomAudioControlsState() {
        if (!selectCustomAudio) return;

        const selectedOption = selectCustomAudio.options[selectCustomAudio.selectedIndex];
        const hasDeletableSelection = selectedOption && selectedOption.value !== "" && selectedOption.dataset.id;

        if (deleteSelectedCustomAudioButton) {
            if (hasDeletableSelection) {
                deleteSelectedCustomAudioButton.classList.remove('hidden');
                deleteSelectedCustomAudioButton.disabled = false;
            } else {
                deleteSelectedCustomAudioButton.classList.add('hidden');
                deleteSelectedCustomAudioButton.disabled = true;
            }
        }
        
        // Logika untuk tombol play dihapus dari sini

        let hasAnyCustomOptions = false;
        for (let i = 0; i < selectCustomAudio.options.length; i++) {
            if (selectCustomAudio.options[i].value !== "") {
                hasAnyCustomOptions = true;
                break;
            }
        }
        if (noCustomAudioMessage) {
            if (hasAnyCustomOptions) {
                noCustomAudioMessage.classList.add('hidden');
            } else {
                noCustomAudioMessage.classList.remove('hidden');
            }
        }

        // Opsional: Disable/Enable dropdown AI berdasarkan pilihan audio kustom
        const useAICall = !selectCustomAudio.value; 
        if (voiceGenderSelect) voiceGenderSelect.disabled = !useAICall;
        // callTopicSelect mungkin tetap relevan untuk Caller Name, jadi tidak di-disable
    }
    
    if (selectCustomAudio) {
        selectCustomAudio.addEventListener('change', updateCustomAudioControlsState);
    }

    // Event listener untuk playSelectedCustomAudioButton DIHAPUS

    function showTimerModal(message, durationSeconds) { /* ... (Sama) ... */ 
        if (fakeCallTimerModal && fakeCallTimerMessage && fakeCallTimerProgressBar && fakeCallTimeRemaining) {
            fakeCallTimerMessage.innerText = message;
            fakeCallTimerProgressBar.style.width = '100%';
            let timeLeft = durationSeconds;
            fakeCallTimeRemaining.innerText = `{{ __('Starting in') }} ${timeLeft} {{ __('seconds') }}...`;
            if (countdownIntervalId) clearInterval(countdownIntervalId);
            countdownIntervalId = setInterval(() => {
                timeLeft--;
                if (timeLeft >= 0) {
                    fakeCallTimeRemaining.innerText = `{{ __('Starting in') }} ${timeLeft} {{ __('seconds') }}...`;
                    const percentageLeft = (timeLeft / durationSeconds) * 100;
                    fakeCallTimerProgressBar.style.width = `${percentageLeft}%`;
                } else {
                    clearInterval(countdownIntervalId);
                    countdownIntervalId = null;
                }
            }, 1000);
            fakeCallTimerProgressBar.style.transitionDuration = `${durationSeconds}s`;
            requestAnimationFrame(() => {
                requestAnimationFrame(() => { 
                     fakeCallTimerProgressBar.style.width = '0%';
                });
            });
            fakeCallTimerModal.classList.remove('hidden');
        }
    }
    function hideTimerModal() { /* ... (Sama) ... */ 
        if (fakeCallTimerModal) fakeCallTimerModal.classList.add('hidden');
        if (countdownIntervalId) { clearInterval(countdownIntervalId); countdownIntervalId = null; }
        if (fakeCallTimerProgressBar) { 
            fakeCallTimerProgressBar.style.transitionDuration = '0s'; 
            fakeCallTimerProgressBar.style.width = '100%';
        }
    }
    function showCallScreenModal(htmlContent) { /* ... (Sama) ... */ 
        if (callScreenModal && callScreenModalContent) {
            callScreenModalContent.innerHTML = htmlContent;
            callScreenModal.classList.remove('hidden');
        }
    }
    function hideCallScreenModal() { /* ... (Sama) ... */ 
        if (callScreenModal) {
            callScreenModal.classList.add('hidden');
            if (callScreenModalContent) callScreenModalContent.innerHTML = '';
        }
    }
    
    function resetFakeCallUI(isCancellationFromModal = false) {
        hideTimerModal();
        hideCallScreenModal(); 

        if (callScheduleTimeoutId) { clearTimeout(callScheduleTimeoutId); callScheduleTimeoutId = null; }
        if (conversationIntervalId) { clearInterval(conversationIntervalId); conversationIntervalId = null; }
        
        if (currentRingtone) { currentRingtone.pause(); currentRingtone.currentTime = 0; currentRingtone = null; }
        if (currentConversationAudio) { currentConversationAudio.pause(); currentConversationAudio.currentTime = 0; currentConversationAudio = null; }
        // if (currentPreviewAudio) { // Dihapus
        //     currentPreviewAudio.pause(); currentPreviewAudio.currentTime = 0; currentPreviewAudio = null;
        // }
        activeCustomAudioUrlForCall = null; 
        if ('speechSynthesis' in window && window.speechSynthesis.speaking) window.speechSynthesis.cancel();
        
        if (fakeCallStatusArea) fakeCallStatusArea.innerHTML = ''; 
        
        if(startFakeCallButton) {
            startFakeCallButton.disabled = false;
            startFakeCallButton.innerText = originalStartButtonText; 
        }
        
        if(callTimerSelect) callTimerSelect.disabled = false;
        if(voiceGenderSelect) voiceGenderSelect.disabled = false;
        if(callTopicSelect) callTopicSelect.disabled = false;
        if(selectCustomAudio) selectCustomAudio.disabled = false;
        if(customAudioFile) { customAudioFile.disabled = false; customAudioFile.value = ''; }
        if(uploadButton) uploadButton.disabled = false;
        
        updateCustomAudioControlsState();

        if (isCancellationFromModal && fakeCallStatusArea) {
            fakeCallStatusArea.innerHTML = `<p class="text-center text-gray-600 p-4">{{ __('Fake call cancelled.') }}</p>`;
            setTimeout(() => { if(fakeCallStatusArea) fakeCallStatusArea.innerHTML = ''; }, 3000);
        }
    }

    if (cancelScheduledCallButton) {
        cancelScheduledCallButton.addEventListener('click', () => resetFakeCallUI(true));
    }
    
    function handleAudioError(context, error, fileName = '') { /* ... (Sama) ... */ 
        console.error(`Error ${context} audio ${fileName}:`, error);
        const audioErrorP = document.createElement('p');
        audioErrorP.className = 'text-xs text-red-500 mt-2 text-center';
        audioErrorP.innerText = `Audio error: ${fileName}. ${error.message || 'Unknown error.'}`;
        
        if (callScreenModal && !callScreenModal.classList.contains('hidden') && callScreenModalContent) {
            const currentCallScreenContentChild = callScreenModalContent.firstChild; 
            if (currentCallScreenContentChild) {
                currentCallScreenContentChild.appendChild(audioErrorP);
            } else {
                callScreenModalContent.appendChild(audioErrorP);
            }
        } else if (fakeCallStatusArea) { 
            fakeCallStatusArea.innerHTML = '';
            fakeCallStatusArea.appendChild(audioErrorP);
        }
    }

    // --- LOGIKA UPLOAD AUDIO KUSTOM (AJAX) ---
    if (uploadCustomAudioForm) {
        uploadCustomAudioForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!customAudioFile.files.length) {
                uploadStatus.textContent = '{{ __("Please choose a file first.") }}';
                uploadStatus.className = 'text-xs mt-2 text-red-600';
                return;
            }
            const formData = new FormData();
            formData.append('custom_audio', customAudioFile.files[0]);
            uploadStatus.textContent = '{{ __("Uploading...") }}';
            uploadStatus.className = 'text-xs mt-2 text-blue-600';
            uploadButton.disabled = true;

            fetch("{{ route('fakecall.uploadCustomAudio') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) return response.json().then(err => { err.statusCode = response.status; throw err; });
                return response.json();
            })
            .then(data => {
                if (data.success && data.audio_url && data.file_name && data.id) {
                    uploadStatus.textContent = data.message || '{{ __("Upload successful!") }}';
                    uploadStatus.className = 'text-xs mt-2 text-green-600';
                    
                    if (selectCustomAudio) {
                        const newOption = new Option(data.file_name, data.audio_url);
                        newOption.dataset.id = data.id;
                        selectCustomAudio.add(newOption);
                        selectCustomAudio.value = data.audio_url; 
                    }
                     customAudioFile.value = '';
                     updateCustomAudioControlsState(); 
                } else {
                    throw new Error(data.message || '{{ __("Upload failed or invalid data received.") }}');
                }
            })
            .catch(error => { 
                let errMsg = error.message || '{{ __("An unknown error occurred during upload.") }}';
                if (error.errors && error.errors.custom_audio) errMsg = error.errors.custom_audio[0];
                else if (error.statusCode === 413) errMsg = '{{ __("File is too large. Max 5MB allowed.") }}';
                uploadStatus.textContent = errMsg;
                uploadStatus.className = 'text-xs mt-2 text-red-600';
            })
            .finally(() => {
                uploadButton.disabled = false;
            });
        });
    }

    // --- LOGIKA DELETE AUDIO KUSTOM TERPILIH (AJAX) ---
    if (deleteSelectedCustomAudioButton && selectCustomAudio) {
        deleteSelectedCustomAudioButton.addEventListener('click', function() {
            const selectedOption = selectCustomAudio.options[selectCustomAudio.selectedIndex];
            if (!selectedOption || !selectedOption.dataset.id) {
                alert('{{ __("Please select a recording to delete.") }}');
                return;
            }
            if (!confirm(`{{ __("Are you sure you want to delete this recording: ") }} "${selectedOption.text}"?`)) {
                return;
            }
            
            const audioIdToDelete = selectedOption.dataset.id;
            deleteSelectedCustomAudioButton.disabled = true; 
            // if(playSelectedCustomAudioButton) playSelectedCustomAudioButton.disabled = true; // Tombol play sudah dihapus
            uploadStatus.textContent = '{{ __("Deleting...") }}'; 
            uploadStatus.className = 'text-xs mt-2 text-blue-600';

            fetch(`/fakecall/custom-audio/${audioIdToDelete}`, { 
                method: 'DELETE', 
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                 if (!response.ok) return response.json().then(err => { throw err; });
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    selectCustomAudio.remove(selectCustomAudio.selectedIndex); 
                    selectCustomAudio.value = ""; 
                    uploadStatus.textContent = data.message || '{{ __("Recording deleted.") }}';
                    uploadStatus.className = 'text-xs mt-2 text-green-600';
                } else {
                     throw new Error(data.message || '{{ __("Failed to delete recording.") }}');
                }
            })
            .catch(error => { 
                uploadStatus.textContent = error.message || '{{ __("Error deleting recording.") }}';
                uploadStatus.className = 'text-xs mt-2 text-red-600';
            })
            .finally(() => {
                updateCustomAudioControlsState(); 
            });
        });
    }

    // --- LOGIKA UTAMA FAKE CALL ---
    if (startFakeCallButton) { 
        startFakeCallButton.addEventListener('click', function() {
            // Hapus logika stop preview audio
            // if (currentPreviewAudio && !currentPreviewAudio.paused) { ... }

            resetFakeCallUI(); 

            activeCustomAudioUrlForCall = null; 
            if (selectCustomAudio && selectCustomAudio.value) {
                activeCustomAudioUrlForCall = selectCustomAudio.value;
                console.log('Custom audio selected from dropdown for the call:', activeCustomAudioUrlForCall);
            } else {
                console.log('No custom audio selected from dropdown, AI voice will be used.');
            }

            const selectedGender = voiceGenderSelect.value;
            const selectedTopicValue = callTopicSelect.value; 
            const selectedTopicText = callTopicSelect.options[callTopicSelect.selectedIndex].text; 
            const selectedTimerValue = parseInt(callTimerSelect.value);

            startFakeCallButton.disabled = true; 
            voiceGenderSelect.disabled = true; callTopicSelect.disabled = true; callTimerSelect.disabled = true;
            if(selectCustomAudio) selectCustomAudio.disabled = true;
            if(deleteSelectedCustomAudioButton) deleteSelectedCustomAudioButton.disabled = true;
            // if(playSelectedCustomAudioButton) playSelectedCustomAudioButton.disabled = true; // Tombol sudah dihapus
            if(customAudioFile) customAudioFile.disabled = true;
            if(uploadButton) uploadButton.disabled = true;

            showTimerModal(`{{ __('Fake call is being scheduled...') }}`, selectedTimerValue);
            if(fakeCallStatusArea) fakeCallStatusArea.innerHTML = '';

            callScheduleTimeoutId = setTimeout(function() {
                callScheduleTimeoutId = null; 
                hideTimerModal(); 
                
                let callerName = callTopicSelect.options[callTopicSelect.selectedIndex].text; 
                if (callerName.toLowerCase().includes('boss')) callerName = '{{ __("Big Boss") }}';
                else if (callerName.toLowerCase().includes('delivery') || callerName.toLowerCase().includes('paket')) callerName = '{{ __("Courier") }}';
                else if (callerName.toLowerCase().includes('friend') || callerName.toLowerCase().includes('teman')) callerName = '{{ __("Close Friend") }}';
                else if (callerName.toLowerCase().includes('family') || callerName.toLowerCase().includes('keluarga')) callerName = '{{ __("Family") }}';

                const incomingCallHTML = `
                    <div class="bg-black text-white p-6 w-full h-full flex flex-col justify-between items-center font-sans">
                        <div class="flex-grow flex flex-col justify-center items-center text-center pt-10">
                            <div class="w-24 h-24 bg-gray-700 rounded-full mx-auto flex items-center justify-center mb-3 shadow-md">
                                <span class="text-4xl text-gray-400">${callerName.substring(0,1).toUpperCase()}</span>
                            </div>
                            <p class="text-3xl font-medium tracking-tight">${callerName}</p>
                            <p class="text-green-400 text-sm animate-pulse mt-1">Incoming Call...</p>
                        </div>
                        <div class="w-full pb-10 sm:pb-6">
                            <div class="flex justify-around items-center">
                                <button id="declineFakeCallButton" title="{{ __('Decline') }}" class="p-4 bg-red-500 hover:bg-red-600 text-white rounded-full shadow-lg focus:outline-none transform transition hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor"><path d="M17.293 3.293a1 1 0 00-1.414 0L10 8.586 4.707 3.293a1 1 0 00-1.414 1.414L8.586 10l-5.293 5.293a1 1 0 101.414 1.414L10 11.414l5.293 5.293a1 1 0 001.414-1.414L11.414 10l5.293-5.293a1 1 0 000-1.414z" /></svg>
                                </button>
                                <button id="answerFakeCallButton" title="{{ __('Answer') }}" class="p-4 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-lg focus:outline-none transform transition hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>`;
                showCallScreenModal(incomingCallHTML);

                try {
                    currentRingtone = new Audio("{{ asset('audio/ringtone.mp3') }}"); 
                    currentRingtone.loop = true; 
                    currentRingtone.play().catch(e => handleAudioError('playing ringtone', e, 'ringtone.mp3'));
                } catch (e) { handleAudioError('creating ringtone object', e, 'ringtone.mp3'); }

                const declineButton = document.getElementById('declineFakeCallButton');
                const answerButton = document.getElementById('answerFakeCallButton');

                if (declineButton) declineButton.addEventListener('click', () => resetFakeCallUI());
                
                if (answerButton) answerButton.addEventListener('click', function() {
                    if (currentRingtone) { currentRingtone.pause(); currentRingtone.currentTime = 0; currentRingtone = null; } 
                    if (conversationIntervalId) clearInterval(conversationIntervalId);
                    
                    const ongoingCallHTML = `
                        <div class="bg-black text-white p-6 w-full h-full flex flex-col justify-between items-center font-sans">
                             <div class="flex-grow flex flex-col justify-center items-center text-center pt-10">
                                <div class="w-24 h-24 bg-gray-700 rounded-full mx-auto flex items-center justify-center mb-3 shadow-md">
                                    <span class="text-4xl text-gray-400">${callerName.substring(0,1).toUpperCase()}</span>
                                </div>
                                <p class="text-2xl font-semibold mb-1">${callerName}</p>
                                <div id="fakeConversationTimer" class="text-gray-300 text-lg">00:00</div>
                                <p id="conversationAudioStatus" class="text-gray-400 text-sm my-4 italic">{{ __('Starting conversation...') }}</p>
                            </div>
                            <div class="w-full pb-10 sm:pb-6">
                                <div class="text-center">
                                    <button id="endOngoingCallButton" title="{{ __('End Call') }}" class="p-4 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-lg focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor"><path d="M17.293 3.293a1 1 0 00-1.414 0L10 8.586 4.707 3.293a1 1 0 00-1.414 1.414L8.586 10l-5.293 5.293a1 1 0 101.414 1.414L10 11.414l5.293 5.293a1 1 0 001.414-1.414L11.414 10l5.293-5.293a1 1 0 000-1.414z" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>`;
                    showCallScreenModal(ongoingCallHTML);

                    const conversationAudioStatusEl = document.getElementById('conversationAudioStatus');
                    
                    if (activeCustomAudioUrlForCall) {
                        if(conversationAudioStatusEl) conversationAudioStatusEl.innerText = '{{ __("Playing your recording...") }}';
                        currentConversationAudio = new Audio(activeCustomAudioUrlForCall);
                        currentConversationAudio.play()
                            .then(() => { currentConversationAudio.onended = function() { resetFakeCallUI(); }; })
                            .catch(e => {
                                handleAudioError('playing custom audio', e, 'Custom Recording');
                                if(conversationAudioStatusEl) conversationAudioStatusEl.innerText = `{{ __('Cannot play custom audio.') }}`;
                                setTimeout(() => resetFakeCallUI(), 3000);
                            });
                    } else {
                        if(conversationAudioStatusEl) conversationAudioStatusEl.innerText = '{{ __("Processing AI voice, please wait...") }}';
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        fetch("{{ route('fakecall.generateAudio') }}", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                            body: JSON.stringify({ topic_value: selectedTopicValue, gender: selectedGender })
                        })
                        .then(response => {
                            if (!response.ok) return response.json().then(errData => { throw new Error(errData.message || 'Failed to fetch audio from server.'); });
                            return response.json();
                        })
                        .then(data => {
                            if (data.success && data.audioUrl) {
                                if(conversationAudioStatusEl) conversationAudioStatusEl.innerText = '{{ __("Playing conversation...") }}';
                                currentConversationAudio = new Audio(data.audioUrl); 
                                currentConversationAudio.play()
                                    .then(() => { currentConversationAudio.onended = function() { resetFakeCallUI(); }; })
                                    .catch(e => {
                                        handleAudioError('playing server audio', e, data.audioUrl);
                                        if(conversationAudioStatusEl) conversationAudioStatusEl.innerText = `{{ __('Cannot play audio.') }}`;
                                    });
                            } else { throw new Error(data.message || 'Audio URL not received.'); }
                        })
                        .catch(error => {
                            if(conversationAudioStatusEl) conversationAudioStatusEl.innerText = error.message;
                            setTimeout(() => resetFakeCallUI(), 3000); 
                        });
                    }

                    let callSeconds = 0;
                    const conversationTimerDisplay = document.getElementById('fakeConversationTimer');
                    if (conversationIntervalId) clearInterval(conversationIntervalId); 
                    conversationIntervalId = setInterval(function() {
                        callSeconds++;
                        const mins = String(Math.floor(callSeconds / 60)).padStart(2, '0');
                        const secs = String(callSeconds % 60).padStart(2, '0');
                        if (conversationTimerDisplay) conversationTimerDisplay.innerText = `${mins}:${secs}`;
                    }, 1000);

                    const endOngoingCallButton = document.getElementById('endOngoingCallButton');
                    if(endOngoingCallButton) endOngoingCallButton.addEventListener('click', function() {
                        if (currentConversationAudio) {currentConversationAudio.pause(); currentConversationAudio.currentTime = 0; currentConversationAudio = null;}
                        resetFakeCallUI();
                    });
                }); 
            }, selectedTimerValue * 1000); 
        }); 
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateCustomAudioControlsState();
    });

</script>
@endpush
</x-app-layout>