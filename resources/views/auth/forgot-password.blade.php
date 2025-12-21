@extends('layouts.base-layout')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#FDFBF7] dark:bg-slate-900 px-4 py-8">
    <div class="max-w-md w-full bg-white dark:bg-slate-800 rounded-3xl shadow-xl p-8 border border-gray-100 dark:border-slate-700 relative">
        {{-- Back Button --}}
        <button 
           type="button"
           onclick="window.history.back();"
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
            <h2 class="text-3xl font-serif text-gray-900 dark:text-white font-bold">{{ __('auth.forgot_password') }}</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-2">{{ __('auth.enter_email_address_verification_code') }}</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
                <p class="text-sm text-green-800 dark:text-green-300">{{ session('success') }}</p>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
            @csrf
            
            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.email_address') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" required 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-pink-300 focus:ring focus:ring-pink-200 transition outline-none bg-gray-50 dark:bg-slate-700 dark:text-white"
                    placeholder="{{ __('auth.enter_your_email') }}">
                @error('email') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full bg-pink-500 text-white py-3.5 rounded-xl font-bold hover:bg-pink-600 transition shadow-lg transform hover:-translate-y-0.5">
                {{ __('auth.send_verification_code') }}
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
            {{ __('auth.remember_your_password') }} 
            <a href="{{ route('login') }}" class="font-bold text-pink-500 dark:text-pink-400 hover:text-pink-600 dark:hover:text-pink-500">{{ __('auth.sign_in') }}</a>
        </p>
    </div>
</div>
@endsection

