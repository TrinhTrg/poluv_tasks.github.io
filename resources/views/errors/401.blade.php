<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="light theme-light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>{{ config('app.name', 'PoLuv Tasks') }} - 401</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
</head>
<body class="antialiased">
<div class="min-h-screen flex items-center justify-center bg-[#FAF7F2] dark:bg-slate-900 px-4">
    <div class="text-center max-w-md w-full">
        <div class="mb-8">
            <h1 class="text-9xl font-bold text-gray-300 dark:text-slate-700 mb-4">401</h1>
            <h2 class="text-3xl font-serif font-semibold text-gray-800 dark:text-white mb-3">
                {{ __('Unauthorized') }}
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-8">
                {{ __('Please sign in to access this page.') }}
            </p>
        </div>
        
        <div class="space-y-4">
            <a href="{{ route('login') }}" 
               class="inline-block px-6 py-3 bg-black dark:bg-indigo-600 text-white rounded-xl hover:bg-gray-800 dark:hover:bg-indigo-700 transition font-medium shadow-lg">
                {{ __('Sign In') }}
            </a>
            <div>
                <a href="{{ route('home') }}" 
                   class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition">
                    {{ __('Go to Homepage') }}
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>

