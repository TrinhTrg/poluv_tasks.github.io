@extends('layouts.base-layout')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#FDFBF7] dark:bg-slate-900 px-4 py-8">
    <div class="max-w-md w-full bg-white dark:bg-slate-800 rounded-3xl shadow-xl p-8 border border-gray-100 dark:border-slate-700 relative">
        {{-- Back Button --}}
        <button 
           type="button"
           onclick="window.location.href='{{ url('/') }}';"
           class="absolute top-4 left-4 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-slate-700 transition text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-pink-300"
           title="{{ __('profile.go_back') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </button>
        <div class="flex items-center justify-center text-center mb-8">
            <x-ui.logo/>
        </div>
        <div class="text-center mb-8">
            <h2 class="text-3xl font-serif text-gray-900 dark:text-white font-bold">{{ __('auth.create_account') }}</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-2">{{ __('auth.start_productivity') }}</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-5">
            @csrf
            
            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.full_name') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" required 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-pink-300 focus:ring focus:ring-pink-200 transition outline-none bg-gray-50 dark:bg-slate-700 dark:text-white">
                @error('name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Username --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('profile.username') }}</label>
                <input type="text" name="username" value="{{ old('username') }}" required 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-pink-300 focus:ring focus:ring-pink-200 transition outline-none bg-gray-50 dark:bg-slate-700 dark:text-white"
                    placeholder="{{ __('auth.username_placeholder') }}">
                @error('username') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.email_address') }}</label>
                <input type="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       autocomplete="email"
                       required 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-pink-300 focus:ring focus:ring-pink-200 transition outline-none bg-gray-50 dark:bg-slate-700 dark:text-white">
                @error('email') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.password') }}</label>
                <input type="password" 
                       name="password" 
                       autocomplete="new-password"
                       required 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-pink-300 focus:ring focus:ring-pink-200 transition outline-none bg-gray-50 dark:bg-slate-700 dark:text-white">
                @error('password') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.confirm_password') }}</label>
                <input type="password" 
                       name="password_confirmation" 
                       autocomplete="new-password"
                       required 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-pink-300 focus:ring focus:ring-pink-200 transition outline-none bg-gray-50 dark:bg-slate-700 dark:text-white">
            </div>

            <button type="submit" class="w-full bg-[#6FA774] text-white py-3.5 rounded-xl font-bold hover:bg-[#5E9163] transition shadow-lg transform hover:-translate-y-0.5">
                {{ __('auth.sign_up') }}
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
            {{ __('auth.already_have_account') }} 
            <a href="{{ route('login') }}" class="font-bold text-pink-500 dark:text-pink-400 hover:text-pink-600 dark:hover:text-pink-500">{{ __('auth.sign_in') }}</a>
        </p>
    </div>
</div>
@endsection