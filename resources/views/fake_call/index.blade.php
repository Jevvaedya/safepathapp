<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-500 leading-tight">
            {{ __('Fake Call') }}
        </h2>
    </x-slot>

    <div class="py-7">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-0">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-text-main">
                    {{-- ... (Seluruh bagian HTML Anda dari atas sampai form tidak berubah) ... --}}
                    <h3 class="text-2xl font-semibold text-text-main mb-6 text-center">{{ __('Set Up a Fake Call') }}</h3>
                    <div class="mb-4">
                        <label for="voiceGender" class="block text-sm font-semibold text-text-main">{{ __('Caller Voice (for AI):') }}</label>
                        <select id="voiceGender" name="voice_gender" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">
                            <option value="female">{{ __('Female') }}</option>
                            <option value="male">{{ __('Male') }}</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="callTopic" class="block text-sm font-semibold text-text-main">{{ __('Call Topic:') }}</label>
                        <select id="callTopic" name="call_topic" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:border-primary focus:ring-primary rounded-md shadow-sm">
                            <option value="boss">{{ __('Urgent call from Boss') }}</option>
                            <option value="delivery">{{ __('Package Delivery Confirmation') }}</option>
                            <option value="friend_urgent">{{ __('Friend needs help urgently') }}</option>
                            <option value="family_checkin">{{ __('Family member checking in') }}</option>
                        </select>
                    </div>
                    <div class="mb-8 p-4 border border-gray-200 rounded-lg">
                        <h4 class="text-md font-semibold text-gray-800 mb-2">{{ __('Use Your Own Recording') }}</h4>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                </button>
                            </div>
                        </div>
                        <p id="noCustomAudioMessage" class="text-sm text-gray-500 mb-3">{{ __('No custom recordings uploaded yet.') }}</p>
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

    {{-- ... (Seluruh Modal HTML Anda tidak berubah) ... --}}
    <div id="fakeCallTimerModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 hidden p-4"> ... </div>
    <div id="callScreenModal" class="fixed inset-0 bg-black flex items-center justify-center z-[60] hidden p-0 m-0"> ... </div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ... (Definisi variabel dan fungsi-fungsi lain tidak berubah)
    const voiceGenderSelect = document.getElementById('voiceGender');
    const callTopicSelect = document.getElementById('callTopic');
    // ... Dst.
    
    // ===================================================================
    // PERBAIKAN UTAMA: Blok Event Listener untuk Form Upload Diganti
    // ===================================================================
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

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch("{{ route('fakecall.uploadCustomAudio') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => {
                // Penanganan error yang lebih baik
                if (response.status === 419) {
                    throw new Error('Your session has expired. Please refresh the page and try again.');
                }
                if (!response.ok) {
                    // Coba baca error JSON dari Laravel (untuk validasi)
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                // Jika server merespon dengan sukses
                if (data.success && data.audio_url && data.file_name && data.id) {
                    uploadStatus.textContent = data.message || '{{ __("Upload successful!") }}';
                    uploadStatus.className = 'text-xs mt-2 text-green-600';
                    
                    // Tambahkan opsi baru ke dropdown dan langsung pilih
                    const newOption = new Option(data.file_name, data.audio_url);
                    newOption.dataset.id = data.id;
                    selectCustomAudio.add(newOption);
                    selectCustomAudio.value = data.audio_url;
                    
                    uploadCustomAudioForm.reset();
                    updateCustomAudioControlsState();
                } else {
                    // Jika format data sukses tidak sesuai
                    throw new Error(data.message || '{{ __("Invalid data received from server.") }}');
                }
            })
            .catch(error => {
                let errMsg = '{{ __("An unknown error occurred.") }}';

                // Cek jika error berasal dari validasi Laravel (format { message, errors })
                if (error.errors && error.errors.audio_file) {
                    errMsg = error.errors.audio_file[0];
                } else if (error.message) {
                    errMsg = error.message;
                }
                
                uploadStatus.textContent = `Error: ${errMsg}`;
                uploadStatus.className = 'text-xs mt-2 text-red-600';
            })
            .finally(() => {
                // Selalu aktifkan kembali tombol setelah proses selesai
                uploadButton.disabled = false;
            });
        });
    }

    // ... (Sisa event listener lainnya tidak berubah)
    // if (deleteSelectedCustomAudioButton) { ... }
    // if (startFakeCallButton) { ... }
});
</script>
@endpush
</x-app-layout>