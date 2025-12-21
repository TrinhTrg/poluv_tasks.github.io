<div class="flex items-center gap-2 sm:gap-3 md:gap-4 lg:gap-6">
    <button id="themeToggle" class="p-1.5 sm:p-2 rounded-full hover:bg-gray-200 dark:hover:bg-slate-700 transition text-gray-600 dark:text-gray-300 focus:outline-none">
        <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>

    <div class="relative cursor-pointer group" id="notificationContainer">
        <button type="button" id="notificationBell" onclick="toggleNotificationPanel()" class="focus:outline-none">
            <svg id="bellIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-7 sm:w-7 text-gray-600 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </button>
        
        <div id="notifBadge" class="hidden absolute -top-0.5 -right-0.5 sm:-top-1 sm:-right-1 bg-red-500 text-white text-[9px] sm:text-[10px] font-bold w-3.5 h-3.5 sm:w-4 sm:h-4 rounded-full flex items-center justify-center shadow-sm animate-pulse z-10">0</div>
        
        <div id="notificationPanel" class="absolute right-0 top-8 sm:top-10 w-56 sm:w-64 bg-white dark:bg-slate-800 p-2 sm:p-3 rounded-xl shadow-xl border border-gray-100 dark:border-slate-700 hidden group-hover:block z-50 transform origin-top-right transition-all">
            <div class="text-[10px] sm:text-xs font-bold text-gray-400 uppercase mb-2 border-b dark:border-slate-700 pb-1">{{ __('notification.upcoming_24h') }}</div>
            <ul id="notifList" class="text-xs sm:text-sm text-gray-700 dark:text-gray-300 space-y-1.5 sm:space-y-2 max-h-40 sm:max-h-48 overflow-y-auto">
                <li class="text-gray-400 italic text-center py-2">{{ __('notification.no_upcoming') }}</li>
            </ul>
        </div>
    </div>

    <div class="flex items-center gap-2 sm:gap-3 md:gap-4 border-l pl-3 sm:pl-4 md:pl-6 border-gray-300 dark:border-slate-600">
        @auth
            {{-- Authenticated User --}}
            <div class="hidden sm:block text-right">
                <div class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('nav.welcome_back') }}</div>
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
                    @php
                        $user = Auth::user();
                        if ($user->profile_picture && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_picture)) {
                            $avatarUrl = asset('storage/' . $user->profile_picture);
                        } else {
                            $avatarUrl = "https://i.pravatar.cc/150?u=" . $user->id;
                        }
                    @endphp
                    <img src="{{ $avatarUrl }}" 
                         alt="avatar" 
                         id="authAvatarImg"
                         onerror="this.src='https://i.pravatar.cc/150?u={{ $user->id }}'"
                         class="w-full h-full object-cover grayscale hover:grayscale-0 transition duration-300">
                </button>
                {{-- Dropdown menu --}}
                <div 
                    id="authDropdownMenu"
                    class="absolute right-0 top-full mt-2 w-48 bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-gray-100 dark:border-slate-700 z-50 hidden"
                >
                    <div class="py-2">
                        <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-slate-700">
                            {{ __('nav.profile') }}
                        </a>
                        <div class="border-t border-gray-100 dark:border-slate-700 mt-1 pt-1">
                            <div 
                                x-data="{ open: false }"
                                @click.away="open = false"
                                class="relative"
                            >
                                <button 
                                    @click="open = !open"
                                    type="button"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-slate-700 cursor-pointer"
                                >
                                    <span>{{ __('nav.language') }}</span>
                                </button>
                                <div 
                                    x-show="open"
                                    x-transition
                                    class="absolute right-full top-0 mr-2 w-40 bg-white dark:bg-slate-800 rounded-lg shadow-xl border border-gray-100 dark:border-slate-700 z-50"
                                    style="display: none;"
                                >
                                    <form action="{{ route('language.switch') }}" method="POST" class="py-1">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            name="locale" 
                                            value="en"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-slate-700 flex items-center gap-2"
                                        >
                                            <span class="text-base">🇬🇧</span>
                                            <span>English</span>
                                            @if(app()->getLocale() === 'en')
                                                <svg class="w-4 h-4 ml-auto text-pink-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </button>
                                        <button 
                                            type="submit" 
                                            name="locale" 
                                            value="vi"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-slate-700 flex items-center gap-2"
                                        >
                                            <span class="text-base">🇻🇳</span>
                                            <span>Tiếng Việt</span>
                                            @if(app()->getLocale() === 'vi')
                                                <svg class="w-4 h-4 ml-auto text-pink-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="border-t border-gray-100 dark:border-slate-700 mt-1">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                {{ __('nav.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            {{-- Guest User --}}
            <div class="hidden sm:block text-right">
                <div class="text-[10px] sm:text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('nav.welcome') }}</div>
                <div class="text-xs sm:text-sm font-semibold text-gray-800 dark:text-white">
                    {{ __('nav.guest') }}
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
                            {{ __('nav.sign_in') }}
                        </a>
                        <a href="{{ route('register') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-slate-700 transition">
                            {{ __('nav.sign_up') }}
                        </a>
                        <div class="border-t border-gray-100 dark:border-slate-700 mt-1 pt-1">
                            <div 
                                x-data="{ open: false }"
                                @click.away="open = false"
                                class="relative"
                            >
                                <button 
                                    @click="open = !open"
                                    type="button"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-slate-700 cursor-pointer transition"
                                >
                                    <span>{{ __('nav.language') }}</span>
                                </button>
                                <div 
                                    x-show="open"
                                    x-transition
                                    class="absolute right-full top-0 mr-2 w-40 bg-white dark:bg-slate-800 rounded-lg shadow-xl border border-gray-100 dark:border-slate-700 z-50"
                                    style="display: none;"
                                >
                                    <form action="{{ route('language.switch') }}" method="POST" class="py-1">
                                        @csrf
                                        <button 
                                            type="submit" 
                                            name="locale" 
                                            value="en"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-slate-700 flex items-center gap-2"
                                        >
                                            <span class="text-base">🇬🇧</span>
                                            <span>English</span>
                                            @if(app()->getLocale() === 'en')
                                                <svg class="w-4 h-4 ml-auto text-pink-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </button>
                                        <button 
                                            type="submit" 
                                            name="locale" 
                                            value="vi"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-pink-50 dark:hover:bg-slate-700 flex items-center gap-2"
                                        >
                                            <span class="text-base">🇻🇳</span>
                                            <span>Tiếng Việt</span>
                                            @if(app()->getLocale() === 'vi')
                                                <svg class="w-4 h-4 ml-auto text-pink-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                </div>
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
    // Notification panel toggle state
    let notificationPanelPinned = false;

    // Toggle notification panel on click
    function toggleNotificationPanel() {
        const panel = document.getElementById('notificationPanel');
        if (!panel) return;
        
        notificationPanelPinned = !notificationPanelPinned;
        
        if (notificationPanelPinned) {
            panel.classList.remove('hidden');
            panel.classList.add('block');
            // Request notification permission
            if ("Notification" in window && Notification.permission !== "granted" && Notification.permission !== "denied") {
                Notification.requestPermission();
            }
        } else {
            panel.classList.add('hidden');
            panel.classList.remove('block');
        }
    }

    // Avatar dropdown functionality (both guest and authenticated)
    document.addEventListener('DOMContentLoaded', function() {
        // Notification panel hover behavior
        const notificationContainer = document.getElementById('notificationContainer');
        const notificationPanel = document.getElementById('notificationPanel');
        
        if (notificationContainer && notificationPanel) {
            // Show on hover (temporary)
            notificationContainer.addEventListener('mouseenter', function() {
                if (!notificationPanelPinned) {
                    notificationPanel.classList.remove('hidden');
                    notificationPanel.classList.add('block');
                }
            });
            
            // Hide on mouse leave if not pinned
            notificationContainer.addEventListener('mouseleave', function() {
                if (!notificationPanelPinned) {
                    notificationPanel.classList.add('hidden');
                    notificationPanel.classList.remove('block');
                }
            });
            
            // Close panel when clicking outside (only if pinned)
            // But don't close if clicking on task items inside the panel
            document.addEventListener('click', function(e) {
                if (notificationPanelPinned && 
                    !notificationContainer.contains(e.target) && 
                    !notificationPanel.contains(e.target)) {
                    notificationPanelPinned = false;
                    notificationPanel.classList.add('hidden');
                    notificationPanel.classList.remove('block');
                }
            });
            
            // Prevent panel from closing when clicking on task items
            if (notificationPanel) {
                notificationPanel.addEventListener('click', function(e) {
                    // If clicking on a task item, don't close the panel
                    if (e.target.closest('[data-task-id]')) {
                        e.stopPropagation();
                    }
                });
            }
        }
        
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

    // Make toggleNotificationPanel globally available
    window.toggleNotificationPanel = toggleNotificationPanel;
</script>
@endpush