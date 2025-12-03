<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pembayaran Registrasi - DINE.CO.ID</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body {
            background-color: #0a0a0a !important;
            color: #EDEDEC !important;
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
        }
        
        [class*="bg-[#0a0a0a]"] { background-color: #0a0a0a !important; }
        [class*="bg-[#161615]"] { background-color: #161615 !important; }
        [class*="text-[#EDEDEC]"] { color: #EDEDEC !important; }
        [class*="text-[#A1A09A]"] { color: #A1A09A !important; }
        [class*="border-[#3E3E3A]"] { border-color: #3E3E3A !important; }
    </style>
</head>
<body class="bg-[#0a0a0a] text-[#EDEDEC]">
    <div class="min-h-screen w-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="flex items-center justify-center gap-2 mb-2">
                    <h1 class="text-3xl font-bold text-[#F53003]">DINE</h1>
                    <span class="text-sm text-[#A1A09A]">.CO.ID</span>
                </div>
                <h2 class="text-center text-xl font-semibold text-[#EDEDEC]">
                    Pembayaran Registrasi
                </h2>
                <p class="mt-2 text-center text-sm text-[#A1A09A]">
                    Paket {{ $registration->account_type }}
                </p>
            </div>

            <div class="bg-[#161615] border border-[#3E3E3A] rounded-2xl p-8">
                <div class="space-y-6">
                    <!-- Payment Info -->
                    <div class="bg-[#0a0a0a] border border-[#3E3E3A] rounded-lg px-4 py-3">
                        <div class="text-center">
                            <p class="text-sm text-[#A1A09A] mb-1">Total Pembayaran</p>
                            <p class="text-2xl font-bold text-[#EDEDEC]">Rp {{ number_format($registration->payment_amount, 0, ',', '.') }}</p>
                            <p class="text-xs text-[#A1A09A] mt-2">Paket: {{ $registration->account_type }}</p>
                        </div>
                    </div>

                    <!-- Payment Method Tabs -->
                    <div class="flex gap-2 mb-4">
                        <button type="button" 
                                onclick="showPaymentMethod('qris')"
                                id="qrisTab"
                                class="flex-1 px-4 py-2 bg-[#F53003] text-white font-semibold rounded-lg">
                            QRIS
                        </button>
                        <button type="button" 
                                onclick="showPaymentMethod('va')"
                                id="vaTab"
                                class="flex-1 px-4 py-2 bg-[#3E3E3A] text-[#EDEDEC] font-semibold rounded-lg hover:bg-[#4a4a46]">
                            Virtual Account
                        </button>
                    </div>

                    <!-- QRIS Display -->
                    <div id="qrisSection" class="space-y-4">
                        <div class="bg-white rounded-lg p-6 flex flex-col items-center">
                            <div class="mb-4 flex items-center justify-center min-h-[256px]">
                                <img id="qrisImage" src="" alt="QRIS" class="w-64 h-64 mx-auto border-2 border-gray-200 rounded-lg object-contain">
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-semibold text-gray-700 mb-1">Scan QRIS dengan aplikasi e-wallet Anda</p>
                                <p class="text-xs text-gray-500">Gunakan aplikasi seperti GoPay, OVO, DANA, LinkAja</p>
                            </div>
                        </div>
                    </div>

                    <!-- Virtual Account Display -->
                    <div id="vaSection" class="space-y-4 hidden">
                        <div class="bg-[#0a0a0a] border border-[#3E3E3A] rounded-lg p-6">
                            <div class="text-center">
                                <p class="text-sm text-[#A1A09A] mb-2">Nomor Virtual Account</p>
                                <p id="virtualAccount" class="text-2xl font-bold text-[#EDEDEC] font-mono mb-4">-</p>
                                <p class="text-xs text-[#A1A09A] mb-4">Transfer ke nomor VA di atas melalui aplikasi bank Anda</p>
                                <button type="button" 
                                        onclick="copyVirtualAccount()"
                                        class="w-full px-4 py-2 bg-[#3E3E3A] text-[#EDEDEC] font-semibold rounded-lg hover:bg-[#4a4a46] transition-colors">
                                    Salin Nomor VA
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-3">
                        <button type="button" 
                                onclick="checkPaymentStatus()"
                                class="w-full px-4 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Cek Status Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const registrationId = @json($registration->id);
        let qrisUrl = null;
        let virtualAccount = null;
        let currentPaymentMethod = 'qris';

        async function loadPayment() {
            try {
                const response = await fetch(`/registration/payment/${registrationId}/generate`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    qrisUrl = data.data.qris_url;
                    virtualAccount = data.data.virtual_account;
                    
                    // Set QRIS image
                    document.getElementById('qrisImage').src = qrisUrl;
                    
                    // Set Virtual Account
                    document.getElementById('virtualAccount').textContent = virtualAccount;
                }
            } catch (error) {
                console.error('Error loading payment:', error);
            }
        }

        function showPaymentMethod(method) {
            currentPaymentMethod = method;
            
            if (method === 'qris') {
                document.getElementById('qrisSection').classList.remove('hidden');
                document.getElementById('vaSection').classList.add('hidden');
                document.getElementById('qrisTab').classList.remove('bg-[#3E3E3A]');
                document.getElementById('qrisTab').classList.add('bg-[#F53003]');
                document.getElementById('vaTab').classList.remove('bg-[#F53003]');
                document.getElementById('vaTab').classList.add('bg-[#3E3E3A]');
            } else {
                document.getElementById('qrisSection').classList.add('hidden');
                document.getElementById('vaSection').classList.remove('hidden');
                document.getElementById('vaTab').classList.remove('bg-[#3E3E3A]');
                document.getElementById('vaTab').classList.add('bg-[#F53003]');
                document.getElementById('qrisTab').classList.remove('bg-[#F53003]');
                document.getElementById('qrisTab').classList.add('bg-[#3E3E3A]');
            }
        }

        function copyVirtualAccount() {
            if (virtualAccount) {
                navigator.clipboard.writeText(virtualAccount).then(() => {
                    alert('Nomor Virtual Account berhasil disalin!');
                });
            }
        }

        async function checkPaymentStatus() {
            try {
                const response = await fetch(`/registration/payment/${registrationId}/status`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await response.json();
                
                if (data.success && data.data.is_paid) {
                    // Complete registration via POST
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/registration/complete/${registrationId}`;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    form.submit();
                } else {
                    alert('Pembayaran belum dikonfirmasi. Silakan coba lagi dalam beberapa saat.');
                }
            } catch (error) {
                console.error('Error checking payment:', error);
                alert('Terjadi kesalahan saat mengecek status pembayaran.');
            }
        }

        // Load payment on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadPayment();
        });
    </script>
</body>
</html>

