<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DINE.CO.ID - Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
        /* Dark Theme Utility Classes */
        [class*="bg-[#0a0a0a]"] { background-color: #0a0a0a !important; }
        [class*="bg-[#161615]"] { background-color: #161615 !important; }
        [class*="text-[#EDEDEC]"] { color: #EDEDEC !important; }
        [class*="text-[#A1A09A]"] { color: #A1A09A !important; }
        [class*="border-[#3E3E3A]"] { border-color: #3E3E3A !important; }
        [class*="bg-[#F53003]"] { background-color: #F53003 !important; }
        [class*="hover:bg-[#d42800]"]:hover { background-color: #d42800 !important; }
        [class*="placeholder-[#A1A09A]"]::placeholder { color: #A1A09A !important; }
        
        /* Input fields dark theme */
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="number"],
        input[type="password"],
        input[type="date"],
        input[type="time"],
        input[type="datetime-local"],
        textarea {
            background-color: #0a0a0a !important;
            color: #EDEDEC !important;
        }
        input::placeholder,
        textarea::placeholder {
            color: #A1A09A !important;
        }
        
        /* Select dropdown dark theme */
        select {
            background-color: #0a0a0a !important;
            color: #EDEDEC !important;
        }
        select option {
            background-color: #0a0a0a !important;
            color: #EDEDEC !important;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#0a0a0a] text-[#EDEDEC]">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-transition fixed inset-y-0 left-0 z-50 w-64 bg-[#161615] border-r border-[#3E3E3A] transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0 flex flex-col">
            <!-- Sidebar Header - Sticky -->
            <div class="flex items-center justify-between h-16 px-6 border-b border-[#3E3E3A] bg-[#161615] sticky top-0 z-10 flex-shrink-0">
                <h1 class="text-xl font-bold text-white">DINE.CO.ID</h1>
                <button id="closeSidebar" class="lg:hidden text-[#A1A09A] hover:text-[#EDEDEC]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Sidebar Navigation - Scrollable -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto min-h-0">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#F53003] text-white' : 'text-[#A1A09A] hover:bg-[#0a0a0a] hover:text-[#EDEDEC]' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Dashboard
                </a>

                <!-- Masterdata Group -->
                <div class="pt-4">
                    <p class="px-4 text-xs font-semibold text-[#A1A09A] uppercase tracking-wider">Masterdata</p>
                    <div class="mt-2 space-y-1">
                        <!-- Brand -->
                        <a href="{{ route('admin.brands.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.brands.*') ? 'bg-[#F53003] text-white' : 'text-[#A1A09A] hover:bg-[#0a0a0a] hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Brand
                        </a>

                        <!-- Store -->
                        <a href="{{ route('admin.stores.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.stores.*') ? 'bg-[#F53003] text-white' : 'text-[#A1A09A] hover:bg-[#0a0a0a] hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Store
                        </a>

                        <!-- Meja -->
                        <a href="{{ route('admin.tables.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.tables.*') ? 'bg-[#F53003] text-white' : 'text-[#A1A09A] hover:bg-[#0a0a0a] hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Meja
                        </a>

                        <!-- Kategori -->
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-[#F53003] text-white' : 'text-[#A1A09A] hover:bg-[#0a0a0a] hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Kategori
                        </a>

                        <!-- Menu -->
                        <a href="{{ route('admin.menus.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.menus.*') ? 'bg-[#F53003] text-white' : 'text-[#A1A09A] hover:bg-[#0a0a0a] hover:text-[#EDEDEC]' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Menu
                        </a>
                    </div>
                </div>

                <!-- Users -->
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-[#F53003] text-white' : 'text-[#A1A09A] hover:bg-[#0a0a0a] hover:text-[#EDEDEC]' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Users
                </a>

                <!-- Rekening -->
                <a href="{{ route('admin.bank-accounts.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bank-accounts.*') ? 'bg-[#F53003] text-white' : 'text-[#A1A09A] hover:bg-[#0a0a0a] hover:text-[#EDEDEC]' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Rekening
                </a>

                <!-- Kritik dan Saran -->
                <a href="{{ route('admin.feedback.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.feedback.*') ? 'bg-[#F53003] text-white' : 'text-[#A1A09A] hover:bg-[#0a0a0a] hover:text-[#EDEDEC]' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    Kritik dan Saran
                </a>

                <!-- Tarik Saldo -->
                <a href="{{ route('admin.withdrawals.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.withdrawals.*') ? 'bg-[#F53003] text-white' : 'text-[#A1A09A] hover:bg-[#0a0a0a] hover:text-[#EDEDEC]' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Tarik Saldo
                </a>
            </nav>

            <!-- Sidebar Footer - Sticky -->
            <div class="px-4 py-4 border-t border-[#3E3E3A] bg-[#161615] sticky bottom-0 flex-shrink-0">
                <div class="flex items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-[#3E3E3A] flex items-center justify-center">
                            <span class="text-sm font-medium text-[#EDEDEC]">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-[#EDEDEC]">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-[#A1A09A]">{{ auth()->user()->email }}</p>
                        <p class="text-xs text-[#A1A09A] capitalize">{{ auth()->user()->role === 'brand_owner' ? 'Pemilik Brand' : 'Store Manager' }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-[#EDEDEC] bg-[#F53003]/20 rounded-lg hover:bg-[#F53003]/30 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <!-- Sidebar Overlay (Mobile) -->
        <div id="sidebarOverlay" class="fixed inset-0 z-40 bg-black bg-opacity-75 hidden lg:hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
            <!-- Top Header -->
            <header class="bg-[#161615] border-b border-[#3E3E3A] h-16 flex items-center justify-between px-4 lg:px-6">
                <button id="openSidebar" class="lg:hidden text-[#A1A09A] hover:text-[#EDEDEC]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="flex-1 flex justify-end items-center space-x-4">
                    <h2 class="text-lg font-semibold text-[#EDEDEC]">@yield('page-title', 'Dashboard')</h2>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Sidebar Toggle (Mobile)
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const openSidebarBtn = document.getElementById('openSidebar');
        const closeSidebarBtn = document.getElementById('closeSidebar');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }

        openSidebarBtn?.addEventListener('click', openSidebar);
        closeSidebarBtn?.addEventListener('click', closeSidebar);
        sidebarOverlay?.addEventListener('click', closeSidebar);

        // Close sidebar on route change (optional)
        document.addEventListener('click', function(e) {
            if (e.target.closest('a[href]')) {
                if (window.innerWidth < 1024) {
                    closeSidebar();
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>

