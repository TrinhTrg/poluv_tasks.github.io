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
            <h2 class="text-3xl font-serif text-gray-900 dark:text-white font-bold">{{ __('auth.welcome_back') }}</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-2">{{ __('auth.enter_details_signin') }}</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-6">
            @csrf
            
            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.email_address') }}</label>
                <input type="email" name="email" value="{{ old('email') }}" required 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-pink-300 focus:ring focus:ring-pink-200 transition outline-none bg-gray-50 dark:bg-slate-700 dark:text-white">
                @error('email') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.password') }}</label>
                <input type="password" name="password" required 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-pink-300 focus:ring focus:ring-pink-200 transition outline-none bg-gray-50 dark:bg-slate-700 dark:text-white">
            </div>

            {{-- Remember Me & Forgot Password --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="w-4 h-4 text-pink-500 border-gray-300 rounded focus:ring-pink-200">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">{{ __('auth.remember_me') }}</span>
                </label>
                <a href="{{ route('password.forgot') }}" class="text-sm font-medium text-pink-500 dark:text-pink-400 hover:text-pink-600 dark:hover:text-pink-500">{{ __('auth.forgot_password') }}</a>
            </div>

            <button type="submit" class="w-full bg-gray-900 text-white py-3.5 rounded-xl font-bold hover:bg-gray-800 transition shadow-lg transform hover:-translate-y-0.5">
                {{ __('auth.sign_in') }}
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
            {{ __('auth.dont_have_account') }} 
            <a href="{{ route('register') }}" class="font-bold text-pink-500 dark:text-pink-400 hover:text-pink-600 dark:hover:text-pink-500">{{ __('auth.sign_up_free') }}</a>
        </p>
    </div>
</div>
@endsection