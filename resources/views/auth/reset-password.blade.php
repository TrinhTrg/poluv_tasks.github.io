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
            <h2 class="text-3xl font-serif text-gray-900 dark:text-white font-bold">Reset Password</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Enter your new password below.</p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
                <p class="text-sm text-green-800 dark:text-green-300">{{ session('success') }}</p>
            </div>
        @endif

        <form action="{{ route('password.reset.post') }}" method="POST" class="space-y-6">
            @csrf
            
            {{-- Email (hidden) --}}
            <input type="hidden" name="email" value="{{ session('reset_password_email') }}">
            
            {{-- New Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.new_password') }}</label>
                <div class="relative">
                    <input type="password" 
                           id="password"
                           name="password" 
                           required 
                           class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-pink-300 focus:ring focus:ring-pink-200 transition outline-none bg-gray-50 dark:bg-slate-700 dark:text-white"
                           placeholder="Enter new password">
                    <button type="button" 
                            onclick="togglePassword('password')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none"
                            aria-label="Toggle password visibility">
                        <svg id="password-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="password-eye-off" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                @error('password') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('auth.confirm_new_password') }}</label>
                <div class="relative">
                    <input type="password" 
                           id="password_confirmation"
                           name="password_confirmation" 
                           required 
                           class="w-full px-4 py-3 pr-12 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-pink-300 focus:ring focus:ring-pink-200 transition outline-none bg-gray-50 dark:bg-slate-700 dark:text-white"
                           placeholder="Confirm new password">
                    <button type="button" 
                            onclick="togglePassword('password_confirmation')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none"
                            aria-label="Toggle password visibility">
                        <svg id="password_confirmation-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg id="password_confirmation-eye-off" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full bg-pink-500 text-white py-3.5 rounded-xl font-bold hover:bg-pink-600 transition shadow-lg transform hover:-translate-y-0.5">
                Reset Password
            </button>
        </form>

        <p class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
            <a href="{{ route('login') }}" class="font-bold text-pink-500 dark:text-pink-400 hover:text-pink-600 dark:hover:text-pink-500">Back to Sign in</a>
        </p>
    </div>
</div>

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eyeIcon = document.getElementById(fieldId + '-eye');
    const eyeOffIcon = document.getElementById(fieldId + '-eye-off');
    
    if (field.type === 'password') {
        field.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeOffIcon.classList.remove('hidden');
    } else {
        field.type = 'password';
        eyeIcon.classList.remove('hidden');
        eyeOffIcon.classList.add('hidden');
    }
}
</script>
@endpush
@endsection

