<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-t">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SafePath - Your Safety Companion</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/welcome.js'])

        <style>
            html {
                scroll-behavior: smooth;
            }
            #mainWelcomeHeader {
                background-color: transparent;
                box-shadow: none;
                transition: background-image 0.3s ease-in-out, background-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            }
            #mainWelcomeHeader.scrolled {
                background-image: linear-gradient(to right, rgba(203, 91, 255, 0.2), rgba(231, 121, 56, 0.2));
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.05);
            }
        </style>
    </head>
    <body class="font-sans antialiased text-text-main bg-gradient-to-r from-header-light to-primary">
        <div class="min-h-screen flex flex-col">

            {{-- Header untuk Landing Page --}}
            <header id="mainWelcomeHeader" class="sticky top-0 z-50 w-full transition-all duration-1000 ease-in-out py-4 bg-transparent">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-10 sm:h-12">
                        <a href="{{ url('/') }}">
                            <img id="headerWelcomeLogo" src="{{ asset('images/safepath-logo-white.png') }}" alt="SafePath Logo" class="block h-8 sm:h-9 w-auto">
                        </a>
                        <div>
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" id="navLinkWelcomeDashboard" class="font-semibold text-white hover:text-gray-200 transition-colors duration-1000">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" id="navLinkWelcomeLogin" class="font-semibold text-white hover:text-gray-200 transition-colors duration-1000">Log in</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" id="navLinkWelcomeRegister" class="ms-4 font-semibold text-white hover:text-gray-200 transition-colors duration-1000">Register</a>
                                    @endif
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </header>

            {{-- Konten Utama Landing Page --}}
            <main id="hero" class="h-screen flex flex-col items-center justify-center text-center text-white p-6 pt-0">
                <div class="max-w-2xl pt-8 pb-16 lg:pt-6 lg:pb-25">
                    <h1 class="text-6xl md:text-8xl mb-6 text-center">
                        <span class="font-extrabold text-white">Safe</span><span class="font-normal text-white">Path</span>
                    </h1>
                    <p class="text-lg md:text-xl italic text-gray-200 mb-8">
                        Langkah Kecil, Perlindungan Besar
                    </p>
                    <a href="#about-us" 
                       class="mt-2 inline-block bg-white bg-opacity-10 hover:bg-opacity-30 text-white font-medium py-3 px-8 text-lg transition-all duration-300 ease-in-out transform hover:scale-105">
                        {{ __('Learn More') }}
                    </a>
                </div>
            </main>

            <section id="about-us" class="pt-24 pb-16 lg:pt-32 lg:pb-24 bg-background-main relative z-10">
                <div class="max-w-3xl mx-auto px-6 lg:px-8 text-center">
                    <h2 class="text-3xl sm:text-4xl font-bold text-text-main mb-12">
                        About Us
                    </h2>
                    <div class="text-lg text-gray-700 space-y-6 text-left sm:text-justify">
                        <p>
                            SafePath adalah aplikasi pendamping keamanan yang dirancang untuk memberikan ketenangan pikiran dan bantuan cepat dalam situasi yang tidak nyaman atau berpotensi berbahaya. Kami percaya bahwa setiap langkah kecil menuju keamanan adalah perlindungan besar bagi diri sendiri dan orang-orang yang kita sayangi.
                        </p>
                        <p>
                            Dengan fitur-fitur inovatif seperti Notifikasi Darurat Cepat (SOS Alerts), Kontak Darurat yang terpercaya, Pelacakan Perjalanan Aman (Safe Walk), dan Panggilan Palsu (Fake Call) untuk mengalihkan perhatian, SafePath hadir untuk memberdayakan Anda dalam menjaga keselamatan diri.
                        </p>
                        <p>
                            Misi kami adalah menciptakan lingkungan yang lebih aman bagi semua orang melalui teknologi yang mudah diakses dan intuitif. Kami berkomitmen untuk terus mengembangkan SafePath dengan fitur-fitur yang relevan dan responsif terhadap kebutuhan keamanan Anda.
                        </p>
                    </div>
                </div>
            </section>

            <section id="meet-our-team" class="pt-16 pb-24 lg:pt-24 lg:pb-32 bg-background-main relative z-10">
                <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center">
                    <h2 class="text-3xl sm:text-4xl font-bold text-text-main mb-16">
                        Meet Our Team
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-y-12 sm:gap-x-8">
                        {{-- Kartu Anggota Tim 1 --}}
                        <div class="flex flex-col items-center">
                            <img src="{{ asset('images/rahel.jpg') }}" alt="Foto Anggota Tim 1" class="w-32 h-32 rounded-xl object-cover mb-4 shadow-lg">
                            <h3 class="text-xl font-bold text-text-main mt-2">Novi Inriani Rahel</h3>
                            <p class="text-text-secondary">Project Coordinator</p>
                        </div>
                        {{-- Kartu Anggota Tim 2 --}}
                        <div class="flex flex-col items-center">
                            <img src="{{ asset('images/citra.jpg') }}" alt="Foto Anggota Tim 2" class="w-32 h-32 rounded-xl object-cover mb-4 shadow-lg">
                            <h3 class="text-xl font-bold text-text-main mt-2">Chintya Citra Widyatami</h3>
                            <p class="text-text-secondary">UI/UX Designer</p>
                        </div>
                        {{-- Kartu Anggota Tim 3 --}}
                        <div class="flex flex-col items-center">
                            <img src="{{ asset('images/jepa.jpg') }}" alt="Foto Anggota Tim 3" class="w-32 h-32 rounded-xl object-cover mb-4 shadow-lg">
                            <h3 class="text-xl font-bold text-text-main mt-2">Jevva Edya Saputra</h3>
                            <p class="text-text-secondary">Backend & Frontend Developer</p>
                        </div>
                        {{-- Kartu Anggota Tim 4 --}}
                        <div class="flex flex-col items-center">
                            <img src="{{ asset('images/nat.jpg') }}" alt="Foto Anggota Tim 4" class="w-32 h-32 rounded-xl object-cover mb-4 shadow-lg">
                            <h3 class="text-xl font-bold text-text-main mt-2">Nathania Adristina</h3>
                            <p class="text-text-secondary">Backend & Frontend Developer</p>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="bg-black text-gray-300 mt-auto">
                <div class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
                    <div class="flex flex-col md:flex-row justify-between items-center md:items-start gap-10 text-center md:text-left">
                        {{-- Kolom Kiri: Logo dan Slogan --}}
                        <div class="flex flex-col items-center md:items-start">
                            <a href="{{ url('/') }}" class="flex items-center gap-3 mb-4">
                                <img src="{{ asset('images/safepath-logo-white.png') }}" alt="SafePath Logo" class="h-9 w-auto">
                                <span class="text-white text-2xl font-bold">SafePath</span>
                            </a>
                            <p class="italic text-gray-400">Langkah Kecil, Perlindungan Besar</p>
                        </div>
                        {{-- Kolom Kanan: Kontak dan Media Sosial --}}
                        <div>
                            <h3 class="font-bold text-lg text-white mb-4">Hubungi Kami</h3>
                            <div class="flex flex-col items-center md:items-start gap-3">
                                <a href="https://wa.me/6285229532952" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 hover:text-white transition-colors duration-300">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.31 20.6C8.75 21.39 10.36 21.82 12.04 21.82C17.5 21.82 21.95 17.37 21.95 11.91C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2ZM12.04 20.13C10.56 20.13 9.12 19.74 7.89 19L7.5 18.78L4.44 19.55L5.23 16.59L4.97 16.19C4.2 14.86 3.82 13.4 3.82 11.91C3.82 7.39 7.52 3.69 12.04 3.69C14.23 3.69 16.33 4.54 17.89 6.1C19.45 7.66 20.25 9.76 20.25 11.91C20.25 16.43 16.55 20.13 12.04 20.13ZM16.46 14.47C16.24 14.98 15.33 15.54 14.96 15.58C14.59 15.62 14.08 15.63 13.72 15.46C13.36 15.29 12.63 15.03 11.73 14.19C10.68 13.22 10.02 12.03 9.84 11.7C9.66 11.37 9.83 11.23 9.99 11.07C10.13 10.93 10.31 10.69 10.46 10.5C10.61 10.31 10.66 10.19 10.81 9.9C10.96 9.61 10.91 9.37 10.81 9.16C10.71 8.95 10.15 7.55 9.9 6.98C9.65 6.41 9.4 6.51 9.24 6.5C9.08 6.49 8.86 6.49 8.64 6.49C8.42 6.49 8.07 6.57 7.77 6.86C7.47 7.14 6.81 7.73 6.81 8.9C6.81 10.07 7.8 11.19 7.95 11.38C8.1 11.57 9.89 14.41 12.7 15.7C15.51 16.99 15.51 16.53 15.93 16.48C16.35 16.43 17.21 15.84 17.43 15.2C17.65 14.56 17.65 14.04 17.58 13.9C17.51 13.76 17.33 13.67 17.08 13.52C16.83 13.37 16.68 13.32 16.53 13.37C16.38 13.42 16.2 13.65 16.12 13.84C16.04 14.03 15.96 14.22 15.86 14.31C15.76 14.4 15.66 14.45 15.53 14.38L15.28 14.25C15.19 14.2 15.01 14.12 14.93 14.03C14.85 13.94 14.73 13.71 14.73 13.42C14.73 13.13 15.01 12.87 15.01 12.87C16.68 14.47 16.46 14.47 16.46 14.47Z"/></svg>
                                    <span>+62 852-2953-2952</span>
                                </a>
                                <a href="https://www.instagram.com/safepath.official" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 hover:text-white transition-colors duration-300">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm3.228 7.646a.75.75 0 01.944.44l1.25 3.5a.75.75 0 01-.44 1.054l-3.5 1.25a.75.75 0 01-1.054-.44l-1.25-3.5a.75.75 0 01.44-1.054l3.5-1.25zM12 10.5a1.5 1.5 0 100 3 1.5 1.5 0 000-3z" clip-rule="evenodd" /><path d="M16.5 7.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /></svg>
                                    <span>@safepath.official</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <hr class="border-gray-700 my-8">
                    <div class="text-center text-sm text-gray-500">
                        SafePath &copy; {{ date('Y') }}. All Rights Reserved.
                    </div>
                </div>
            </footer>

        </div>
    </body>
</html>