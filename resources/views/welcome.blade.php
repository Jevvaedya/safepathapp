<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
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

            {{-- Section About Us --}}
            <section id="about-us" class="pt-24 pb-16 lg:pt-32 lg:pb-24 bg-background-main relative z-10">
                {{-- ... (Konten tidak berubah) ... --}}
            </section>

            {{-- Section Meet Our Team --}}
            <section id="meet-our-team" class="pt-16 pb-24 lg:pt-24 lg:pb-32 bg-background-main relative z-10">
                {{-- ... (Konten tidak berubah) ... --}}
            </section>

            {{-- Footer --}}
            <footer class="bg-black text-gray-300 mt-auto">
                {{-- ... (Konten tidak berubah) ... --}}
            </footer>

        </div>
    </body>
</html>