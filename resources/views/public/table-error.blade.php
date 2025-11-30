@extends('layouts.public')

@section('title', 'QR Code Tidak Valid')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-6 text-center">
        <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-4">
            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-900 mb-2">QR Code Tidak Valid</h1>
        <p class="text-sm text-gray-600 mb-6">
            {{ $error ?? 'QR code meja tidak ditemukan atau tidak valid.' }}
        </p>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-700 mb-2">
                <strong>Solusi:</strong>
            </p>
            <ul class="text-sm text-gray-600 text-left space-y-2">
                <li class="flex items-start">
                    <span class="mr-2">•</span>
                    <span>Pastikan Anda scan QR code yang ada di meja restoran</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">•</span>
                    <span>Periksa kembali QR code yang Anda scan</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">•</span>
                    <span>Hubungi staff restoran jika masalah berlanjut</span>
                </li>
            </ul>
        </div>

        @if(isset($brandSlug))
        <a href="/{{ $brandSlug }}" 
           class="inline-block px-6 py-3 bg-gray-900 text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors">
            Coba Lagi
        </a>
        @endif
    </div>
</div>
@endsection

