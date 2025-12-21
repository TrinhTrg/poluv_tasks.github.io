@extends('layouts.user-layout')

@section('main-content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 mb-4">
        <h1 class="text-3xl md:text-4xl font-serif text-gray-900 dark:text-white leading-tight">
        {{ __('homepage.hello') }}, <span id="userName" class="underline decoration-pink-300 decoration-2 underline-offset-4">{{ Auth::check() ? (Auth::user()->username ?? Auth::user()->name) : __('homepage.guest') }}</span>, 
            <span class="text-gray-500 dark:text-gray-400 font-sans font-light block sm:inline mt-2 sm:mt-0 sm:ml-2 text-2xl sm:text-2xl">{{ __('homepage.ready_to_focus') }}</span>
        </h1>
    </div>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex-grow flex flex-col">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        @auth
        <aside class="lg:col-span-3 space-y-6">
                <x-partials.sidebar :tasks="$tasks" :todayTasks="$todayTasks"/>
        </aside>
        @endauth

        <section class="@auth lg:col-span-9 @else lg:col-span-12 @endauth space-y-4 sm:space-y-6 md:space-y-8 relative overflow-visible">
            
            <div class="bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm p-3 sm:p-4 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4 border border-white/60 dark:border-slate-700/60 relative z-10 overflow-visible">
                @auth
                    <div class="flex flex-wrap gap-2 overflow-visible relative">
                        <button id="btnToggleView" class="flex items-center gap-1.5 sm:gap-2 bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200 px-2.5 sm:px-3 py-2 rounded-xl text-xs sm:text-sm font-bold hover:bg-indigo-200 transition" onclick="toggleViewMode()">
                            <svg id="iconList" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                            <svg id="iconChart" class="w-4 h-4 sm:w-5 sm:h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>
                            <span class="xs:inline">{{ __('homepage.view') }}</span>
                    </button>
                    
                        <livewire:status-dropdown />
                        
                        <livewire:category-dropdown />
                        
                        <livewire:priority-dropdown />
                    </div>
                @else
                    <div class="flex flex-wrap gap-2 overflow-visible relative">
                        <p class="text-sm text-gray-600 dark:text-gray-400 italic">{{ __('homepage.sign_in_to_access') }}</p>
                    </div>
                @endauth

                <div class="flex items-center gap-2 sm:gap-3 w-full md:w-auto">
                    @auth
                        <livewire:search />
                        <button id="btnAdd" class="flex-shrink-0 bg-[#6FA774] hover:bg-[#5E9163] text-white px-3 sm:px-4 py-2 rounded-xl text-xl sm:text-2xl font-light shadow-lg hover:shadow-green-200 transition pb-2 sm:pb-3 h-9 sm:h-10 w-9 sm:w-10 flex items-center justify-center">+</button>
                    @endauth
                </div>
            </div>

            <div class="relative z-0">
                <x-task.grids :tasks="$tasks" />
            </div>

            @auth
                <x-dashboard.chart-view />
            @endauth

        </section>
    </div>
    @auth
        <x-dashboard.summary :tasks="$tasks" />
    @endauth
    </main>

    @push('scripts')
    <script>
        // --- TRANSLATIONS ---
        window.translations = {
            taskReminder: @json(__('notification.task_reminder')),
            taskDueAt: @json(__('notification.task_due_at')),
            noUpcoming: @json(__('notification.no_upcoming')),
            noTasksFound: @json(__('homepage.no_tasks_found')),
            tryAdjustingFilters: @json(__('homepage.try_adjusting_filters')),
            noTasksToday: @json(__('homepage.no_tasks_today')),
            noTasksForDate: @json(__('common.no_tasks_for_date')),
            trySelectingAnotherDate: @json(__('common.try_selecting_another_date'))
        };
        
        // Calendar translations
        window.calendarTranslations = {
            months: [
                @json(__('calendar.january')),
                @json(__('calendar.february')),
                @json(__('calendar.march')),
                @json(__('calendar.april')),
                @json(__('calendar.may')),
                @json(__('calendar.june')),
                @json(__('calendar.july')),
                @json(__('calendar.august')),
                @json(__('calendar.september')),
                @json(__('calendar.october')),
                @json(__('calendar.november')),
                @json(__('calendar.december'))
            ],
            days: [
                @json(__('calendar.sunday')),
                @json(__('calendar.monday')),
                @json(__('calendar.tuesday')),
                @json(__('calendar.wednesday')),
                @json(__('calendar.thursday')),
                @json(__('calendar.friday')),
                @json(__('calendar.saturday'))
            ],
            dayAbbrs: [
                @json(__('calendar.su')),
                @json(__('calendar.mo')),
                @json(__('calendar.tu')),
                @json(__('calendar.we')),
                @json(__('calendar.th')),
                @json(__('calendar.fr')),
                @json(__('calendar.sa'))
            ]
        };
        
        // --- HOMEPAGE CORE SCRIPTS (Global Variables, API, Helpers, Search, Livewire) ---
        window.tasks = @json($tasks);

    // Chuẩn hóa dữ liệu (mapping field name từ DB sang JS để code cũ hoạt động)
        window.tasks = window.tasks.map(t => {
            // Parse start_at datetime để lấy time (fix timezone issue)
            let startTime = '';
            if (t.start_at) {
                // Parse datetime string trực tiếp để tránh timezone conversion
                const startAtStr = t.start_at.replace('T', ' ').replace('Z', '').trim();
                const [datePart, timePart] = startAtStr.split(' ');
                if (datePart && timePart) {
                    const [hours, minutes] = timePart.split(':');
                    startTime = `${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}`;
                } else {
                    // Fallback: parse như datetime object
                    const startDateTime = new Date(t.start_at);
                    if (!isNaN(startDateTime.getTime())) {
                        const hours = String(startDateTime.getHours()).padStart(2, '0');
                        const minutes = String(startDateTime.getMinutes()).padStart(2, '0');
                        startTime = `${hours}:${minutes}`;
                    }
                }
            } else if (t.start_time) {
                startTime = t.start_time.slice(0, 5);
            }
            
            // Parse due_at datetime để lấy time (fix timezone issue)
            let dueTime = '';
            if (t.due_at) {
                // Parse datetime string trực tiếp để tránh timezone conversion
                const dueAtStr = t.due_at.replace('T', ' ').replace('Z', '').trim();
                const [datePart, timePart] = dueAtStr.split(' ');
                if (datePart && timePart) {
                    const [hours, minutes] = timePart.split(':');
                    dueTime = `${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}`;
                } else {
                    // Fallback: parse như datetime object
                    const dueDateTime = new Date(t.due_at);
                    if (!isNaN(dueDateTime.getTime())) {
                        const hours = String(dueDateTime.getHours()).padStart(2, '0');
                        const minutes = String(dueDateTime.getMinutes()).padStart(2, '0');
                        dueTime = `${hours}:${minutes}`;
                    }
                }
            } else if (t.due_time) {
                dueTime = t.due_time.slice(0, 5);
            }
            
            return {
        ...t,
                // Map start_at (database field) to startDate and start_date for compatibility
                start_at: t.start_at || t.start_date,
                startDate: t.start_at || t.start_date, 
                start_date: t.start_at || t.start_date,
                startTime: startTime,
                // Map due_at (database field) to date and due_date for compatibility
                due_at: t.due_at || t.due_date,
                date: t.due_at || t.due_date,
                due_date: t.due_at || t.due_date,
                dueTime: dueTime,
                created: new Date(t.created_at).getTime(),
                // Extract category name and ID from object or use string directly
                category: (t.category && typeof t.category === 'object' && t.category.name) ? t.category.name : (t.category || 'Other'),
                category_id: (t.category && typeof t.category === 'object' && t.category.id) ? t.category.id : (t.category_id || null),
                // Compatibility aliases
                completed: t.is_completed,
                desc: t.description,
                notify: t.has_notify,
                // Ensure priority is a number (1=low, 2=medium, 3=high)
                priority: typeof t.priority === 'number' ? t.priority : (t.priority ? parseInt(t.priority) : 2)
            };
        });

    // --- SETUP FETCH API (CSRF) ---
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    async function apiCall(url, method, body = null) {
            if (!url.startsWith('/api/')) {
                url = '/api/v1' + url;
            }
            
        const headers = {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        };
            const options = { 
                method, 
                headers,
                credentials: 'include', // Quan trọng: Gửi cookies để session auth hoạt động
                sameSite: 'lax'
            };
        if (body) options.body = JSON.stringify(body);
        
        try {
            const res = await fetch(url, options);
                const responseData = await res.json().catch(() => ({}));
                
                if (!res.ok) {
                    const errorMessage = responseData.message || responseData.error || `API Error: ${res.status}`;
                    console.error('API Error:', res.status, responseData);
                    
                    // Log validation errors if present
                    if (responseData.errors) {
                        console.error('Validation errors:', responseData.errors);
                    }
                    
                    throw new Error(errorMessage);
                }
                return responseData;
        } catch (error) {
                console.error('API Call Error:', error);
                throw error; // Re-throw để caller có thể xử lý
            }
        }
        window.apiCall = apiCall;

        // --- CORE VARIABLES (Global) ---
    const htmlEl = document.documentElement;
    const themeToggleBtn = document.getElementById('themeToggle');
    const sunIcon = document.getElementById('sunIcon');
    const moonIcon = document.getElementById('moonIcon');
        window.editingId = null;
        window.deletingId = null; 
    let itemsPerPage = 6;
    let currentPage = 1;
    let currentFilter = 'all';
        let currentSort = 'newest';
    let currentSearch = '';
        window.currentSearch = ''; // Global search variable for compatibility
        window.selectedDateOnCalendar = null;
        window.currentViewDate = new Date();
        window.chartInstance = null;
    let currentCategory = 'all';
        window.analyticsRange = 'month';

        window.categoryColors = ['#6b5bff', '#f97316', '#facc15', '#22d3ee', '#a855f7', '#34d399', '#f472b6'];
        window.categoryMeta = {
        Work: { label: '💼 Work', cardClass: 'bg-green-200 text-green-900', badgeClass: 'bg-green-100 text-green-800' },
        Homework: { label: '📚 Homework', cardClass: 'bg-blue-200 text-blue-900', badgeClass: 'bg-blue-100 text-blue-700' },
        Meeting: { label: '🗣️ Meeting', cardClass: 'bg-red-200 text-red-900', badgeClass: 'bg-red-100 text-red-700' },
        Personal: { label: '👤 Personal', cardClass: 'bg-yellow-200 text-yellow-900', badgeClass: 'bg-yellow-100 text-yellow-800' },
        Other: { label: '📦 Other', cardClass: 'bg-purple-200 text-purple-900', badgeClass: 'bg-purple-100 text-purple-800' }
    };
        window.defaultCategoryMeta = { label: '📌 Task', cardClass: 'bg-slate-200 text-slate-900', badgeClass: 'bg-slate-100 text-slate-700' };

    // --- THEME LOGIC ---
    function initTheme() {
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
        }
        
        if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', () => {
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
                const analyticsView = document.getElementById('analyticsView');
                if (analyticsView && !analyticsView.classList.contains('hidden') && typeof window.renderChart === 'function') {
                    window.renderChart();
                }
    });
        }

    // --- HELPER FUNCTIONS ---
        function toggleViewMode() {
            const list = document.getElementById('listView');
            const analytics = document.getElementById('analyticsView');
            const iconList = document.getElementById('iconList');
            const iconChart = document.getElementById('iconChart');

            if (!list || !analytics || !iconList || !iconChart) return;

            if (list.classList.contains('hidden')) {
                // Switch to List View
                list.classList.remove('hidden');
                analytics.classList.add('hidden');
                iconList.classList.remove('hidden');
                iconChart.classList.add('hidden');
            } else {
                // Switch to Analytics View
                list.classList.add('hidden');
                analytics.classList.remove('hidden');
                iconList.classList.add('hidden');
                iconChart.classList.remove('hidden');
                if (typeof window.renderChart === 'function') window.renderChart();
            }
        }
        window.toggleViewMode = toggleViewMode;
        
        // Convert priority to number: handle both string ('low','medium','high') and number (1,2,3)
        const byPriorityValue = (p) => {
            if (typeof p === 'number') {
                // Already a number (1, 2, 3)
                return p >= 1 && p <= 3 ? p : 2; // Default to medium (2) if invalid
            }
            if (typeof p === 'string') {
                // String value ('low', 'medium', 'high')
                const map = {low: 1, medium: 2, high: 3};
                return map[p.toLowerCase()] || 2;
            }
            // Default to medium (2) if undefined or invalid
            return 2;
        };
    const timeFromISO = (iso) => {
        if (!iso) return '';
        const d = new Date(iso);
        return isNaN(d) ? '' : `${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
    };
    const formatTimeValue = (timeStr) => {
        if (!timeStr) return '';
        const [hh, mm] = timeStr.split(':').map(Number);
        if (Number.isNaN(hh)) return '';
        const suffix = hh >= 12 ? 'PM' : 'AM';
        const hour12 = ((hh + 11) % 12) + 1;
        return `${String(hour12).padStart(2,'0')}:${String(mm).padStart(2,'0')} ${suffix}`;
    };
    const getTimeRange = (task) => {
        const startRaw = task.startTime || timeFromISO(task.startDate);
        const endRaw = task.dueTime || timeFromISO(task.date);
        const start = formatTimeValue(startRaw);
        const end = formatTimeValue(endRaw);
        if (start && end) return `${start} - ${end}`;
        if (start) return `${start}`;
        if (end) return `${end}`;
        return '--';
    };
        window.getTimeRange = getTimeRange;

    const getVietnamTime = (value) => {
        const base = value ? new Date(value) : new Date();
        const localized = base.toLocaleString('en-US', { timeZone: 'Asia/Ho_Chi_Minh' });
        return new Date(localized);
    };
    const formatCountdown = (ms) => {
        if (!ms || ms <= 0) return '00h00m';
        const hours = Math.floor(ms / 3600000);
        const minutes = Math.floor((ms % 3600000) / 60000);
        return `${String(hours).padStart(2,'0')}h${String(minutes).padStart(2,'0')}m`;
    };
    const getFreshOverdueInfo = (task) => {
        if (!task.date) return null;
        const dueVN = getVietnamTime(task.date);
        const nowVN = getVietnamTime();
        if (nowVN <= dueVN) return null;
        const diff = nowVN - dueVN;
        if (diff > 24 * 60 * 60 * 1000) return null;
        const resetPoint = new Date(nowVN);
        resetPoint.setHours(24, 0, 0, 0);
        return { highlight: true, countdown: formatCountdown(resetPoint - nowVN) };
    };
    function escapeHtml(str){ return String(str||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
        window.escapeHtml = escapeHtml;

    // --- NOTIFICATIONS ---
    // Track which tasks have been notified to avoid duplicate notifications
    window.notifiedTaskIds = window.notifiedTaskIds || new Set();
    
    function checkNotifications() {
            if (typeof window.tasks === 'undefined') return;
            
        const now = new Date();
        const next24h = new Date(now.getTime() + 24 * 60 * 60 * 1000);
            const urgentTasks = window.tasks.filter(t => {
                // Use due_at or date field (mapped from due_at)
                const dueDate = t.due_at || t.date || t.due_date;
                if(!dueDate || t.is_completed || t.completed || !(t.has_notify || t.notify)) return false;
            const d = new Date(dueDate);
            return d >= now && d <= next24h;
        });
        const badge = document.getElementById('notifBadge');
        const bell = document.getElementById('bellIcon');
        const list = document.getElementById('notifList');
        if (urgentTasks.length > 0) {
                if (badge) badge.innerText = urgentTasks.length;
                if (badge) badge.classList.remove('hidden');
                if (bell) bell.classList.add('text-red-500', 'bell-animate');
                if (list) {
                    list.innerHTML = urgentTasks.map(t => `
                        <li class="font-medium text-red-600 border-b dark:border-slate-700 pb-1 mb-1 last:border-0 cursor-pointer hover:text-pink-600 dark:hover:text-pink-400 transition-colors" 
                            data-task-id="${t.id}" 
                            onclick="highlightTask(${t.id})">
                            • ${escapeHtml(t.title)}
                        </li>
                    `).join('');
                }
                
                // Send browser notifications for tasks that haven't been notified yet
                if ("Notification" in window && Notification.permission === "granted") {
                    urgentTasks.forEach(task => {
                        const taskId = task.id.toString();
                        // Only send notification if not already notified
                        if (!window.notifiedTaskIds.has(taskId)) {
                            const dueDate = new Date(task.due_at || task.date || task.due_date);
                            const dueTime = dueDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                            
                            const titleText = window.translations.taskReminder.replace(':title', escapeHtml(task.title));
                            const bodyText = window.translations.taskDueAt.replace(':title', escapeHtml(task.title)).replace(':time', dueTime);
                            new Notification(titleText, {
                                body: bodyText,
                                icon: '/favicon-32x32.png', // Use your app icon
                                tag: 'task-' + taskId, // Prevent duplicate notifications
                                requireInteraction: false,
                            });
                            
                            // Mark as notified
                            window.notifiedTaskIds.add(taskId);
                        }
                    });
                }
        } else {
                if (badge) badge.classList.add('hidden');
                if (bell) bell.classList.remove('text-red-500', 'bell-animate');
                if (list) list.innerHTML = '<li class="text-gray-400 italic text-center py-2">' + window.translations.noUpcoming + '</li>';
        }
        }

    // Highlight task card with pink border and scroll to it
    function highlightTask(taskId) {
        // Find task card in the task list (not in notification list)
        const taskList = document.getElementById('taskList');
        if (!taskList) {
            console.warn('Task list not found');
            return;
        }
        
        // Remove previous highlights from task cards only
        taskList.querySelectorAll('[data-task-id]').forEach(card => {
            card.classList.remove('ring-4', 'ring-pink-400', 'ring-offset-2', 'shadow-lg');
        });
        
        // Find the task card in task list
        const taskCard = taskList.querySelector(`[data-task-id="${taskId}"]`);
        if (!taskCard) {
            console.warn('Task card not found for ID:', taskId);
            return;
        }
        
        // Add highlight classes (pink ring)
        taskCard.classList.add('ring-4', 'ring-pink-400', 'ring-offset-2', 'shadow-lg');
        
        // Scroll to task card with offset for header
        const headerOffset = 100;
        const elementPosition = taskCard.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
        
        window.scrollTo({
            top: offsetPosition,
            behavior: 'smooth'
        });
        
        // Remove highlight after 3 seconds
        setTimeout(() => {
            taskCard.classList.remove('ring-4', 'ring-pink-400', 'ring-offset-2', 'shadow-lg');
        }, 3000);
    }
    
    // Make functions globally available
    window.checkNotifications = checkNotifications;
    window.highlightTask = highlightTask;

    function renderTodaySchedule() {
            if (typeof window.tasks === 'undefined') return;
            
        const todayStr = new Date().toISOString().slice(0, 10);
            const todayTasks = window.tasks.filter(t => {
            if (!t.date) return false;
            const taskDate = t.date.slice(0, 10);
                return taskDate === todayStr && !(t.is_completed || t.completed);
        }).sort((a, b) => (a.startTime || a.dueTime || '').localeCompare(b.startTime || b.dueTime || ''));

        const container = document.getElementById('todayList');
        const dateDisplay = document.getElementById('todayDateDisplay');
        if (!container || !dateDisplay) return;

        const today = new Date();
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        dateDisplay.innerText = `${monthNames[today.getMonth()]} ${today.getDate()}, ${today.getFullYear()}`;

        if (todayTasks.length === 0) {
            container.innerHTML = '<div class="text-center text-gray-400 text-sm py-4 italic">' + window.translations.noTasksToday + '</div>';
            return;
        }
        const catColors = { 'Work': 'bg-green-400', 'Homework': 'bg-blue-400', 'Meeting': 'bg-red-400', 'Personal': 'bg-yellow-400', 'Other': 'bg-purple-400' };
        container.innerHTML = todayTasks.map(t => {
            const category = t.category || 'Other';
            const colorClass = catColors[category] || 'bg-gray-400';
            const timeRange = getTimeRange(t);
            return `<div class="flex gap-3 items-center group cursor-pointer" onclick="openEditModal('${t.id}')"><div class="w-1 h-10 rounded-full ${colorClass}"></div><div class="flex-1"><div class="text-sm font-semibold text-gray-800 dark:text-gray-200 line-clamp-1 group-hover:text-pink-500 dark:group-hover:text-pink-400 transition">${escapeHtml(t.title)}</div><div class="text-xs text-gray-400">${timeRange}</div></div></div>`;
        }).join('');
    }

        // --- RENDER TASKS (GRID VIEW) - DEPRECATED: Tasks are now server-rendered ---
        // This function is kept for compatibility but tasks are rendered server-side
        function getFilteredSortedTasks(){
            if (typeof window.tasks === 'undefined') return [];
            
            let arr = window.tasks.slice();
            
            // Optimized: Pre-calculate filter conditions (avoid recalculating in loop)
            const selectedDate = window.selectedDateOnCalendar;
            const isCompletedFilter = currentFilter === 'completed';
            const isPendingFilter = currentFilter === 'pending';
            const searchQuery = (typeof window.currentSearch !== 'undefined' && window.currentSearch.trim()) 
                ? window.currentSearch.trim().toLowerCase() 
                : null;
            const categoryFilter = currentCategory && currentCategory !== 'all' ? currentCategory : null;
            const isCategoryId = categoryFilter && !isNaN(parseInt(categoryFilter));
            const categoryIdFilter = isCategoryId ? parseInt(categoryFilter) : null;
            const priorityFilter = currentSort === 'high' ? 3 : (currentSort === 'medium' ? 2 : (currentSort === 'low' ? 1 : null));
            
            // Single filter loop - combine all filters for better performance
            arr = arr.filter(t => {
                // 1. Filter by Calendar Date
                if (selectedDate) {
                    if (!t.date) return false;
                    const taskDate = t.date.slice(0, 10);
                    if (taskDate !== selectedDate) return false;
                }
                
                // 2. Filter by Status
                const taskCompleted = t.is_completed === true || t.completed === true;
                if (isCompletedFilter && !taskCompleted) return false;
                if (isPendingFilter && taskCompleted) return false;
                
                // 3. Filter by Category
                if (categoryFilter) {
                    if (isCategoryId) {
                        // Filter by category ID
                        const taskCategoryId = t.category_id || (t.category && typeof t.category === 'object' && t.category.id) || null;
                        if (!taskCategoryId || parseInt(taskCategoryId) !== categoryIdFilter) {
                            return false;
                        }
                    } else {
                        // Filter by category name
                        const taskCategory = typeof t.category === 'object' ? t.category.name : t.category;
                        if ((taskCategory || '').toLowerCase() !== categoryFilter.toLowerCase()) {
                            return false;
                        }
                    }
                }
                
                // 4. Filter by Search Query
                if (searchQuery) {
                    const title = (t.title || '').toLowerCase();
                    const desc = (t.description || t.desc || '').toLowerCase();
                    if (!title.includes(searchQuery) && !desc.includes(searchQuery)) {
                        return false;
                    }
                }
                
                // 5. Filter by Priority (if sort is priority-based)
                if (priorityFilter && byPriorityValue(t.priority) !== priorityFilter) {
                    return false;
                }
                
                return true;
            });
            
            // Single sort operation
            if (currentSort === 'newest') {
                // Sort by creation date (newest first)
                arr.sort((a, b) => (b.created || 0) - (a.created || 0));
            } else if (currentSort === 'high' || currentSort === 'medium' || currentSort === 'low') {
                // Sort by priority (high to low), then by creation date (newest first)
                arr.sort((a, b) => {
                    const priorityA = byPriorityValue(a.priority);
                    const priorityB = byPriorityValue(b.priority);
                    if (priorityB !== priorityA) {
                        return priorityB - priorityA; // Higher priority first
                    }
                    return (b.created || 0) - (a.created || 0); // Newest first as tiebreaker
                });
            }
            
            return arr;
        }

        function renderTasks(){
            // Filter tasks client-side vì tasks đã được load vào window.tasks
            if (typeof window.tasks === 'undefined') {
                console.warn('Tasks not loaded yet');
                return;
    }
            
            const taskList = document.getElementById('taskList');
            if (!taskList) return;

            // Get filtered tasks
            const filteredTasks = getFilteredSortedTasks();
            
            // Hide all task cards first
            const taskCards = taskList.querySelectorAll('[data-task-id]');
            taskCards.forEach(card => {
                card.style.display = 'none';
            });
            
            // Show only filtered tasks
            let visibleCount = 0;
            filteredTasks.forEach(task => {
                const card = taskList.querySelector(`[data-task-id="${task.id}"]`);
                if (card) {
                    card.style.display = '';
                    visibleCount++;
    }
            });
            
            // Remove ALL existing empty states (both server-rendered and client-rendered)
            // Match both: empty-state class, empty-state-server class, and elements with col-span that contain empty state text
            const allEmptyStates = taskList.querySelectorAll('.col-span-1.md\\:col-span-2.empty-state, .col-span-1.md\\:col-span-2.empty-state-server, .col-span-1.md\\:col-span-2:not([data-task-id])');
            allEmptyStates.forEach(emptyState => {
                // Check if it's actually an empty state (contains the no tasks message or has h3)
                const text = emptyState.textContent || '';
                if (text.includes(window.translations.noTasksFound) || 
                    text.includes('Không tìm thấy công việc') || 
                    text.includes('No tasks found') ||
                    emptyState.querySelector('h3') ||
                    emptyState.classList.contains('empty-state-server')) {
                    emptyState.remove();
                } 
            });
            
            // Show empty state if no tasks (only if we don' t already have one)
            if (visibleCount === 0) {
                // Check if empty state already exists
                const hasEmptyState = Array.from(taskList.children).some(child => {
                    const text = child.textContent || '';
                    return (text.includes(window.translations.noTasksFound) || 
                            text.includes('Không tìm thấy công việc') || 
                            text.includes('No tasks found')) &&
                           child.classList.contains('col-span-1');
                });
                
                if (!hasEmptyState) {
                    const emptyDiv = document.createElement('div');
                    emptyDiv.className = 'col-span-1 md:col-span-2 text-center py-8 sm:py-10 empty-state';
                    emptyDiv.innerHTML = `
                        <div class="text-5xl sm:text-6xl mb-3 sm:mb-4">📝</div>
                        <h3 class="text-lg sm:text-xl font-serif text-gray-600 dark:text-gray-400">${window.translations.noTasksFound}</h3>
                        <p class="text-xs sm:text-sm text-gray-400">${window.translations.tryAdjustingFilters}</p>
                    `;
                    taskList.appendChild(emptyDiv);
                }
            }
        }
        window.renderTasks = renderTasks;

    // --- RELOAD TASKS DYNAMICALLY (without page reload) ---
    async function reloadTasks() {
        try {
            const taskListContainer = document.getElementById('taskList');
            if (!taskListContainer) {
                window.location.reload();
                return;
            }
            
            // Fetch fresh tasks directly from API (bypass cache)
            try {
                const apiResponse = await window.apiCall('/tasks?per_page=1000', 'GET');
                // Handle both collection (when per_page >= 1000) and paginated response
                const tasksData = Array.isArray(apiResponse) ? apiResponse : (apiResponse.data || []);
                
                // Map tasks using same logic as initial load
                if (tasksData && Array.isArray(tasksData)) {
                    window.tasks = tasksData.map(t => {
                        let startTime = '';
                        if (t.start_at) {
                            const startAtStr = String(t.start_at).replace('T', ' ').replace('Z', '').trim();
                            const [datePart, timePart] = startAtStr.split(' ');
                            if (datePart && timePart) {
                                const [hours, minutes] = timePart.split(':');
                                startTime = `${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}`;
                            }
                        }
                        
                        let dueTime = '';
                        if (t.due_at) {
                            const dueAtStr = String(t.due_at).replace('T', ' ').replace('Z', '').trim();
                            const [datePart, timePart] = dueAtStr.split(' ');
                            if (datePart && timePart) {
                                const [hours, minutes] = timePart.split(':');
                                dueTime = `${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}`;
                            }
                        }
                        
                        return {
                            ...t,
                            start_at: t.start_at || t.start_date,
                            startDate: t.start_at || t.start_date,
                            start_date: t.start_at || t.start_date,
                            startTime: startTime,
                            due_at: t.due_at || t.due_date,
                            date: t.due_at || t.due_date,
                            due_date: t.due_at || t.due_date,
                            dueTime: dueTime,
                            created: new Date(t.created_at).getTime(),
                            category: (t.category && typeof t.category === 'object' && t.category.name) ? t.category.name : (t.category || 'Other'),
                            category_id: (t.category && typeof t.category === 'object' && t.category.id) ? t.category.id : (t.category_id || null),
                            priority: typeof t.priority === 'number' ? t.priority : (t.priority ? parseInt(t.priority) : 2),
                            completed: t.is_completed,
                            desc: t.description,
                            notify: t.has_notify
                        };
                    });
                    
                    // Fetch fresh HTML from server for task cards (server already handles empty state)
                    const htmlResponse = await fetch(window.location.href + '?_t=' + Date.now(), {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html',
                            'Cache-Control': 'no-cache',
                        },
                        credentials: 'include'
                    });
                    
                    if (htmlResponse.ok) {
                        const htmlText = await htmlResponse.text();
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(htmlText, 'text/html');
                        const newTaskList = doc.getElementById('taskList');
                        
                        if (newTaskList) {
                            // Replace task list HTML (server already renders empty state if no tasks)
                            taskListContainer.innerHTML = newTaskList.innerHTML;
                            
                            // Only apply client-side filters if there are tasks
                            // If empty, server already shows empty state, don't call renderTasks to avoid duplicate
                            const hasTasksInHTML = newTaskList.querySelector('[data-task-id]');
                            if (hasTasksInHTML && typeof window.renderTasks === 'function') {
                                window.renderTasks();
                            }
                        }
                    } else {
                        // Fallback: if HTML fetch fails, use renderTasks with updated window.tasks
                        if (typeof window.renderTasks === 'function') {
                            window.renderTasks();
                        }
                    }
                }
            } catch (apiError) {
                console.error('Could not fetch tasks from API:', apiError);
                // Fallback: reload page
                window.location.reload();
            }
            
            // Update stats and re-render
            if (typeof window.updateStats === 'function') {
                window.updateStats();
            }
            if (typeof window.checkNotifications === 'function') {
                window.checkNotifications();
            }
            if (typeof window.renderTodaySchedule === 'function') {
                window.renderTodaySchedule();
            }
            if (typeof window.renderCalendar === 'function') {
                window.renderCalendar();
            }
            if (typeof window.renderTasks === 'function') {
                window.renderTasks();
            }
        } catch (error) {
            console.error('Error reloading tasks:', error);
            // Fallback: reload page if dynamic reload fails
            window.location.reload();
        }
    }
    window.reloadTasks = reloadTasks;

        // Control Events
        document.addEventListener('DOMContentLoaded', function() {
            const btnLoadMore = document.getElementById('btnLoadMore');
            if (btnLoadMore) btnLoadMore.addEventListener('click', () => { 
                currentPage++; 
                renderTasks(); 
            });
    });

        // Initialize helper functions for Livewire components
        if (typeof window.getCategoryName === 'undefined') {
            window.getCategoryName = function(categoryId) {
                return 'Unknown';
            };
        }

        // listen to category, status, sort, search changes
        document.addEventListener('livewire:init', () => {
            Livewire.on('category-changed', (event) => {
                // event có thể là object hoặc string
                const categoryValue = typeof event === 'object' && event.category !== undefined ? event.category : event;
                currentCategory = categoryValue === 'all' ? 'all' : categoryValue;
                currentPage = 1;
                renderTasks();
    });

            Livewire.on('status-changed', (event) => {
                // event có thể là object với property status hoặc là giá trị trực tiếp
                const statusValue = typeof event === 'object' && event.status !== undefined ? event.status : event;
                currentFilter = statusValue;
                currentPage = 1; 
                renderTasks();
            });

            Livewire.on('sort-changed', (event) => {
                // event có thể là object với property sort hoặc là giá trị trực tiếp
                const sortValue = typeof event === 'object' && event.sort !== undefined ? event.sort : event;
                currentSort = sortValue;
        currentPage = 1; 
                renderTasks();
    });

            // Search logic is now handled in Search Livewire component
            // This listener is kept for compatibility but search filtering is done client-side

            Livewire.on('analytics-range-changed', (range) => {
                window.analyticsRange = range;
                if (typeof window.renderChart === 'function') window.renderChart();
            });
        });

    // BOOTSTRAP
    (function boot(){
        initTheme(); 
            
            // Initialize calendar if function exists
            if (typeof window.renderCalendar === 'function') {
                window.renderCalendar();
            }
            
        checkNotifications();
            renderTodaySchedule();
            
            if ("Notification" in window && Notification.permission !== "granted" && Notification.permission !== "denied") {
                Notification.requestPermission();
            }
    })();
    </script>
    @endpush
@endsection
