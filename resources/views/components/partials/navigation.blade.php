<div class="flex items-center gap-2 sm:gap-3 md:gap-4 lg:gap-6">
    <button id="themeToggle" class="p-1.5 sm:p-2 rounded-full hover:bg-gray-200 dark:hover:bg-slate-700 transition text-gray-600 dark:text-gray-300 focus:outline-none">
        <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>

    <div class="relative cursor-pointer group" onclick="requestNotificationPermission()">
        <svg id="bellIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-7 sm:w-7 text-gray-600 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        
        <div id="notifBadge" class="hidden absolute -top-0.5 -right-0.5 sm:-top-1 sm:-right-1 bg-red-500 text-white text-[9px] sm:text-[10px] font-bold w-3.5 h-3.5 sm:w-4 sm:h-4 rounded-full flex items-center justify-center shadow-sm animate-pulse">0</div>
        
        <div class="absolute right-0 top-8 sm:top-10 w-56 sm:w-64 bg-white dark:bg-slate-800 p-2 sm:p-3 rounded-xl shadow-xl border border-gray-100 dark:border-slate-700 hidden group-hover:block z-50 transform origin-top-right transition-all">
            <div class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase mb-2 border-b dark:border-slate-700 pb-1">Upcoming (24h)</div>
            <ul id="notifList" class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 space-y-1.5 sm:space-y-2 max-h-40 sm:max-h-48 overflow-y-auto">
                <li class="text-gray-400 italic text-center py-2">No upcoming tasks</li>
            </ul>
        </div>
    </div>

    <div class="flex items-center gap-2 sm:gap-3 md:gap-4 border-l pl-3 sm:pl-4 md:pl-6 border-gray-300 dark:border-slate-600">
        @auth
            {{-- Authenticated User --}}
            <div class="hidden sm:block text-right">
                <div class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Welcome back</div>
                <div class="text-xs sm:text-sm font-semibold text-gray-800 dark:text-white">
                    {{ Auth::user()->username ?? Auth::user()->name }}
                </div>
            </div>
            <div class="relative" id="authAvatarDropdown">
                <button 
                    type="button"
                    id="authAvatarBtn"
                    class="w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 rounded-full overflow-hidden border-2 border-white dark:border-slate-600 shadow-md hover:ring-2 hover:ring-pink-300 transition cursor-pointer focus:outline-none focus:ring-2 focus:ring-pink-300"
                >
                    <img src="https://i.pravatar.cc/150?u={{ Auth::id() }}" 
                         alt="avatar" 
                         id="authAvatarImg"
                         class="w-full h-full object-cover grayscale hover:grayscale-0 transition duration-300">
                </button>
                {{-- Dropdown menu --}}
                <div 
                    id="authDropdownMenu"
                    class="absolute right-0 top-full mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-gray-100 dark:border-slate-700 z-50 hidden"
                >
                    <div class="py-2">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-slate-700">
                            Profile
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-slate-700">
                            Settings
                        </a>
                        <div class="border-t border-gray-100 dark:border-slate-700 mt-1 pt-1">
                            <div class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-slate-700 cursor-pointer">
                                Language
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="border-t border-gray-100 dark:border-slate-700 mt-1">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            {{-- Guest User --}}
            <div class="hidden sm:block text-right">
                <div class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Welcome</div>
                <div class="text-xs sm:text-sm font-semibold text-gray-800 dark:text-white">
                    Guest!
                </div>
            </div>
            <div class="relative" id="guestAvatarDropdown">
                <button 
                    type="button"
                    id="guestAvatarBtn"
                    class="w-8 h-8 sm:w-9 sm:h-9 md:w-10 md:h-10 rounded-full bg-gray-200 dark:bg-slate-700 flex items-center justify-center border-2 border-white dark:border-slate-600 shadow-md hover:ring-2 hover:ring-pink-300 transition cursor-pointer focus:outline-none focus:ring-2 focus:ring-pink-300"
                >
                    {{-- Generic guest avatar icon --}}
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-200" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-3.33 0-6 2.24-6 5v1h12v-1c0-2.76-2.67-5-6-5z"/>
                    </svg>
                </button>
                {{-- Dropdown menu --}}
                <div 
                    id="guestDropdownMenu"
                    class="absolute right-0 top-full mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-gray-100 dark:border-slate-700 z-50 hidden"
                >
                    <div class="py-2">
                        <a href="{{ route('login') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-slate-700 transition">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-slate-700 transition">
                            Sign Up
                        </a>
                        <div class="border-t border-gray-100 dark:border-slate-700 mt-1 pt-1">
                            <div class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-slate-700 cursor-pointer transition">
                                Language
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endauth
    </div>
    
</div>

@push('scripts')
<script>
    // Avatar dropdown functionality (both guest and authenticated)
    document.addEventListener('DOMContentLoaded', function() {
        // Guest avatar dropdown
        const guestAvatarBtn = document.getElementById('guestAvatarBtn');
        const guestDropdownMenu = document.getElementById('guestDropdownMenu');
        
        if (guestAvatarBtn && guestDropdownMenu) {
            // Toggle dropdown on click
            guestAvatarBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                guestDropdownMenu.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!guestAvatarBtn.contains(e.target) && !guestDropdownMenu.contains(e.target)) {
                    guestDropdownMenu.classList.add('hidden');
                }
            });
        }
        
        // Authenticated user avatar dropdown
        const authAvatarBtn = document.getElementById('authAvatarBtn');
        const authDropdownMenu = document.getElementById('authDropdownMenu');
        
        if (authAvatarBtn && authDropdownMenu) {
            // Toggle dropdown on click
            authAvatarBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                authDropdownMenu.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!authAvatarBtn.contains(e.target) && !authDropdownMenu.contains(e.target)) {
                    authDropdownMenu.classList.add('hidden');
                }
            });
        }
    });
</script>
@endpush