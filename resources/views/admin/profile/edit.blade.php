@extends('layouts.admin')

@section('title', 'Profile Settings - DINE.CO.ID')
@section('page-title', 'Profile Settings')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        @if(session('success'))
            <div class="bg-green-500/20 border border-green-500/50 text-green-400 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-[#161615] border border-[#3E3E3A] rounded-lg p-6">
            <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-[#A1A09A] mb-2">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-[#A1A09A] mb-2">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-2 bg-[#0a0a0a] border border-[#3E3E3A] text-[#EDEDEC] rounded-lg focus:ring-2 focus:ring-[#F53003]/50 focus:border-[#F53003]/50 transition-colors">
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Appearance -->
                <div>
                    <label class="block text-sm font-medium text-[#A1A09A] mb-3">Appearance</label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="appearance" value="dark" {{ old('appearance', $user->appearance) == 'dark' ? 'checked' : '' }}
                                class="text-[#F53003] focus:ring-[#F53003] bg-[#0a0a0a] border-[#3E3E3A]">
                            <span class="text-[#EDEDEC]">Dark Mode</span>
                        </label>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="radio" name="appearance" value="light" {{ old('appearance', $user->appearance) == 'light' ? 'checked' : '' }}
                                class="text-[#F53003] focus:ring-[#F53003] bg-[#0a0a0a] border-[#3E3E3A]">
                            <span class="text-[#EDEDEC]">Light Mode</span>
                        </label>
                    </div>
                    @error('appearance')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role (Read Only) -->
                <div>
                    <label class="block text-sm font-medium text-[#A1A09A] mb-2">Role</label>
                    <input type="text" value="{{ $user->role === 'brand_owner' ? 'Pemilik Brand' : 'Store Manager' }}"
                        disabled
                        class="w-full px-4 py-2 bg-[#161615] border border-[#3E3E3A] text-[#A1A09A] rounded-lg cursor-not-allowed">
                </div>

                <!-- Submit -->
                <div class="pt-4 border-t border-[#3E3E3A]">
                    <button type="submit"
                        class="px-6 py-2 bg-[#F53003] text-white rounded-lg hover:bg-[#d42800] transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection