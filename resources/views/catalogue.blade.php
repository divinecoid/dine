<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Catalogue - DINE.CO.ID</title>
    
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

        /* Pricing Section Layout */
        .pricing-container {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            justify-content: center;
            gap: 1.5rem;
        }
        @media (min-width: 768px) {
            .pricing-container {
                flex-direction: row !important;
                align-items: stretch !important;
            }
            .pricing-card {
                flex: 1;
                max-width: calc(50% - 0.75rem);
                display: flex;
                flex-direction: column;
            }
        }
        @media (min-width: 1024px) {
            .pricing-card {
                max-width: calc(33.333% - 1.33rem);
            }
        }

        /* Popup Modal */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.75);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background-color: #161615;
            border: 1px solid #3E3E3A;
            border-radius: 1rem;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }

        .modal-overlay.active .modal-content {
            transform: scale(1);
        }

        /* File Upload */
        .file-upload-area {
            border: 2px dashed #3E3E3A;
            border-radius: 0.5rem;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-area:hover {
            border-color: #F53003;
            background-color: #0f0f0f;
        }

        .file-upload-area.dragover {
            border-color: #F53003;
            background-color: #0f0f0f;
        }

        .file-preview {
            margin-top: 1rem;
            display: none;
        }

        .file-preview.active {
            display: block;
        }

        .file-preview img {
            max-width: 100%;
            max-height: 200px;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body class="bg-[#0a0a0a] text-[#EDEDEC]">
    <!-- Navigation -->
    <nav class="w-full border-b border-[#3E3E3A] bg-[#161615] sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-[#F53003]">DINE</h1>
                    <span class="text-sm text-[#A1A09A]">.CO.ID</span>
                </div>
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/admin/dashboard') }}" class="px-6 py-2 text-sm font-medium text-[#EDEDEC] border border-[#3E3E3A] rounded-lg hover:bg-[#1915014a] transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-6 py-2 text-sm font-medium text-[#EDEDEC] hover:underline">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-6 py-2 text-sm font-medium text-white bg-[#F53003] rounded-lg hover:bg-[#d42800] transition-colors">
                                    Get Started
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Catalogue Section -->
    <section class="max-w-7xl mx-auto px-6 lg:px-12 py-16 lg:py-20">
        <div class="text-center mb-12">
            <h2 class="text-2xl lg:text-3xl font-bold mb-4 text-[#EDEDEC]">
                💳 Pilih Paket yang Tepat untuk Bisnis Anda
            </h2>
            <p class="text-sm lg:text-base text-[#A1A09A] max-w-xl mx-auto leading-relaxed">
                Pilih paket yang sesuai dengan kebutuhan bisnis Anda
            </p>
        </div>

        <div class="pricing-container flex flex-col md:flex-row lg:flex-row items-stretch justify-center gap-6 lg:gap-8">
            <!-- CORE Package -->
            <div class="pricing-card relative bg-[#161615] border border-[#3E3E3A] rounded-xl p-6 hover:border-green-500/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-green-500/10 flex flex-col w-full">
                <div class="mb-5">
                        <div class="flex items-start justify-between mb-3 relative">
                            <div class="flex items-center gap-2.5 flex-1">
                                <div class="w-2.5 h-2.5 rounded-full bg-green-500"></div>
                                <h3 class="text-xl font-bold text-[#EDEDEC]">CORE</h3>
                            </div>
                        </div>
                        <p class="text-xs text-[#A1A09A] mb-4 leading-relaxed">Fondasi untuk memulai</p>
                        <div class="mb-5">
                            <span class="text-3xl font-bold text-[#EDEDEC]">Rp 20.000</span>
                            <span class="text-sm text-[#A1A09A] ml-1">/ bulan</span>
                        </div>
                </div>

                <div class="space-y-2.5 mb-6 flex-grow">
                    <div class="flex items-start gap-2.5">
                        <span class="text-green-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">QR Table Order (unlimited)</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-green-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">POS / Kasir dasar</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-green-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Manajemen produk sederhana</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-green-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Laporan penjualan harian & bulanan</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-green-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">1 Store</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-green-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">1 Device</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-green-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">URL default (subdomain)</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-green-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Support FAQ / komunitas</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-green-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">QRIS Dinamis</span>
                    </div>
                </div>

                <p class="text-xs text-[#A1A09A] italic mb-5 leading-relaxed">Cocok untuk: kedai kecil & UMKM yang ingin mulai digitalisasi.</p>

                <button onclick="openCheckoutModal('CORE', 20000)" class="w-full px-6 py-3 bg-[#F53003] text-white text-sm font-semibold rounded-xl hover:bg-[#d42800] transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-[#F53003]/20 mt-auto">
                    Checkout
                </button>
            </div>

            <!-- SCALE Package -->
            <div class="pricing-card relative bg-[#161615] border-2 border-[#F53003]/50 rounded-xl p-6 hover:border-[#F53003] transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-[#F53003]/20 flex flex-col w-full">
                <div class="mb-5">
                    <div class="flex items-start justify-between mb-3 relative">
                        <div class="flex items-center gap-2.5 flex-1">
                            <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                            <h3 class="text-xl font-bold text-[#EDEDEC]">SCALE</h3>
                        </div>
                        <div class="bg-[#F53003] text-white px-3 py-1.5 rounded-bl-lg rounded-tr-xl text-xs font-bold ml-2 flex-shrink-0">
                            POPULER
                        </div>
                    </div>
                    <p class="text-xs text-[#A1A09A] mb-4 leading-relaxed">Power-up untuk bisnis yang sedang berkembang</p>
                    <div class="mb-5">
                        <span class="text-3xl font-bold text-[#EDEDEC]">Rp 99.000</span>
                        <span class="text-sm text-[#A1A09A] ml-1">/ bulan</span>
                    </div>
                </div>

                <div class="space-y-2.5 mb-6 flex-grow">
                    <p class="text-xs font-semibold text-[#F53003] mb-3">Semua fitur CORE, plus:</p>
                    <div class="flex items-start gap-2.5">
                        <span class="text-blue-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Custom Domain Gratis</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-blue-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Branding custom (logo, tema, warna)</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-blue-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Multi User (Admin, Kasir, Supervisor)</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-blue-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Multi Device</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-blue-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Laporan keuangan lengkap</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-blue-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Laporan per brand & per store</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-blue-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Stok lanjutan + alert stok menipis</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-blue-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">QRIS Dinamis via API</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-blue-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Promo, diskon, voucher</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-blue-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Export PDF & Excel</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-blue-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Priority Support (jam kerja)</span>
                    </div>
                </div>

                <p class="text-xs text-[#A1A09A] italic mb-5 leading-relaxed">Cocok untuk: cafe/resto menengah, franchise kecil, dan bisnis yang butuh laporan profesional.</p>

                <button onclick="openCheckoutModal('SCALE', 99000)" class="w-full px-6 py-3 bg-[#F53003] text-white text-sm font-semibold rounded-xl hover:bg-[#d42800] transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-[#F53003]/20 mt-auto">
                    Checkout
                </button>
            </div>

            <!-- INFINITE Package -->
            <div class="pricing-card relative bg-[#161615] border border-[#3E3E3A] rounded-xl p-6 hover:border-orange-500/50 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-orange-500/10 flex flex-col w-full">
                <div class="mb-5">
                    <div class="flex items-start justify-between mb-3 relative">
                        <div class="flex items-center gap-2.5 flex-1">
                            <div class="w-2.5 h-2.5 rounded-full bg-orange-500"></div>
                            <h3 class="text-xl font-bold text-[#EDEDEC]">INFINITE</h3>
                        </div>
                        <div class="bg-orange-500 text-white px-3 py-1.5 rounded-bl-lg rounded-tr-xl text-xs font-bold ml-2 flex-shrink-0">
                            PREMIUM
                        </div>
                    </div>
                    <p class="text-xs text-[#A1A09A] mb-4 leading-relaxed">Kekuatan penuh tanpa batas</p>
                    <div class="mb-5">
                        <span class="text-3xl font-bold text-[#EDEDEC]">Rp 499.000</span>
                        <span class="text-sm text-[#A1A09A] ml-1">/ bulan</span>
                    </div>
                </div>

                <div class="space-y-2.5 mb-6 flex-grow">
                    <p class="text-xs font-semibold text-orange-500 mb-3">Semua fitur SCALE, plus:</p>
                    <div class="flex items-start gap-2.5">
                        <span class="text-orange-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Custom Domain + Full White-Label</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-orange-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Multi Store tanpa batas</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-orange-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Multi Brand Management</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-orange-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Advanced Owner Dashboard</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-orange-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">API Access</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-orange-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Shift & Payroll Module (opsional)</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-orange-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Integrasi Akuntansi (Jurnal, Accurate, dsb.)</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-orange-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Dedicated Account Manager</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-orange-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Onboarding & training</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-orange-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">SLA & 24/7 Premium Support</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="text-orange-500 text-base flex-shrink-0 mt-0.5">✔</span>
                        <span class="text-xs text-[#EDEDEC] leading-relaxed">Custom Workflow (approval, settlement, dsb.)</span>
                    </div>
                </div>

                <p class="text-xs text-[#A1A09A] italic mb-5 leading-relaxed">Cocok untuk: grup F&B, chain resto besar, dan brand multi-outlet.</p>

                <button onclick="openCheckoutModal('INFINITE', 499000)" class="w-full px-6 py-3 bg-[#F53003] text-white text-sm font-semibold rounded-xl hover:bg-[#d42800] transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-[#F53003]/20 mt-auto">
                    Checkout
                </button>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-[#3E3E3A] bg-[#161615] mt-16">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 py-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <h3 class="text-xl font-bold text-[#F53003]">DINE</h3>
                    <span class="text-sm text-[#A1A09A]">.CO.ID</span>
                </div>
                <p class="text-sm text-[#A1A09A]">
                    © {{ date('Y') }} DINE App. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Checkout Modal -->
    <div id="checkoutModal" class="modal-overlay" onclick="closeCheckoutModal(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-[#EDEDEC]">Checkout</h3>
                <button onclick="closeCheckoutModal()" class="text-[#A1A09A] hover:text-[#EDEDEC] transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="space-y-6">
                <!-- Package Info -->
                <div>
                    <p class="text-sm text-[#A1A09A] mb-2">Paket yang dipilih:</p>
                    <p id="selectedPackage" class="text-lg font-semibold text-[#EDEDEC]"></p>
                    <p id="packagePrice" class="text-2xl font-bold text-[#F53003] mt-2"></p>
                </div>

                <!-- Payment Info -->
                <div class="bg-[#0a0a0a] border border-[#3E3E3A] rounded-lg p-4">
                    <p class="text-sm font-semibold text-[#EDEDEC] mb-2">Informasi Pembayaran:</p>
                    <p class="text-sm text-[#EDEDEC] mb-1">Bayar ke rekening:</p>
                    <p class="text-base font-bold text-[#F53003] mb-1">BCA 7571230368</p>
                    <p class="text-sm text-[#A1A09A]">a/n Digital Ventura Integrasi, PT</p>
                </div>

                <!-- Upload Bukti Bayar -->
                <form id="checkoutForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="packageType" name="package_type">
                    <input type="hidden" id="packageAmount" name="package_amount">
                    
                    <div>
                        <label class="block text-sm font-medium text-[#EDEDEC] mb-2">
                            Upload Bukti Bayar <span class="text-[#F53003]">*</span>
                        </label>
                        <div class="file-upload-area" id="fileUploadArea">
                            <input type="file" id="paymentProof" name="payment_proof" accept="image/*" class="hidden" required>
                            <div id="uploadText">
                                <svg class="w-12 h-12 mx-auto mb-2 text-[#A1A09A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-sm text-[#EDEDEC] mb-1">Klik atau drag & drop untuk upload</p>
                                <p class="text-xs text-[#A1A09A]">Format: JPG, PNG, atau format image lainnya</p>
                            </div>
                        </div>
                        <div id="filePreview" class="file-preview">
                            <img id="previewImage" src="" alt="Preview">
                            <button type="button" onclick="removeFile()" class="mt-2 text-sm text-[#F53003] hover:text-[#d42800]">
                                Hapus file
                            </button>
                        </div>
                        <p id="fileError" class="text-xs text-[#F53003] mt-2 hidden"></p>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="button" onclick="closeCheckoutModal()" class="flex-1 px-4 py-2 text-sm font-medium text-[#EDEDEC] border border-[#3E3E3A] rounded-lg hover:bg-[#1915014a] transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 text-sm font-semibold text-white bg-[#F53003] rounded-lg hover:bg-[#d42800] transition-colors">
                            Kirim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let selectedPackageType = '';
        let selectedPackageAmount = 0;

        function openCheckoutModal(packageType, amount) {
            selectedPackageType = packageType;
            selectedPackageAmount = amount;
            
            document.getElementById('selectedPackage').textContent = packageType;
            document.getElementById('packagePrice').textContent = 'Rp ' + amount.toLocaleString('id-ID');
            document.getElementById('packageType').value = packageType;
            document.getElementById('packageAmount').value = amount;
            
            document.getElementById('checkoutModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeCheckoutModal(event) {
            if (event && event.target !== event.currentTarget) {
                return;
            }
            document.getElementById('checkoutModal').classList.remove('active');
            document.body.style.overflow = 'auto';
            resetForm();
        }

        function resetForm() {
            document.getElementById('checkoutForm').reset();
            document.getElementById('filePreview').classList.remove('active');
            document.getElementById('fileError').classList.add('hidden');
            document.getElementById('fileError').textContent = '';
        }

        function removeFile() {
            document.getElementById('paymentProof').value = '';
            document.getElementById('filePreview').classList.remove('active');
            document.getElementById('fileError').classList.add('hidden');
        }

        // File upload handling
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('paymentProof');
        const filePreview = document.getElementById('filePreview');
        const previewImage = document.getElementById('previewImage');
        const fileError = document.getElementById('fileError');

        fileUploadArea.addEventListener('click', () => {
            fileInput.click();
        });

        fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            fileUploadArea.classList.add('dragover');
        });

        fileUploadArea.addEventListener('dragleave', () => {
            fileUploadArea.classList.remove('dragover');
        });

        fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            fileUploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFile(files[0]);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleFile(e.target.files[0]);
            }
        });

        function handleFile(file) {
            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                fileError.textContent = 'Format file tidak valid. Hanya file gambar (JPG, PNG, dll) yang diperbolehkan.';
                fileError.classList.remove('hidden');
                fileInput.value = '';
                return;
            }

            // Validate file size (max 5MB)
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                fileError.textContent = 'Ukuran file terlalu besar. Maksimal 5MB.';
                fileError.classList.remove('hidden');
                fileInput.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
                filePreview.classList.add('active');
                fileError.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }

        // Form submission
        document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(e.target);
            const fileInput = document.getElementById('paymentProof');

            if (!fileInput.files || fileInput.files.length === 0) {
                fileError.textContent = 'Silakan upload bukti pembayaran.';
                fileError.classList.remove('hidden');
                return;
            }

            // Show loading state
            const submitButton = e.target.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.textContent = 'Mengirim...';

            try {
                const response = await fetch('{{ route("catalogue.checkout") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    alert('Bukti pembayaran berhasil dikirim! Kami akan memverifikasi pembayaran Anda.');
                    closeCheckoutModal();
                } else {
                    fileError.textContent = data.message || 'Terjadi kesalahan. Silakan coba lagi.';
                    fileError.classList.remove('hidden');
                }
            } catch (error) {
                fileError.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
                fileError.classList.remove('hidden');
            } finally {
                submitButton.disabled = false;
                submitButton.textContent = originalText;
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeCheckoutModal();
            }
        });
    </script>
</body>
</html>

