<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-500 leading-tight">
            {{ __('Fake Call') }}
        </h2>
    </x-slot>

    <div class="py-7">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-0">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-text-main dark:text-gray-100">
                    <h3 class="text-2xl font-semibold mb-6 text-center">{{ __('Set Up a Fake Call') }}</h3>
                    
                    {{-- Opsi AI Voice --}}
                    <div class="mb-4">
                        <label for="voiceGender" class="block text-sm font-semibold">{{ __('Caller Voice (for AI):') }}</label>
                        <select id="voiceGender" name="voice_gender" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="female">{{ __('Female') }}</option>
                            <option value="male">{{ __('Male') }}</option>
                        </select>
                    </div>

                    {{-- Opsi Topik Panggilan --}}
                    <div class="mb-4">
                        <label for="callTopic" class="block text-sm font-semibold">{{ __('Call Topic:') }}</label>
                        <select id="callTopic" name="call_topic" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="boss">{{ __('Urgent call from Boss') }}</option>
                            <option value="delivery">{{ __('Package Delivery Confirmation') }}</option>
                            <option value="friend_urgent">{{ __('Friend needs help urgently') }}</option>
                            <option value="family_checkin">{{ __('Family member checking in') }}</option>
                        </select>
                    </div>

                    {{-- Area Audio Kustom --}}
                    <div class="mb-8 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <h4 class="text-md font-semibold mb-2">{{ __('Use Your Own Recording') }}</h4>
                        
                        <div class="mb-3">
                            <label for="selectCustomAudio" class="block text-sm font-semibold">{{ __('Select Your Recording:') }}</label>
                            <div class="flex items-center space-x-2 mt-1">
                                <select id="selectCustomAudio" name="selected_custom_audio" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
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
                        <p id="noCustomAudioMessage" class="text-sm text-gray-500 dark:text-gray-400 mb-3 @if(isset($userCustomAudios) && $userCustomAudios->count() > 0) hidden @endif">{{ __('No custom recordings uploaded yet.') }}</p>
                        
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1 mt-4">{{ __('Or, upload a new one:') }}</p>
                        <form id="uploadCustomAudioForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="customAudioFile" class="block text-sm font-semibold">{{ __('Choose audio file (MP3, WAV, AAC, OGG - Max 5MB):') }}</label>
                                <input type="file" name="audio_file" id="customAudioFile" accept=".mp3,.wav,.aac,.ogg"
                                       class="mt-1 block w-full text-sm text-gray-500 cursor-pointer
                                              file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold
                                              file:bg-indigo-50 dark:file:bg-indigo-900 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100">
                            </div>
                            <button type="submit" id="uploadButton" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium shadow-sm">
                                {{ __('Upload Recording') }}
                            </button>
                        </form>
                        <p id="uploadStatus" class="text-xs mt-2"></p>
                    </div>
                    
                    <hr class="my-8 border-gray-200 dark:border-gray-700">
                    
                    {{-- Opsi Timer --}}
                    <div class="mb-6">
                        <label for="callTimer" class="block text-sm font-semibold">{{ __('Start Call After:') }}</label>
                        <select id="callTimer" name="call_timer" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="5">{{ __('5 seconds') }}</option>
                            <option value="10" selected>{{ __('10 seconds') }}</option>
                            <option value="30">{{ __('30 seconds') }}</option>
                            <option value="60">{{ __('1 minute') }}</option>
                        </select>
                    </div>
                    
                    {{-- Tombol Utama --}}
                    <div class="text-center">
                        <button id="startFakeCallButton" class="w-full sm:w-auto px-8 py-3 bg-gray-800 dark:bg-gray-200 hover:bg-gray-700 dark:hover:bg-white text-white dark:text-gray-800 font-bold rounded-lg text-lg shadow-md transition duration-150 ease-in-out">
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
        <div class="bg-white dark:bg-gray-800 p-6 sm:p-8 rounded-lg shadow-xl text-center w-full max-w-sm mx-auto">
            <p id="fakeCallTimerMessage" class="text-lg text-gray-700 dark:text-gray-200 mb-2"></p>
            <div class="relative w-full h-2 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden mb-4">
                <div id="fakeCallTimerProgressBar" class="absolute top-0 left-0 h-full bg-indigo-600 transition-all duration-1000 ease-linear"></div>
            </div>
            <p id="fakeCallTimeRemaining" class="text-sm text-gray-500 dark:text-gray-400 mb-6"></p>
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
    // KUMPULAN LENGKAP SEMUA LOGIKA JAVASCRIPT
    // ===================================================================

    // 1. ELEMEN UI (DOM)
    const voiceGenderSelect = document.getElementById('voiceGender');
    const callTopicSelect = document.getElementById('callTopic');
    const callTimerSelect = document.getElementById('callTimer');
    const startFakeCallButton = document.getElementById('startFakeCallButton');
    const fakeCallStatusArea = document.getElementById('fakeCallStatusArea');
    const fakeCallTimerModal = document.getElementById('fakeCallTimerModal');
    const fakeCallTimerMessage = document.getElementById('fakeCallTimerMessage');
    const fakeCallTimerProgressBar = document.getElementById('fakeCallTimerProgressBar');
    const fakeCallTimeRemaining = document.getElementById('fakeCallTimeRemaining');
    const cancel