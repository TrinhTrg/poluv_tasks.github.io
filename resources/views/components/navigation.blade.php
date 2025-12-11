<div class="flex items-center gap-4 md:gap-6">
    <button id="themeToggle" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-slate-700 transition text-gray-600 dark:text-gray-300 focus:outline-none">
        <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>

    <div class="relative cursor-pointer group" onclick="requestNotificationPermission()">
        <svg id="bellIcon" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-gray-600 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        
        <div id="notifBadge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center shadow-sm animate-pulse">0</div>
        
        <div class="absolute right-0 top-10 w-64 bg-white dark:bg-slate-800 p-3 rounded-xl shadow-xl border border-gray-100 dark:border-slate-700 hidden group-hover:block z-50 transform origin-top-right transition-all">
            <div class="text-xs font-bold text-gray-400 uppercase mb-2 border-b dark:border-slate-700 pb-1">Upcoming (24h)</div>
            <ul id="notifList" class="text-sm text-gray-700 dark:text-gray-300 space-y-2 max-h-48 overflow-y-auto">
                <li class="text-gray-400 italic text-center py-2">No upcoming tasks</li>
            </ul>
        </div>
    </div>

    <div class="flex items-center gap-4 border-l pl-6 border-gray-300 dark:border-slate-600">
        <div class="hidden md:block text-right">
            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Welcome back</div>
            <div class="text-sm font-semibold text-gray-800 dark:text-white">
                {{ Auth::check() ? Auth::user()->name : 'Guest User' }}
            </div>
        </div>
        <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-white dark:border-slate-600 shadow-md hover:ring-2 hover:ring-pink-300 transition">
            <img src="https://i.pravatar.cc/150?u={{ Auth::id() ?? 1 }}" 
                 alt="avatar" 
                 class="w-full h-full object-cover grayscale hover:grayscale-0 transition duration-300">
        </div>
    </div>
</div>