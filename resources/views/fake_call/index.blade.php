<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-500 leading-tight">
            {{ __('Fake Call') }}
        </h2>
    </x-slot>

    <div class="py-7">
        {{-- Typo sm:px- diperbaiki menjadi sm:px-6 --}}
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-0">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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

                    {{-- Pilihan Topik Panggilan --}}
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
                                <input type="file" name="audio_file" id="customAudioFile" accept=".mp3,.wav,.aac,.ogg"
                                       class="mt-1 block w-full px-3 py-2 text-base border border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary rounded-md shadow-sm text-sm text-gray-500 cursor-pointer
                                              file:mr-4 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:font-semibold
                                              file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
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
                            <option value="10" selected>{{ __('10 seconds') }}</option>
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
document.addEventListener('DOMContentLoaded', function() {
    
    // ===================================================================
    // 1. ELEMEN UI (DOM)
    // ===================================================================
    const voiceGenderSelect = document.getElementById('voiceGender');
    const callTopicSelect = document.getElementById('callTopic');
    const callTimerSelect = document.getElementById('callTimer');
    const startFakeCallButton = document.getElementById('startFakeCallButton');
    const fakeCallStatusArea = document.getElementById('fakeCallStatusArea');

    // Modal Timer
    const fakeCallTimerModal = document.getElementById('fakeCallTimerModal');
    const fakeCallTimerMessage = document.getElementById('fakeCallTimerMessage');
    const fakeCallTimerProgressBar = document.getElementById('fakeCallTimerProgressBar');
    const fakeCallTimeRemaining = document.getElementById('fakeCallTimeRemaining');
    const cancelScheduledCallButton = document.getElementById('cancelScheduledCallButton');

    // Modal Layar Panggilan
    const callScreenModal = document.getElementById('callScreenModal');
    const callScreenModalContent = document.getElementById('callScreenModalContent');

    // Audio Kustom
    const selectCustomAudio = document.getElementById('selectCustomAudio');
    const deleteSelectedCustomAudioButton = document.getElementById('deleteSelectedCustomAudioButton');
    const uploadCustomAudioForm = document.getElementById('uploadCustomAudioForm');
    const customAudioFile = document.getElementById('customAudioFile');
    const uploadButton = document.getElementById('uploadButton');
    const uploadStatus = document.getElementById('uploadStatus');
    const noCustomAudioMessage = document.getElementById('noCustomAudioMessage');
    
    // ===================================================================
    // 2. STATE (VARIABEL PENYIMPAN STATUS)
    // ===================================================================
    let currentRingtone = null;
    let callScheduleTimeoutId = null;
    let conversationIntervalId = null;
    let originalStartButtonText = '{{ __("Start Fake Call") }}';
    let currentConversationAudio = null;
    let countdownIntervalId = null;

    // ===================================================================
    // 3. FUNGSI-FUNGSI BANTUAN (HELPER FUNCTIONS)
    // ===================================================================

    function updateCustomAudioControlsState() {
        if (!selectCustomAudio) return;
        const selectedOption = selectCustomAudio.options[selectCustomAudio.selectedIndex];
        const hasDeletableSelection = selectedOption && selectedOption.value !== "" && selectedOption.dataset.id;
        
        deleteSelectedCustomAudioButton.classList.toggle('hidden', !hasDeletableSelection);
        deleteSelectedCustomAudioButton.disabled = !hasDeletableSelection;
        
        const hasAnyCustomOptions = selectCustomAudio.options.length > 1;
        noCustomAudioMessage.classList.toggle('hidden', hasAnyCustomOptions);

        const useAICall = !selectCustomAudio.value; 
        if (voiceGenderSelect) voiceGenderSelect.disabled = !useAICall;
    }

    function showTimerModal(message, durationSeconds) {
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

    function hideTimerModal() {
        if (fakeCallTimerModal) fakeCallTimerModal.classList.add('hidden');
        if (countdownIntervalId) { clearInterval(countdownIntervalId); countdownIntervalId = null; }
        if (fakeCallTimerProgressBar) { 
            fakeCallTimerProgressBar.style.transitionDuration = '0s'; 
            fakeCallTimerProgressBar.style.width = '100%';
        }
    }

    function showCallScreenModal(htmlContent) {
        if (callScreenModal && callScreenModalContent) {
            callScreenModalContent.innerHTML = htmlContent;
            callScreenModal.classList.remove('hidden');
        }
    }
    
    function hideCallScreenModal() {
        if (callScreenModal) {
            callScreenModal.classList.add('hidden');
            if (callScreenModalContent) callScreenModalContent.innerHTML = '';
        }
    }

    function resetFakeCallUI(isCancellation = false) {
        hideTimerModal();
        hideCallScreenModal();

        if (callScheduleTimeoutId) { clearTimeout(callScheduleTimeoutId); callScheduleTimeoutId = null; }
        if (conversationIntervalId) { clearInterval(conversationIntervalId); conversationIntervalId = null; }
        
        if (currentRingtone) { currentRingtone.pause(); currentRingtone.currentTime = 0; currentRingtone = null; }
        if (currentConversationAudio) { currentConversationAudio.pause(); currentConversationAudio.currentTime = 0; currentConversationAudio = null; }
        
        if ('speechSynthesis' in window && window.speechSynthesis.speaking) {
            window.speechSynthesis.cancel();
        }
        
        if (startFakeCallButton) {
            startFakeCallButton.disabled = false;
            startFakeCallButton.innerText = originalStartButtonText;
        }
        if (voiceGenderSelect) voiceGenderSelect.disabled = false;
        if (callTopicSelect) callTopicSelect.disabled = false;
        if (callTimerSelect) callTimerSelect.disabled = false;
        if (selectCustomAudio) selectCustomAudio.disabled = false;
        if (customAudioFile) { customAudioFile.disabled = false; customAudioFile.value = ''; }
        if (uploadButton) uploadButton.disabled = false;
        
        updateCustomAudioControlsState();

        if (isCancellation && fakeCallStatusArea) {
            fakeCallStatusArea.innerHTML = `<p class="text-sm text-gray-600">{{ __('Fake call cancelled.') }}</p>`;
            setTimeout(() => { if(fakeCallStatusArea) fakeCallStatusArea.innerHTML = ''; }, 3000);
        }
    }

    // ===================================================================
    // 4. EVENT LISTENERS
    // ===================================================================
    
    updateCustomAudioControlsState();
    
    if (selectCustomAudio) {
        selectCustomAudio.addEventListener('change', updateCustomAudioControlsState);
    }
    
    if (cancelScheduledCallButton) {
        cancelScheduledCallButton.addEventListener('click', () => resetFakeCallUI(true));
    }

    if (uploadCustomAudioForm) {
        uploadCustomAudioForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!customAudioFile.files.length) {
                uploadStatus.textContent = '{{ __("Please choose a file first.") }}';
                uploadStatus.className = 'text-xs mt-2 text-red-600';
                return;
            }
            const formData = new FormData();
            formData.append('audio_file', customAudioFile.files[0]);
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
                    const newOption = new Option(data.file_name, data.audio_url);
                    newOption.dataset.id = data.id;
                    selectCustomAudio.add(newOption);
                    selectCustomAudio.value = data.audio_url; 
                    uploadCustomAudioForm.reset();
                    updateCustomAudioControlsState(); 
                } else {
                    throw new Error(data.message || '{{ __("Upload failed or invalid data received.") }}');
                }
            })
            .catch(error => { 
                let errMsg = error.message || '{{ __("An unknown error occurred during upload.") }}';
                if (error.errors && error.errors.audio_file) errMsg = error.errors.audio_file[0];
                else if (error.statusCode === 413) errMsg = '{{ __("File is too large. Max 5MB allowed.") }}';
                uploadStatus.textContent = errMsg;
                uploadStatus.className = 'text-xs mt-2 text-red-600';
            })
            .finally(() => {
                uploadButton.disabled = false;
            });
        });
    }

    if (deleteSelectedCustomAudioButton) {
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
            uploadStatus.textContent = '{{ __("Deleting...") }}'; 
            uploadStatus.className = 'text-xs mt-2 text-blue-600';

            let deleteUrl = "{{ route('fakecall.deleteCustomAudio', ['id' => ':id']) }}";
            deleteUrl = deleteUrl.replace(':id', audioIdToDelete);

            fetch(deleteUrl, { 
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

    if (startFakeCallButton) {
        startFakeCallButton.addEventListener('click', function() {
            resetFakeCallUI();

            const selectedGender = voiceGenderSelect.value;
            const selectedTopicValue = callTopicSelect.value;
            const selectedTimerValue = parseInt(callTimerSelect.value, 10);
            const customAudioUrl = selectCustomAudio.value;

            startFakeCallButton.disabled = true;
            voiceGenderSelect.disabled = true;
            callTopicSelect.disabled = true;
            callTimerSelect.disabled = true;
            selectCustomAudio.disabled = true;
            deleteSelectedCustomAudioButton.disabled = true;
            customAudioFile.disabled = true;
            uploadButton.disabled = true;
            
            showTimerModal(`{{ __('Fake call is being scheduled...') }}`, selectedTimerValue);
            
            callScheduleTimeoutId = setTimeout(() => {
                hideTimerModal();
                let callerName = callTopicSelect.options[callTopicSelect.selectedIndex].text;
                if (callerName.toLowerCase().includes('boss')) callerName = '{{ __("Big Boss") }}';
                else if (callerName.toLowerCase().includes('delivery')) callerName = '{{ __("Courier") }}';
                else if (callerName.toLowerCase().includes('friend')) callerName = '{{ __("Close Friend") }}';
                else if (callerName.toLowerCase().includes('family')) callerName = '{{ __("Family") }}';

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

                currentRingtone = new Audio("{{ asset('audio/ringtone.mp3') }}");
                currentRingtone.loop = true;
                currentRingtone.play().catch(e => console.error("Ringtone error:", e));

                document.getElementById('declineFakeCallButton').addEventListener('click', () => resetFakeCallUI());
                
                document.getElementById('answerFakeCallButton').addEventListener('click', () => {
                    if (currentRingtone) currentRingtone.pause();
                    
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
                            <div class="w-full pb-10 sm:pb-6"><div class="text-center">
                                <button id="endOngoingCallButton" title="{{ __('End Call') }}" class="p-4 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-lg focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor"><path d="M17.293 3.293a1 1 0 00-1.414 0L10 8.586 4.707 3.293a1 1 0 00-1.414 1.414L8.586 10l-5.293 5.293a1 1 0 101.414 1.414L10 11.414l5.293 5.293a1 1 0 001.414-1.414L11.414 10l5.293-5.293a1 1 0 000-1.414z" /></svg>
                                </button>
                            </div></div>
                        </div>`;
                    showCallScreenModal(ongoingCallHTML);
                    
                    document.getElementById('endOngoingCallButton').addEventListener('click', () => resetFakeCallUI());

                    const conversationAudioStatusEl = document.getElementById('conversationAudioStatus');
                    
                    if (customAudioUrl) {
                        if(conversationAudioStatusEl) conversationAudioStatusEl.innerText = '{{ __("Playing your recording...") }}';
                        currentConversationAudio = new Audio(customAudioUrl);
                        currentConversationAudio.play().catch(e => {
                            if(conversationAudioStatusEl) conversationAudioStatusEl.innerText = '{{ __("Cannot play custom audio.") }}';
                            setTimeout(() => resetFakeCallUI(), 2000);
                        });
                        currentConversationAudio.onended = () => resetFakeCallUI();
                    } else {
                        if(conversationAudioStatusEl) conversationAudioStatusEl.innerText = '{{ __("Processing AI voice, please wait...") }}';
                        fetch("{{ route('fakecall.generateAudio') }}", {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' },
                            body: JSON.stringify({ topic_value: selectedTopicValue, gender: selectedGender })
                        })
                        .then(res => {
                            if (!res.ok) return res.json().then(err => { throw new Error(err.message || 'Server error') });
                            return res.json();
                        })
                        .then(data => {
                            if (data.success && data.audioUrl) {
                                if(conversationAudioStatusEl) conversationAudioStatusEl.innerText = '{{ __("Playing conversation...") }}';
                                currentConversationAudio = new Audio(data.audioUrl);
                                currentConversationAudio.play().catch(e => {
                                    if(conversationAudioStatusEl) conversationAudioStatusEl.innerText = '{{ __("Cannot play AI audio.") }}';
                                    setTimeout(() => resetFakeCallUI(), 2000);
                                });
                                currentConversationAudio.onended = () => resetFakeCallUI();
                            } else { throw new Error(data.message || 'Audio URL not received.'); }
                        })
                        .catch(error => {
                            if(conversationAudioStatusEl) conversationAudioStatusEl.innerText = error.message;
                            setTimeout(() => resetFakeCallUI(), 3000); 
                        });
                    }

                    let callSeconds = 0;
                    const timerEl = document.getElementById('fakeConversationTimer');
                    conversationIntervalId = setInterval(() => {
                        callSeconds++;
                        const mins = String(Math.floor(callSeconds / 60)).padStart(2, '0');
                        const secs = String(callSeconds % 60).padStart(2, '0');
                        if (timerEl) timerEl.innerText = `${mins}:${secs}`;
                    }, 1000);
                });
            }, selectedTimerValue * 1000);
        });
    }
});
</script>
@endpush
</x-app-layout>