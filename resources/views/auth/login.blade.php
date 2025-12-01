<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - DINE.CO.ID Admin</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Prevent scrolling - Fixed height */
        html, body {
            height: 100% !important;
            width: 100% !important;
            overflow: hidden !important;
            position: fixed !important;
            background-color: #0a0a0a !important;
            color: #EDEDEC !important;
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
        }
        
        /* Dark Theme Utility Classes */
        [class*="bg-[#0a0a0a]"] { background-color: #0a0a0a !important; }
        [class*="bg-[#161615]"] { background-color: #161615 !important; }
        [class*="text-[#EDEDEC]"] { color: #EDEDEC !important; }
        [class*="text-[#A1A09A]"] { color: #A1A09A !important; }
        [class*="border-[#3E3E3A]"] { border-color: #3E3E3A !important; }
    </style>
</head>
<body class="bg-[#0a0a0a] text-[#EDEDEC]">
    <div class="h-screen w-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="flex items-center justify-center gap-2 mb-2">
                    <h1 class="text-3xl font-bold text-[#F53003]">DINE</h1>
                    <span class="text-sm text-[#A1A09A]">.CO.ID</span>
                </div>
                <h2 class="text-center text-xl font-semibold text-[#EDEDEC]">
                    Restaurant Management System
                </h2>
                <p class="mt-2 text-center text-sm text-[#A1A09A]">
                    Masuk sebagai Brand Owner atau Store Manager
                </p>
            </div>
            
            @if (session('error'))
                <div class="bg-[#1D0002] border border-[#F53003]/30 text-[#FF4433] px-4 py-3 rounded-lg relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-[#1D0002] border border-[#F53003]/30 text-[#FF4433] px-4 py-3 rounded-lg relative" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-[#161615] border border-[#3E3E3A] rounded-2xl p-8">
                <form class="space-y-6" action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-[#EDEDEC] mb-2">Nomor Telepon</label>
                            <input id="phone" name="phone" type="tel" autocomplete="tel" required 
                                   class="appearance-none relative block w-full px-4 py-3 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 sm:text-sm transition-colors" 
                                   placeholder="081234567890" value="{{ old('phone') }}">
                            <p class="mt-1 text-xs text-[#A1A09A]">Format: 081234567890 atau +6281234567890</p>
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-[#EDEDEC] mb-2">Password</label>
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                   class="appearance-none relative block w-full px-4 py-3 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 sm:text-sm transition-colors" 
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" 
                               class="h-4 w-4 bg-[#0a0a0a] border-[#3E3E3A] text-[#F53003] focus:ring-[#F53003]/50 rounded">
                        <label for="remember" class="ml-2 block text-sm text-[#A1A09A]">
                            Ingat saya
                        </label>
                    </div>

                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-[#F53003] hover:bg-[#d42800] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F53003]/50 focus:ring-offset-[#0a0a0a] transition-colors">
                            Masuk
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-[#A1A09A]">
                            Belum punya akun? 
                            <a href="{{ route('register') }}" class="font-medium text-[#F53003] hover:text-[#d42800] transition-colors">
                                Daftar di sini
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Prevent scrolling on mobile devices
        document.addEventListener('DOMContentLoaded', function() {
            // Prevent touch scrolling
            document.body.addEventListener('touchmove', function(e) {
                e.preventDefault();
            }, { passive: false });

            // Prevent wheel scrolling
            document.body.addEventListener('wheel', function(e) {
                e.preventDefault();
            }, { passive: false });

            // Prevent keyboard scrolling on mobile
            document.body.addEventListener('scroll', function(e) {
                e.preventDefault();
                window.scrollTo(0, 0);
            });

            // Lock scroll position
            window.addEventListener('scroll', function() {
                window.scrollTo(0, 0);
            });

            // Prevent iOS bounce scroll
            document.addEventListener('touchmove', function(e) {
                if (e.target.closest('input, textarea, select')) {
                    return; // Allow scrolling inside input fields
                }
                e.preventDefault();
            }, { passive: false });
        });

        // Prevent scroll on resize (mobile keyboard)
        window.addEventListener('resize', function() {
            window.scrollTo(0, 0);
        });
    </script>
</body>
</html>

