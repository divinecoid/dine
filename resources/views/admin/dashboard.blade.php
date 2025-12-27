@extends('layouts.admin')

@section('title', 'Dashboard - DINE.CO.ID')

@section('page-title', 'Dashboard')

@section('content')
    <div class="space-y-6">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Brands -->
            <div class="bg-white dark:bg-[#161615] border border-slate-200 dark:border-[#3E3E3A] rounded-lg p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-500 dark:text-[#A1A09A]">Total Brands</p>
                        <p class="text-2xl font-semibold text-slate-900 dark:text-[#EDEDEC]">
                            {{ number_format($stats['total_brands']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Stores -->
            <div class="bg-white dark:bg-[#161615] border border-slate-200 dark:border-[#3E3E3A] rounded-lg p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-500 dark:text-[#A1A09A]">Total Stores</p>
                        <p class="text-2xl font-semibold text-slate-900 dark:text-[#EDEDEC]">
                            {{ number_format($stats['total_stores']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Menus -->
            <div class="bg-white dark:bg-[#161615] border border-slate-200 dark:border-[#3E3E3A] rounded-lg p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-500 dark:text-[#A1A09A]">Total Menus</p>
                        <p class="text-2xl font-semibold text-slate-900 dark:text-[#EDEDEC]">
                            {{ number_format($stats['total_menus']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="bg-white dark:bg-[#161615] border border-slate-200 dark:border-[#3E3E3A] rounded-lg p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-500 dark:text-[#A1A09A]">Total Orders</p>
                        <p class="text-2xl font-semibold text-slate-900 dark:text-[#EDEDEC]">
                            {{ number_format($stats['total_orders']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Chart -->
        <div class="bg-white dark:bg-[#161615] border border-slate-200 dark:border-[#3E3E3A] rounded-lg p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-[#EDEDEC] mb-4">Sales Overview (Last 30 Days)</h3>
            <div class="h-80 w-full relative">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Menus -->
            <div class="bg-white dark:bg-[#161615] border border-slate-200 dark:border-[#3E3E3A] rounded-lg shadow-sm">
                <div class="p-6 border-b border-slate-200 dark:border-[#3E3E3A]">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-[#EDEDEC]">Top 5 Best Selling Menus</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="text-xs text-slate-500 dark:text-[#A1A09A] uppercase bg-slate-50 dark:bg-[#0a0a0a] border-b border-slate-200 dark:border-[#3E3E3A]">
                                <th class="px-6 py-3 font-medium">Menu Name</th>
                                <th class="px-6 py-3 font-medium text-right">Sold Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-[#3E3E3A]">
                            @forelse($topMenus as $menu)
                                <tr class="hover:bg-slate-50 dark:hover:bg-[#0a0a0a]/50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-[#EDEDEC]">{{ $menu->menu_name }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-[#EDEDEC] text-right font-medium">
                                        {{ number_format($menu->total_qty) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-4 text-sm text-slate-500 dark:text-[#A1A09A] text-center">No
                                        sales data yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Stores -->
            <div class="bg-white dark:bg-[#161615] border border-slate-200 dark:border-[#3E3E3A] rounded-lg shadow-sm">
                <div class="p-6 border-b border-slate-200 dark:border-[#3E3E3A]">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-[#EDEDEC]">Top 5 Stores by Revenue</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="text-xs text-slate-500 dark:text-[#A1A09A] uppercase bg-slate-50 dark:bg-[#0a0a0a] border-b border-slate-200 dark:border-[#3E3E3A]">
                                <th class="px-6 py-3 font-medium">Store Name</th>
                                <th class="px-6 py-3 font-medium text-right">Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-[#3E3E3A]">
                            @forelse($topStores as $store)
                                <tr class="hover:bg-slate-50 dark:hover:bg-[#0a0a0a]/50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-[#EDEDEC]">{{ $store->name }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-900 dark:text-[#EDEDEC] text-right font-medium">RP
                                        {{ number_format($store->orders_sum_total_amount ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-4 text-sm text-slate-500 dark:text-[#A1A09A] text-center">No
                                        sales data yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-[#161615] border border-slate-200 dark:border-[#3E3E3A] rounded-lg shadow-sm">
            <div class="p-6 border-b border-slate-200 dark:border-[#3E3E3A]">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-[#EDEDEC]">Quick Actions</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <a href="{{ route('admin.brands.index') }}"
                        class="flex items-center p-4 border border-slate-200 dark:border-[#3E3E3A] rounded-lg hover:bg-slate-50 dark:hover:bg-[#0a0a0a] hover:border-[#F53003]/50 transition-colors">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-slate-400 dark:text-[#A1A09A]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                        </div>
                        <span class="ml-3 text-sm font-medium text-slate-900 dark:text-[#EDEDEC]">Add Brand</span>
                    </a>

                    <a href="{{ route('admin.stores.index') }}"
                        class="flex items-center p-4 border border-slate-200 dark:border-[#3E3E3A] rounded-lg hover:bg-slate-50 dark:hover:bg-[#0a0a0a] hover:border-[#F53003]/50 transition-colors">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-slate-400 dark:text-[#A1A09A]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                        </div>
                        <span class="ml-3 text-sm font-medium text-slate-900 dark:text-[#EDEDEC]">Add Store</span>
                    </a>

                    <a href="{{ route('admin.menus.index') }}"
                        class="flex items-center p-4 border border-slate-200 dark:border-[#3E3E3A] rounded-lg hover:bg-slate-50 dark:hover:bg-[#0a0a0a] hover:border-[#F53003]/50 transition-colors">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-slate-400 dark:text-[#A1A09A]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                        </div>
                        <span class="ml-3 text-sm font-medium text-slate-900 dark:text-[#EDEDEC]">Add Menu</span>
                    </a>

                    <a href="{{ route('admin.bank-accounts.index') }}"
                        class="flex items-center p-4 border border-slate-200 dark:border-[#3E3E3A] rounded-lg hover:bg-slate-50 dark:hover:bg-[#0a0a0a] hover:border-[#F53003]/50 transition-colors">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-slate-400 dark:text-[#A1A09A]" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                        </div>
                        <span class="ml-3 text-sm font-medium text-slate-900 dark:text-[#EDEDEC]">Add Bank Account</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Sales Chart
                const ctx = document.getElementById('salesChart').getContext('2d');
                const isDark = document.documentElement.getAttribute('data-theme') === 'dark' || !document.documentElement.getAttribute('data-theme');

                // Brand Primary Color
                const brandColor = '#F53003';
                const gridColor = isDark ? '#3E3E3A' : '#E2E8F0';
                const textColor = isDark ? '#A1A09A' : '#64748B';

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chartLabels) !!},
                        datasets: [{
                            label: 'Total Sales (RP)',
                            data: {!! json_encode($chartValues) !!},
                            borderColor: brandColor,
                            backgroundColor: 'rgba(245, 48, 3, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: brandColor,
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: brandColor
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: isDark ? '#161615' : '#fff',
                                titleColor: isDark ? '#EDEDEC' : '#0F172A',
                                bodyColor: isDark ? '#A1A09A' : '#64748B',
                                borderColor: gridColor,
                                borderWidth: 1
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: gridColor,
                                    drawBorder: false
                                },
                                ticks: {
                                    color: textColor,
                                    callback: function (value) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: textColor
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                    }
                });
            });
        </script>
    @endpush
@endsection