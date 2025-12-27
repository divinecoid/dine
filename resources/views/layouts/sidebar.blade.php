<aside id="sidebar"
    class="sidebar-transition fixed inset-y-0 left-0 z-50 w-64 bg-[var(--bg-sidebar)] border-r border-[var(--border-color)] transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0 flex flex-col">
    <!-- Sidebar Header - Sticky -->
    <div
        class="flex items-center justify-between h-16 px-6 border-b border-[var(--border-color)] bg-[var(--bg-sidebar)] sticky top-0 z-10 flex-shrink-0">
        <h1 class="text-xl font-bold text-[var(--text-main)]">DINE.CO.ID</h1>
        <button id="closeSidebar" class="lg:hidden text-[var(--text-muted)] hover:text-[var(--text-main)]">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Sidebar Navigation - Scrollable -->
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto min-h-0">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[var(--brand-primary)] text-white' : 'text-[var(--text-muted)] hover:bg-[var(--bg-main)] hover:text-[var(--text-main)]' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                </path>
            </svg>
            Dashboard
        </a>

        <!-- Kasir -->
        @if(auth()->user()->isKasir() || auth()->user()->isStoreManager() || auth()->user()->isBrandOwner())
            <a href="{{ route('admin.kasir.index') }}"
                class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.kasir.*') ? 'bg-[var(--brand-primary)] text-white' : 'text-[var(--text-muted)] hover:bg-[var(--bg-main)] hover:text-[var(--text-main)]' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Kasir
            </a>
        @endif

        <!-- Masterdata Group -->
        <div class="pt-4">
            <p class="px-4 text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Masterdata</p>
            <div class="mt-2 space-y-1">
                <!-- Brand -->
                <a href="{{ route('admin.brands.index') }}"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.brands.*') ? 'bg-[var(--brand-primary)] text-white' : 'text-[var(--text-muted)] hover:bg-[var(--bg-main)] hover:text-[var(--text-main)]' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                        </path>
                    </svg>
                    Brand
                </a>

                <!-- Store -->
                <a href="{{ route('admin.stores.index') }}"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.stores.*') ? 'bg-[var(--brand-primary)] text-white' : 'text-[var(--text-muted)] hover:bg-[var(--bg-main)] hover:text-[var(--text-main)]' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    Store
                </a>

                <!-- Meja -->
                <a href="{{ route('admin.tables.index') }}"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.tables.*') ? 'bg-[var(--brand-primary)] text-white' : 'text-[var(--text-muted)] hover:bg-[var(--bg-main)] hover:text-[var(--text-main)]' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z">
                        </path>
                    </svg>
                    Meja
                </a>

                <!-- Kategori -->
                <a href="{{ route('admin.categories.index') }}"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-[var(--brand-primary)] text-white' : 'text-[var(--text-muted)] hover:bg-[var(--bg-main)] hover:text-[var(--text-main)]' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                        </path>
                    </svg>
                    Kategori
                </a>

                <!-- Menu -->
                <a href="{{ route('admin.menus.index') }}"
                    class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.menus.*') ? 'bg-[var(--brand-primary)] text-white' : 'text-[var(--text-muted)] hover:bg-[var(--bg-main)] hover:text-[var(--text-main)]' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                    Menu
                </a>
            </div>
        </div>

        <!-- Users -->
        <a href="{{ route('admin.users.index') }}"
            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-[var(--brand-primary)] text-white' : 'text-[var(--text-muted)] hover:bg-[var(--bg-main)] hover:text-[var(--text-main)]' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                </path>
            </svg>
            Users
        </a>

        <!-- Rekening -->
        <a href="{{ route('admin.bank-accounts.index') }}"
            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bank-accounts.*') ? 'bg-[var(--brand-primary)] text-white' : 'text-[var(--text-muted)] hover:bg-[var(--bg-main)] hover:text-[var(--text-main)]' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
            Rekening
        </a>

        <!-- Kritik dan Saran -->
        <a href="{{ route('admin.feedback.index') }}"
            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.feedback.*') ? 'bg-[var(--brand-primary)] text-white' : 'text-[var(--text-muted)] hover:bg-[var(--bg-main)] hover:text-[var(--text-main)]' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                </path>
            </svg>
            Kritik dan Saran
        </a>

        <!-- Tarik Saldo -->
        <a href="{{ route('admin.withdrawals.index') }}"
            class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.withdrawals.*') ? 'bg-[var(--brand-primary)] text-white' : 'text-[var(--text-muted)] hover:bg-[var(--bg-main)] hover:text-[var(--text-main)]' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                </path>
            </svg>
            Tarik Saldo
        </a>
    </nav>

    <!-- Sidebar Footer - Sticky -->
    <div class="px-4 py-4 border-t border-[var(--border-color)] bg-[var(--bg-sidebar)] sticky bottom-0 flex-shrink-0">
        <!-- Theme Toggle -->
        <div class="flex items-center justify-between mb-4 px-1">
            <span class="text-xs font-medium text-[var(--text-muted)] uppercase tracking-wider">Appearance</span>
            <button id="themeToggle"
                class="relative inline-flex h-6 w-11 items-center rounded-full bg-[var(--bg-card)] border border-[var(--border-color)] transition-colors focus:outline-none">
                <span class="sr-only">Toggle theme</span>
                <span id="themeToggleHandle"
                    class="translate-x-1 inline-block h-4 w-4 transform rounded-full bg-[var(--text-muted)] transition-transform duration-200"></span>
            </button>
        </div>

        <div class="flex items-center mb-3">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-[var(--border-color)] flex items-center justify-center">
                    <span
                        class="text-sm font-medium text-[var(--text-main)]">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </div>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium text-[var(--text-main)]">{{ auth()->user()->name }}</p>
                <p class="text-xs text-[var(--text-muted)]">{{ auth()->user()->email }}</p>
                <p class="text-xs text-[var(--text-muted)] capitalize">
                    {{ auth()->user()->role === 'brand_owner' ? 'Pemilik Brand' : 'Store Manager' }}
                </p>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-[var(--text-main)] bg-[var(--brand-primary)] bg-opacity-20 rounded-lg hover:bg-[var(--brand-primary)] hover:bg-opacity-30 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                Keluar
            </button>
        </form>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const themeToggle = document.getElementById('themeToggle');
        const themeToggleHandle = document.getElementById('themeToggleHandle');
        const html = document.documentElement;

        // Initial state
        if (html.getAttribute('data-theme') === 'light') {
            themeToggleHandle.classList.remove('translate-x-1');
            themeToggleHandle.classList.add('translate-x-6');
            themeToggleHandle.classList.remove('bg-[var(--text-muted)]');
            themeToggleHandle.classList.add('bg-yellow-400');
        }

        themeToggle.addEventListener('click', async function () {
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            // Optimistic update
            html.setAttribute('data-theme', newTheme);

            // Animate toggle
            if (newTheme === 'light') {
                themeToggleHandle.classList.remove('translate-x-1');
                themeToggleHandle.classList.add('translate-x-6');
                themeToggleHandle.classList.remove('bg-[var(--text-muted)]');
                themeToggleHandle.classList.add('bg-yellow-400');
            } else {
                themeToggleHandle.classList.remove('translate-x-6');
                themeToggleHandle.classList.add('translate-x-1');
                themeToggleHandle.classList.remove('bg-yellow-400');
                themeToggleHandle.classList.add('bg-[var(--text-muted)]');
            }

            try {
                const response = await fetch('{{ route("admin.profile.appearance.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ appearance: newTheme })
                });

                if (!response.ok) {
                    throw new Error('Failed to update preference');
                }
            } catch (error) {
                console.error('Error updating appearance:', error);
                // Revert on error
                html.setAttribute('data-theme', currentTheme);
                // Revert toggle logic here if strictly needed, but might be jarring
            }
        });
    });
</script>