@php
    use Illuminate\Support\Facades\Auth;
@endphp

<a href="{{ Auth::check() ? route('home') : url('/') }}" class="flex items-center gap-2 sm:gap-3 group">
    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full overflow-hidden shadow-md group-hover:shadow-lg transition-shadow duration-300">
        <img src="{{ asset('poluv_light.png') }}"
             alt="PoLuv Logo Light"
             class="w-full h-full object-cover block dark:hidden"
             loading="{{ request()->routeIs('register') || request()->routeIs('login') || request()->routeIs('password.*') ? 'lazy' : 'eager' }}"
             fetchpriority="{{ request()->routeIs('register') || request()->routeIs('login') || request()->routeIs('password.*') ? 'low' : 'high' }}"
             width="48"
             height="48"
             decoding="async">

        <img src="{{ asset('poluv_dark.png') }}"
             alt="PoLuv Logo Dark"
             class="w-full h-full object-cover hidden dark:block"
             loading="{{ request()->routeIs('register') || request()->routeIs('login') || request()->routeIs('password.*') ? 'lazy' : 'eager' }}"
             fetchpriority="{{ request()->routeIs('register') || request()->routeIs('login') || request()->routeIs('password.*') ? 'low' : 'high' }}"
             width="48"
             height="48"
             decoding="async">
    </div>

    <div>
        <div class="text-xl sm:text-2xl font-serif font-bold italic text-gray-900 dark:text-white tracking-wide group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors">
            PoLuv <span class="font-sans font-light text-xs sm:text-sm not-italic ml-0.5 uppercase tracking-widest text-gray-600 dark:text-gray-400">Tasks</span>
        </div>
    </div>
</a>