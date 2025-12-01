<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - DINE.CO.ID Admin</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Dark Theme */
        html, body {
            background-color: #0a0a0a !important;
            color: #EDEDEC !important;
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            min-height: 100vh;
        }
        
        /* Dark Theme Utility Classes */
        [class*="bg-[#0a0a0a]"] { background-color: #0a0a0a !important; }
        [class*="bg-[#161615]"] { background-color: #161615 !important; }
        [class*="text-[#EDEDEC]"] { color: #EDEDEC !important; }
        [class*="text-[#A1A09A]"] { color: #A1A09A !important; }
        [class*="border-[#3E3E3A]"] { border-color: #3E3E3A !important; }

        /* Pull to Refresh Indicator */
        .pull-refresh-indicator {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #0a0a0a;
            transform: translateY(-100%);
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .pull-refresh-indicator.active {
            transform: translateY(0);
        }

        .pull-refresh-spinner {
            width: 24px;
            height: 24px;
            border: 3px solid #3E3E3A;
            border-top-color: #F53003;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-[#0a0a0a] text-[#EDEDEC]">
    <!-- Pull to Refresh Indicator -->
    <div id="pullRefreshIndicator" class="pull-refresh-indicator">
        <div class="pull-refresh-spinner"></div>
    </div>

    <div class="min-h-screen w-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="flex items-center justify-center gap-2 mb-2">
                    <h1 class="text-3xl font-bold text-[#F53003]">DINE</h1>
                    <span class="text-sm text-[#A1A09A]">.CO.ID</span>
                </div>
                <h2 class="text-center text-xl font-semibold text-[#EDEDEC]">
                    Daftar sebagai Brand Owner
                </h2>
                <p class="mt-2 text-center text-sm text-[#A1A09A]">
                    Buat akun baru untuk mengelola brand dan restoran Anda
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
                <form class="space-y-6" action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-[#EDEDEC] mb-2">Nama Lengkap</label>
                            <input id="name" name="name" type="text" autocomplete="name" required 
                                   class="appearance-none relative block w-full px-4 py-3 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 sm:text-sm transition-colors" 
                                   placeholder="Nama Anda" value="{{ old('name') }}">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-[#EDEDEC] mb-2">Email</label>
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                   class="appearance-none relative block w-full px-4 py-3 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 sm:text-sm transition-colors" 
                                   placeholder="email@example.com" value="{{ old('email') }}">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-[#EDEDEC] mb-2">Nomor Telepon</label>
                            <input id="phone" name="phone" type="tel" autocomplete="tel" required 
                                   class="appearance-none relative block w-full px-4 py-3 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 sm:text-sm transition-colors" 
                                   placeholder="081234567890" value="{{ old('phone') }}">
                            <p class="mt-1 text-xs text-[#A1A09A]">Format: 081234567890 atau +6281234567890</p>
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-[#EDEDEC] mb-2">Password</label>
                            <input id="password" name="password" type="password" autocomplete="new-password" required 
                                   class="appearance-none relative block w-full px-4 py-3 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 sm:text-sm transition-colors" 
                                   placeholder="Minimal 8 karakter">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-[#EDEDEC] mb-2">Konfirmasi Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required 
                                   class="appearance-none relative block w-full px-4 py-3 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 sm:text-sm transition-colors" 
                                   placeholder="Ulangi password">
                        </div>
                    </div>

                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-[#F53003] hover:bg-[#d42800] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F53003]/50 focus:ring-offset-[#0a0a0a] transition-colors">
                            Daftar
                        </button>
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-[#A1A09A]">
                            Sudah punya akun? 
                            <a href="{{ route('login') }}" class="font-medium text-[#F53003] hover:text-[#d42800] transition-colors">
                                Masuk di sini
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Pull to Refresh functionality
        let pullStartY = 0;
        let pullCurrentY = 0;
        let isPulling = false;
        let pullThreshold = 80; // Minimum distance to trigger refresh
        const indicator = document.getElementById('pullRefreshIndicator');

        document.addEventListener('DOMContentLoaded', function() {
            // Touch start - detect pull from top
            document.addEventListener('touchstart', function(e) {
                // Only allow pull from top of screen (scroll position at top)
                if (window.scrollY === 0 || window.pageYOffset === 0) {
                    pullStartY = e.touches[0].clientY;
                    isPulling = true;
                }
            }, { passive: true });

            // Touch move - handle pull gesture
            document.addEventListener('touchmove', function(e) {
                if (!isPulling) {
                    return; // Allow normal scrolling
                }

                pullCurrentY = e.touches[0].clientY;
                const pullDistance = pullCurrentY - pullStartY;

                // Only allow pull down (positive distance) when at top
                if (pullDistance > 0 && window.scrollY === 0) {
                    // Show indicator when pulling
                    if (pullDistance > 20) {
                        indicator.classList.add('active');
                    }

                    // Prevent default scroll if pulling down
                    if (pullDistance < pullThreshold) {
                        e.preventDefault();
                    }
                } else {
                    // Reset if scrolling up or not at top
                    isPulling = false;
                    indicator.classList.remove('active');
                }
            }, { passive: false });

            // Touch end - trigger refresh if threshold reached
            document.addEventListener('touchend', function(e) {
                if (isPulling) {
                    const pullDistance = pullCurrentY - pullStartY;
                    
                    if (pullDistance >= pullThreshold) {
                        // Trigger refresh
                        indicator.classList.add('active');
                        
                        // Reload page after short delay
                        setTimeout(function() {
                            window.location.reload();
                        }, 300);
                    } else {
                        // Reset if not enough pull
                        indicator.classList.remove('active');
                    }
                    
                    isPulling = false;
                    pullStartY = 0;
                    pullCurrentY = 0;
                }
            }, { passive: true });

            // Reset pull state on scroll
            window.addEventListener('scroll', function() {
                if (window.scrollY > 0) {
                    isPulling = false;
                    indicator.classList.remove('active');
                }
            });
        });
    </script>
</body>
</html>

