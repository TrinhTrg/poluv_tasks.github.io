@extends('layouts.base-layout')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
{{-- Header with Navigation --}}
<header class="bg-header dark:bg-slate-800 sticky top-0 z-30 transition-all duration-300 shadow-sm border-b border-transparent dark:border-slate-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <x-ui.logo />
            <x-partials.navigation />
        </div>
    </div>
</header>

<main class="bg-main text-gray-800 dark:bg-slate-900 dark:text-gray-100 antialiased font-sans min-h-screen flex flex-col transition-colors duration-300">
    <div class="flex-1 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto relative">
            {{-- Back Button --}}
            <div class="mb-5" style="margin-top: 5px;">
                <a href="{{ route('home') }}" 
                   class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition p-2 rounded-full hover:bg-gray-100 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-pink-300"
                   title="{{ __('profile.go_back') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="text-sm font-medium"></span>
                </a>
            </div>
            
            {{-- Page Header --}}
            <div class="mb-8">
                <h1 class="text-3xl font-serif font-bold text-gray-900 dark:text-white">{{ __('profile.settings') }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('profile.manage_account') }}</p>
            </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-200 px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Panel: Profile Picture --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-slate-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">{{ __('profile.profile_picture') }}</h2>
                    
                    <div class="flex flex-col items-center">
                        {{-- Avatar --}}
                        <div class="relative mb-4">
                            @php
                                $user = Auth::user();
                                if ($user->profile_picture) {
                                    // Kiểm tra xem file có tồn tại không
                                    if (Storage::disk('public')->exists($user->profile_picture)) {
                                        $avatarUrl = asset('storage/' . $user->profile_picture);
                                    } else {
                                        // Nếu file không tồn tại, dùng default avatar
                                        $avatarUrl = "https://i.pravatar.cc/150?u=" . $user->id;
                                    }
                                } else {
                                    $avatarUrl = "https://i.pravatar.cc/150?u=" . $user->id;
                                }
                            @endphp
                            <img src="{{ $avatarUrl }}" 
                                 alt="Profile Picture" 
                                 onerror="this.src='https://i.pravatar.cc/150?u={{ $user->id }}'"
                                 class="w-32 h-32 rounded-full object-cover border-4 border-gray-200 dark:border-slate-600">
                        </div>

                        {{-- File Requirements --}}
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-4">
                            {{ __('profile.jpg_or_png') }}
                        </p>

                        {{-- Upload Button --}}
                        <form id="profilePictureForm" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" 
                                   id="profilePictureInput" 
                                   name="profile_picture" 
                                   accept="image/jpeg,image/png,image/jpg" 
                                   class="hidden"
                                   required>
                            <button type="button" 
                                    id="uploadImageBtn"
                                    class="w-full px-4 py-2.5 bg-pink-500 hover:bg-pink-600 text-white font-semibold rounded-xl transition shadow-md hover:shadow-lg">
                                {{ __('profile.upload_new_image') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right Panel: Account Details --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-slate-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">{{ __('profile.account_details') }}</h2>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <div class="space-y-5">
                            {{-- Username --}}
                            <div>
                                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('profile.username') }}
                                </label>
                                <input type="text" 
                                       id="username" 
                                       name="username" 
                                       value="{{ old('username', Auth::user()->username) }}"
                                       required
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 transition outline-none bg-gray-50 dark:bg-slate-700 dark:text-white">
                                @error('username')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- First Name & Last Name --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        {{ __('profile.first_name') }}
                                    </label>
                                    @php
                                        $nameParts = explode(' ', Auth::user()->name ?? '', 2);
                                        $firstName = old('first_name', $nameParts[0] ?? '');
                                    @endphp
                                    <input type="text" 
                                           id="first_name" 
                                           name="first_name" 
                                           value="{{ $firstName }}"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 transition outline-none bg-gray-50 dark:bg-slate-700 dark:text-white">
                                    @error('first_name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                        {{ __('profile.last_name') }}
                                    </label>
                                    @php
                                        $lastName = old('last_name', $nameParts[1] ?? '');
                                    @endphp
                                    <input type="text" 
                                           id="last_name" 
                                           name="last_name" 
                                           value="{{ $lastName }}"
                                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 transition outline-none bg-gray-50 dark:bg-slate-700 dark:text-white">
                                    @error('last_name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Email Address --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('profile.email_address') }}
                                </label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', Auth::user()->email) }}"
                                       required
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 transition outline-none bg-gray-50 dark:bg-slate-700 dark:text-white">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Phone Number --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('profile.phone_number') }}
                                </label>
                                <input type="tel" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', Auth::user()->phone ?? '') }}"
                                       placeholder="555-123-4567"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 transition outline-none bg-gray-50 dark:bg-slate-700 dark:text-white">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Birthday --}}
                            <div>
                                <label for="birthday" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    {{ __('profile.birthday') }}
                                </label>
                                <input type="date" 
                                       id="birthday" 
                                       name="birthday" 
                                       value="{{ old('birthday', Auth::user()->birthday ?? '') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 transition outline-none bg-gray-50 dark:bg-slate-700 dark:text-white">
                                @error('birthday')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Save Button --}}
                        <div class="mt-8 flex justify-end">
                            <button type="submit" 
                                    class="px-6 py-3 bg-pink-500 hover:bg-pink-600 text-white font-semibold rounded-xl transition shadow-md hover:shadow-lg">
                                {{ __('profile.save_changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

{{-- Footer --}}
<x-partials.footer />
@endsection

@push('scripts')
<script>
    // Theme toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggle = document.getElementById('themeToggle');
        const htmlEl = document.documentElement;
        const moonIcon = document.getElementById('moonIcon');
        const sunIcon = document.getElementById('sunIcon');
        
        // Initialize theme from localStorage
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            htmlEl.classList.add('dark', 'theme-dark');
            htmlEl.classList.remove('theme-light');
            if (moonIcon) moonIcon.classList.add('hidden');
            if (sunIcon) sunIcon.classList.remove('hidden');
        } else {
            htmlEl.classList.remove('dark', 'theme-dark');
            htmlEl.classList.add('theme-light');
            if (moonIcon) moonIcon.classList.remove('hidden');
            if (sunIcon) sunIcon.classList.add('hidden');
        }
        
        // Theme toggle event listener
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                htmlEl.classList.toggle('dark');
                const isDark = htmlEl.classList.contains('dark');
                if (isDark) {
                    htmlEl.classList.add('theme-dark');
                    htmlEl.classList.remove('theme-light');
                } else {
                    htmlEl.classList.add('theme-light');
                    htmlEl.classList.remove('theme-dark');
                }
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                if (moonIcon) moonIcon.classList.toggle('hidden', isDark);
                if (sunIcon) sunIcon.classList.toggle('hidden', !isDark);
            });
        }

        // Profile picture upload functionality
        const uploadImageBtn = document.getElementById('uploadImageBtn');
        const profilePictureInput = document.getElementById('profilePictureInput');
        const profilePictureForm = document.getElementById('profilePictureForm');

        if (uploadImageBtn && profilePictureInput) {
            // Open file picker when button is clicked
            uploadImageBtn.addEventListener('click', function() {
                profilePictureInput.click();
            });

            // Submit form when file is selected
            profilePictureInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    // Show loading state
                    uploadImageBtn.disabled = true;
                    uploadImageBtn.textContent = @json(__('profile.uploading'));
                    
                    // Submit form
                    profilePictureForm.submit();
                }
            });
        }
    });
</script>
@endpush

