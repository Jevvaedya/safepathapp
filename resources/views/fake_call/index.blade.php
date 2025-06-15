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
                                    {{-- Loop ini akan mengisi dropdown dengan rekaman yang sudah ada --}}
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
                                {{-- Nama input file disamakan dengan key di FormData untuk konsistensi --}}
                                <input type="file" name="audio_file" id="customAudioFile" accept=".mp3,.wav,.aac,.ogg"
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
        {{-- Konten Modal Timer --}}
    </div>

    {{-- Modal untuk Layar Panggilan --}}
    <div id="callScreenModal" class="fixed inset-0 bg-black flex items-center justify-center z-[60] hidden p-0 m-0">
         {{-- Konten Layar Panggilan --}}
    </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Elemen UI, State, dan Fungsi utilitas (seperti showTimerModal, resetFakeCallUI, etc.)
    // ... (Semua kode JS Anda sebelumnya yang tidak berhubungan dengan UPLOAD dan DELETE diletakkan di sini) ...
    // Saya akan fokus pada bagian UPLOAD dan DELETE untuk perbaikan

    // --- Elemen & Logika untuk Audio Kustom ---
    const selectCustomAudio = document.getElementById('selectCustomAudio');
    const deleteSelectedCustomAudioButton = document.getElementById('deleteSelectedCustomAudioButton');
    const uploadCustomAudioForm = document.getElementById('uploadCustomAudioForm');
    const customAudioFile = document.getElementById('customAudioFile');
    const uploadButton = document.getElementById('uploadButton');
    const uploadStatus = document.getElementById('uploadStatus');
    const noCustomAudioMessage = document.getElementById('noCustomAudioMessage');

    function updateCustomAudioControlsState() {
        if (!selectCustomAudio) return;
        const selectedOption = selectCustomAudio.options[selectCustomAudio.selectedIndex];
        const hasDeletableSelection = selectedOption && selectedOption.value !== "" && selectedOption.dataset.id;
        
        deleteSelectedCustomAudioButton.classList.toggle('hidden', !hasDeletableSelection);
        deleteSelectedCustomAudioButton.disabled = !hasDeletableSelection;
        
        const hasAnyCustomOptions = selectCustomAudio.options.length > 1; // lebih dari 1 karena ada opsi default "-- Use AI..."
        noCustomAudioMessage.classList.toggle('hidden', hasAnyCustomOptions);

        const useAICall = !selectCustomAudio.value; 
        document.getElementById('voiceGender').disabled = !useAICall;
    }

    if (selectCustomAudio) {
        selectCustomAudio.addEventListener('change', updateCustomAudioControlsState);
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
            // 'audio_file' adalah key yang akan dicek di Controller
            formData.append('audio_file', customAudioFile.files[0]);
            
            // Memberi feedback ke user
            uploadStatus.textContent = '{{ __("Uploading...") }}';
            uploadStatus.className = 'text-xs mt-2 text-blue-600';
            uploadButton.disabled = true;

            // Menggunakan fetch untuk mengirim file ke server
            fetch("{{ route('fakecall.uploadCustomAudio') }}", {
                method: 'POST',
                headers: {
                    // Header ini penting untuk Laravel mengenali request dan token keamanan
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                // Jika response tidak OK (bukan status 2xx), lempar error beserta body JSON-nya
                if (!response.ok) return response.json().then(err => { err.statusCode = response.status; throw err; });
                return response.json(); // Jika OK, proses body sebagai JSON
            })
            .then(data => {
                // Jika server merespons dengan sukses
                if (data.success && data.audio_url && data.file_name && data.id) {
                    uploadStatus.textContent = data.message || '{{ __("Upload successful!") }}';
                    uploadStatus.className = 'text-xs mt-2 text-green-600';
                    
                    // Tambahkan opsi baru ke dropdown, lalu pilih opsi tersebut
                    const newOption = new Option(data.file_name, data.audio_url);
                    newOption.dataset.id = data.id;
                    selectCustomAudio.add(newOption);
                    selectCustomAudio.value = data.audio_url; 
                    
                    uploadCustomAudioForm.reset(); // Kosongkan input file
                    updateCustomAudioControlsState(); 
                } else {
                    throw new Error(data.message || '{{ __("Upload failed or invalid data received.") }}');
                }
            })
            .catch(error => { 
                // Menangani semua jenis error (network, server error, etc)
                let errMsg = error.message || '{{ __("An unknown error occurred during upload.") }}';
                // Menangani error validasi dari Laravel
                if (error.errors && error.errors.audio_file) errMsg = error.errors.audio_file[0];
                // Menangani error file terlalu besar
                else if (error.statusCode === 413) errMsg = '{{ __("File is too large. Max 5MB allowed.") }}';
                
                uploadStatus.textContent = errMsg;
                uploadStatus.className = 'text-xs mt-2 text-red-600';
            })
            .finally(() => {
                // Apapun hasilnya, aktifkan kembali tombol upload
                uploadButton.disabled = false;
            });
        });
    }

    // --- LOGIKA DELETE AUDIO KUSTOM TERPILIH (AJAX) ---
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
            
            // Memberi feedback ke user
            deleteSelectedCustomAudioButton.disabled = true; 
            uploadStatus.textContent = '{{ __("Deleting...") }}'; 
            uploadStatus.className = 'text-xs mt-2 text-blue-600';

            // Membuat URL dengan route helper agar lebih aman dan mudah dipelihara
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
                // Update status UI, termasuk mengaktifkan kembali tombol hapus jika ada item lain yang dipilih
                updateCustomAudioControlsState(); 
            });
        });
    }

    // Panggil fungsi ini saat halaman dimuat untuk mengatur state awal tombol
    updateCustomAudioControlsState();
    
    // ... Sisa dari kode JavaScript Anda untuk memulai panggilan, dll.
});
</script>
@endpush
</x-app-layout>