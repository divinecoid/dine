<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Nomor Telepon - DINE.CO.ID</title>
    
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
                    Verifikasi Nomor Telepon
                </h2>
                <p class="mt-2 text-center text-sm text-[#A1A09A]">
                    Masukkan kode verifikasi yang dikirim ke<br>
                    <span class="font-medium text-[#EDEDEC]">{{ $registration->phone }}</span>
                </p>
            </div>
            
            @if (session('success'))
                <div class="bg-green-500/20 border border-green-500/30 text-green-400 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-[#1D0002] border border-[#F53003]/30 text-[#FF4433] px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-[#161615] border border-[#3E3E3A] rounded-2xl p-8">
                <form class="space-y-6" action="{{ route('registration.verify.otp', $registration) }}" method="POST">
                    @csrf
                    <div>
                        <label for="otp_code" class="block text-sm font-medium text-[#EDEDEC] mb-2 text-center">
                            Kode Verifikasi (6 digit)
                        </label>
                        <input id="otp_code" 
                               name="otp_code" 
                               type="text" 
                               required 
                               maxlength="6"
                               pattern="[0-9]{6}"
                               autocomplete="one-time-code"
                               class="appearance-none relative block w-full px-4 py-4 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] placeholder-[#A1A09A] rounded-lg focus:outline-none focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 sm:text-sm transition-colors text-center text-2xl tracking-widest font-mono"
                               placeholder="000000"
                               autofocus>
                        <p class="mt-2 text-xs text-[#A1A09A] text-center">
                            Kode berlaku selama 10 menit
                        </p>
                    </div>

                    <div>
                        <button type="submit" 
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-[#F53003] hover:bg-[#d42800] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#F53003]/50 focus:ring-offset-[#0a0a0a] transition-colors">
                            Verifikasi
                        </button>
                    </div>

                    <div class="text-center">
                        <form action="{{ route('registration.resend-otp', $registration) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-[#A1A09A] hover:text-[#EDEDEC] transition-colors">
                                Kirim ulang kode verifikasi
                            </button>
                        </form>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus and format OTP input
        document.getElementById('otp_code').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length === 6) {
                this.form.submit();
            }
        });
    </script>
</body>
</html>

