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
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-transition fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-900">DINE.CO.ID</h1>
                <button id="closeSidebar" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Dashboard
                </a>

                <!-- Masterdata Group -->
                <div class="pt-4">
                    <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Masterdata</p>
                    <div class="mt-2 space-y-1">
                        <!-- Brand -->
                        <a href="{{ route('admin.brands.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.brands.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Brand
                        </a>

                        <!-- Store -->
                        <a href="{{ route('admin.stores.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.stores.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Store
                        </a>

                        <!-- Meja -->
                        <a href="{{ route('admin.tables.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.tables.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Meja
                        </a>

                        <!-- Kategori -->
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Kategori
                        </a>

                        <!-- Menu -->
                        <a href="{{ route('admin.menus.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.menus.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Menu
                        </a>
                    </div>
                </div>

                <!-- Users -->
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    Users
                </a>

                <!-- Rekening -->
                <a href="{{ route('admin.bank-accounts.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bank-accounts.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    Rekening
                </a>

                <!-- Kritik dan Saran -->
                <a href="{{ route('admin.feedback.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.feedback.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    Kritik dan Saran
                </a>

                <!-- Tarik Saldo -->
                <a href="{{ route('admin.withdrawals.index') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.withdrawals.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Tarik Saldo
                </a>
            </nav>

            <!-- Sidebar Footer -->
            <div class="px-4 py-4 border-t border-gray-200">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-700">A</span>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-900">Admin</p>
                        <p class="text-xs text-gray-500">admin@dine.co.id</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Sidebar Overlay (Mobile) -->
        <div id="sidebarOverlay" class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 hidden lg:hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
            <!-- Top Header -->
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 lg:px-6">
                <button id="openSidebar" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <div class="flex-1 flex justify-end items-center space-x-4">
                    <h2 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h2>
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

