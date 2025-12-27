<!DOCTYPE html>
<html lang="id" data-theme="{{ auth()->user()->appearance ?? 'dark' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DINE.CO.ID - Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            /* Base Colors */
            --bg-main: #0a0a0a;
            --bg-sidebar: #161615;
            --bg-card: #161615;
            --bg-input: #0a0a0a;

            /* Text Colors */
            --text-main: #EDEDEC;
            --text-muted: #A1A09A;
            --text-inverse: #0a0a0a;

            /* Borders */
            --border-color: #3E3E3A;

            /* Brand */
            --brand-primary: #F53003;
            --brand-hover: #d42800;
        }

        /* Light Theme Override */
        [data-theme="light"] {
            --bg-main: #F4F4F5;
            /* Zinc-100 - Softer, slight grey background */
            --bg-sidebar: #FFFFFF;
            /* White sidebar */
            --bg-card: #FFFFFF;
            /* White cards */
            --bg-input: #FFFFFF;
            /* White inputs */

            --text-main: #18181B;
            /* Zinc-900 - Softer black */
            --text-muted: #71717A;
            /* Zinc-500 */
            --text-inverse: #FFFFFF;

            --border-color: #E5E7EB;
            /* Gray-200 */
        }

        /* Force table headers and body to be white in light mode to match user preference */
        [data-theme="light"] table,
        [data-theme="light"] table thead,
        [data-theme="light"] table tbody,
        [data-theme="light"] table tr,
        [data-theme="light"] table th,
        [data-theme="light"] table td {
            background-color: #FFFFFF !important;
        }

        [data-theme="light"] table thead th {
            border-bottom-color: var(--border-color) !important;
        }

        /* Force inputs/selects to be white in light mode (overriding the main bg utility class) */
        [data-theme="light"] input[class*="bg-[#0a0a0a]"],
        [data-theme="light"] select[class*="bg-[#0a0a0a]"],
        [data-theme="light"] textarea[class*="bg-[#0a0a0a]"] {
            background-color: var(--bg-input) !important;
        }

        .sidebar-transition {
            transition: transform 0.3s ease-in-out, background-color 0.3s, border-color 0.3s;
        }

        body {
            background-color: var(--bg-main) !important;
            color: var(--text-main) !important;
        }

        /* Utility Classes Mapped to Variables */
        [class*="bg-[#0a0a0a]"],
        .bg-\[\#0a0a0a\] {
            background-color: var(--bg-main) !important;
        }

        [class*="bg-[#161615]"],
        .bg-\[\#161615\] {
            background-color: var(--bg-sidebar) !important;
        }

        [class*="text-[#EDEDEC]"],
        .text-\[\#EDEDEC\] {
            color: var(--text-main) !important;
        }

        [class*="text-[#A1A09A]"],
        .text-\[\#A1A09A\] {
            color: var(--text-muted) !important;
        }

        [class*="border-[#3E3E3A]"],
        .border-\[\#3E3E3A\] {
            border-color: var(--border-color) !important;
        }

        [class*="bg-[#F53003]"],
        .bg-\[\#F53003\] {
            background-color: var(--brand-primary) !important;
        }

        [class*="hover:bg-[#d42800]"]:hover {
            background-color: var(--brand-hover) !important;
        }

        /* Input fields theme */
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="number"],
        input[type="password"],
        input[type="date"],
        input[type="time"],
        input[type="datetime-local"],
        textarea,
        select,
        select option {
            background-color: var(--bg-input) !important;
            color: var(--text-main) !important;
            border-color: var(--border-color);
        }

        input::placeholder,
        textarea::placeholder {
            color: var(--text-muted) !important;
        }

        /* Custom Scrollbar for Sidebar */
        nav::-webkit-scrollbar {
            width: 6px;
        }

        nav::-webkit-scrollbar-track {
            background: var(--bg-sidebar);
            border-radius: 3px;
        }

        nav::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        }

        nav::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Firefox scrollbar */
        nav {
            scrollbar-width: thin;
            scrollbar-color: var(--border-color) var(--bg-sidebar);
        }
    </style>
    @stack('styles')
    <!-- QR Code Generator Library for QRIS -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
</head>

<body class="bg-[#0a0a0a] text-[#EDEDEC]">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Sidebar Overlay (Mobile) -->
        <div id="sidebarOverlay" class="fixed inset-0 z-40 bg-black bg-opacity-75 hidden lg:hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0">
            <!-- Top Header -->
            <header class="bg-[#161615] border-b border-[#3E3E3A] h-16 flex items-center justify-between px-4 lg:px-6">
                <button id="openSidebar" class="lg:hidden text-[#A1A09A] hover:text-[#EDEDEC]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
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
        document.a ddEventListener('click', function (e) {
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