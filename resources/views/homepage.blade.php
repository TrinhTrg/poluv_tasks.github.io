@extends('layouts.user-layout')

@section('main-content')
    <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8 mt-4 sm:mt-6 md:mt-8 mb-4">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-serif text-gray-900 dark:text-white leading-tight">
            Hello, <span class="underline decoration-pink-300 decoration-2 underline-offset-4">{{ Auth::check() ? Auth::user()->name : 'Friend' }}</span>,
            <span class="text-gray-500 dark:text-gray-400 font-sans font-light block sm:inline mt-1 sm:mt-0 sm:ml-2 text-xl sm:text-2xl">Ready to focus?</span>
        </h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6 md:gap-8 relative overflow-visible px-3 sm:px-4 md:px-6 lg:px-8 max-w-7xl mx-auto">
        
        <aside class="lg:col-span-3 space-y-4 sm:space-y-6">
            <x-partials.sidebar :tasks="$tasks" :todayTasks="$todayTasks"/>
            
        </aside>

        <section class="lg:col-span-9 space-y-4 sm:space-y-6 md:space-y-8 relative overflow-visible">
            
            <div class="bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm p-3 sm:p-4 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-3 sm:gap-4 border border-white/60 dark:border-slate-700/60 relative z-10 overflow-visible">
                <div class="flex flex-wrap gap-2 overflow-visible relative">
                    <button id="btnToggleView" class="flex items-center gap-1.5 sm:gap-2 bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200 px-2.5 sm:px-3 py-2 rounded-xl text-xs sm:text-sm font-bold hover:bg-indigo-200 transition" onclick="toggleViewMode()">
                        <svg id="iconList" class="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                        <svg id="iconChart" class="w-4 h-4 sm:w-5 sm:h-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>
                        <span class="xs:inline">View</span>
                    </button>
                    
                    <livewire:status-dropdown />
                    
                    <livewire:category-dropdown />
                    
                    <livewire:priority-dropdown />
                </div>

                <div class="flex items-center gap-2 sm:gap-3 w-full md:w-auto">
                    <div class="relative w-full md:w-64 group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-4 w-4 text-gray-400 group-focus-within:text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </span>
                        <input id="searchInput" type="search" placeholder="Search..." class="w-full pl-9 sm:pl-10 pr-3 sm:pr-4 py-2 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-pink-300 focus:ring focus:ring-pink-200 text-sm bg-white dark:bg-slate-700 dark:text-white transition outline-none" />
                    </div>
                    <button id="btnAdd" class="flex-shrink-0 bg-[#6FA774] hover:bg-[#5E9163] text-white px-3 sm:px-4 py-2 rounded-xl text-xl sm:text-2xl font-light shadow-lg hover:shadow-green-200 transition pb-2 sm:pb-3 h-9 sm:h-10 w-9 sm:w-10 flex items-center justify-center">+</button>
                </div>
            </div>

            <div class="relative z-0">
                <x-task.grids :tasks="$tasks" />
            </div>

            <x-dashboard.chart-view />

        </section>
    </div>
    <x-dashboard.summary :tasks="$tasks" />

    @push('scripts')
    <script>
    // --- KHỞI TẠO DỮ LIỆU TỪ LARAVEL ---
    // Chuyển đổi collection PHP sang mảng JavaScript
    let tasks = @json($tasks);

    // Chuẩn hóa dữ liệu (mapping field name từ DB sang JS để code cũ hoạt động)
    tasks = tasks.map(t => ({
        ...t,
        startDate: t.start_date, 
        startTime: t.start_time ? t.start_time.slice(0,5) : '',
        date: t.due_date,
        dueTime: t.due_time ? t.due_time.slice(0,5) : '',
        created: new Date(t.created_at).getTime()
    }));

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
        const options = { method, headers };
        if (body) options.body = JSON.stringify(body);
        
        try {
            const res = await fetch(url, options);
            if (!res.ok) {
                const errorData = await res.json().catch(() => ({}));
                console.error('API Error:', res.status, errorData);
                throw new Error(`API Error: ${res.status}`);
            }
            return await res.json();
        } catch (error) {
            console.error('API Call Error:', error);
            return null;
        }
    }

    // --- CORE VARIABLES ---
    const htmlEl = document.documentElement;
    const themeToggleBtn = document.getElementById('themeToggle');
    const sunIcon = document.getElementById('sunIcon');
    const moonIcon = document.getElementById('moonIcon');
    let editingId = null;
    let deletingId = null; 
    let itemsPerPage = 6;
    let currentPage = 1;
    let currentFilter = 'all';
    let currentSort = 'newest';
    let currentSearch = '';
    let selectedDateOnCalendar = null;
    let currentViewDate = new Date();
    let chartInstance = null;
    let currentCategory = 'all';
    let analyticsRange = 'month';

    const colorOptions = [
        { id: 'colorOption1', value: '#6FA774', bgClass: 'bg-[#6FA774]' },
        { id: 'colorOption2', value: '#EF4444', bgClass: 'bg-[#EF4444]' },
        { id: 'colorOption3', value: '#3B82F6', bgClass: 'bg-[#3B82F6]' },
        { id: 'colorOption4', value: '#F59E0B', bgClass: 'bg-[#F59E0B]' },
        { id: 'colorOption5', value: '#8B5CF6', bgClass: 'bg-[#8B5CF6]' }
    ];
    const DEFAULT_COLOR = colorOptions[0].value;
    const categoryColors = ['#6b5bff', '#f97316', '#facc15', '#22d3ee', '#a855f7', '#34d399', '#f472b6'];
    const categoryMeta = {
        Work: { label: '💼 Work', cardClass: 'bg-green-200 text-green-900', badgeClass: 'bg-green-100 text-green-800' },
        Homework: { label: '📚 Homework', cardClass: 'bg-blue-200 text-blue-900', badgeClass: 'bg-blue-100 text-blue-700' },
        Meeting: { label: '🗣️ Meeting', cardClass: 'bg-red-200 text-red-900', badgeClass: 'bg-red-100 text-red-700' },
        Personal: { label: '👤 Personal', cardClass: 'bg-yellow-200 text-yellow-900', badgeClass: 'bg-yellow-100 text-yellow-800' },
        Other: { label: '📦 Other', cardClass: 'bg-purple-200 text-purple-900', badgeClass: 'bg-purple-100 text-purple-800' }
    };
    const defaultCategoryMeta = { label: '📌 Task', cardClass: 'bg-slate-200 text-slate-900', badgeClass: 'bg-slate-100 text-slate-700' };

    // --- THEME LOGIC ---
    function initTheme() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            htmlEl.classList.add('dark', 'theme-dark');
            htmlEl.classList.remove('theme-light');
            moonIcon.classList.add('hidden'); sunIcon.classList.remove('hidden');
        } else {
            htmlEl.classList.remove('dark', 'theme-dark');
            htmlEl.classList.add('theme-light');
            moonIcon.classList.remove('hidden'); sunIcon.classList.add('hidden');
        }
    }
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
        moonIcon.classList.toggle('hidden', isDark);
        sunIcon.classList.toggle('hidden', !isDark);
        if(!document.getElementById('analyticsView').classList.contains('hidden')) renderChart();
    });

    // --- HELPER FUNCTIONS ---
    // Make toggleViewMode globally accessible early
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
            renderChart();
        }
    }
    window.toggleViewMode = toggleViewMode;
    
    const byPriorityValue = (p) => ({low:1, medium:2, high:3})[p] || 2;
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

    // --- CALENDAR LOGIC ---
    function renderCalendar(){
      const elDate = document.getElementById('calendar');
      const elWeeks = document.getElementById('calendarWeeks');
      const elMonthTitle = document.getElementById('currentMonth');
      
      const year = currentViewDate.getFullYear();
      const month = currentViewDate.getMonth();
      const mNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
      
      if(elMonthTitle) elMonthTitle.innerText = mNames[month] + " " + year;
      
      const today = new Date();
      // Update sidebar headers
      const dayNameEl = document.getElementById('currentDayName');
      const fullDateEl = document.getElementById('currentFullDate');
      if(dayNameEl) dayNameEl.innerText = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][today.getDay()];
      if(fullDateEl) fullDateEl.innerText = `${String(today.getDate()).padStart(2,'0')}, ${mNames[today.getMonth()]} ${today.getFullYear()}`;

      const first = new Date(year, month, 1);
      const last = new Date(year, month+1, 0);
      const startDay = first.getDay(); 
      const daysInMonth = last.getDate();

      let htmlDays = '<div class="grid grid-cols-7 gap-y-2 gap-x-1 text-xs mb-2 border-b border-gray-100 dark:border-slate-700 pb-2">';
      ['Su','Mo','Tu','We','Th','Fr','Sa'].forEach(d => htmlDays += `<div class="text-center font-bold text-gray-400 uppercase tracking-wider">${d}</div>`);
      htmlDays += '</div><div class="grid grid-cols-7 gap-y-2 gap-x-1">';
      
      for (let i=0;i<startDay;i++) htmlDays += `<div></div>`;

      // Tạo Set chứa các ngày có task để đánh dấu chấm đỏ
      const taskStartDates = new Set(tasks.map(t => (t.startDate ? new Date(t.startDate).toISOString().slice(0,10) : null)).filter(Boolean));
      
      for (let d=1; d<=daysInMonth; d++){
        const cellDate = new Date(year, month, d, 12, 0, 0); 
        const iso = cellDate.toISOString().slice(0,10);
        const isToday = cellDate.toDateString() === today.toDateString();
        const hasTaskStart = taskStartDates.has(iso);
        
        let btnClass = "w-8 h-8 rounded-full flex items-center justify-center mx-auto text-sm transition hover:bg-gray-100 dark:hover:bg-slate-700 dark:text-gray-300";
        if (selectedDateOnCalendar === iso) {
            btnClass = "w-8 h-8 rounded-full flex items-center justify-center mx-auto text-sm bg-gray-800 text-white dark:bg-indigo-500 shadow-md";
        } else if (isToday) {
            btnClass = "w-8 h-8 rounded-full flex items-center justify-center mx-auto text-sm bg-pink-200 text-pink-800 font-bold";
        }
        
        htmlDays += `
          <div class="flex flex-col items-center gap-1">
            <button data-iso="${iso}" class="${btnClass}">${d}</button>
            ${hasTaskStart ? '<span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>' : '<span class="w-1.5 h-1.5"></span>'}
          </div>`;
      }
      htmlDays += '</div>';
      if(elDate) elDate.innerHTML = htmlDays;

      // Render weeks logic
      function getISOWeek(d) {
            d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
            d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay()||7));
            var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
            return Math.ceil((((d - yearStart) / 86400000) + 1)/7);
      }
      const numRows = Math.ceil((startDay + daysInMonth) / 7);
      let htmlWeeks = '';
      let currentProcessingDate = new Date(year, month, 1 - startDay);
      for(let r=0; r<numRows; r++) {
          const weekNum = getISOWeek(currentProcessingDate);
          htmlWeeks += `<div class="h-8 w-6 flex items-center justify-center bg-pink-50 dark:bg-slate-700 rounded text-[10px] font-bold text-pink-800 dark:text-pink-300 mb-[0px]">${weekNum}</div>`;
          currentProcessingDate.setDate(currentProcessingDate.getDate() + 7);
      }
      if(elWeeks) elWeeks.innerHTML = htmlWeeks;

      // Event Click Date
      if(elDate) {
        elDate.querySelectorAll('button[data-iso]').forEach(btn => {
            btn.addEventListener('click', () => {
            const val = btn.getAttribute('data-iso');
            selectedDateOnCalendar = (selectedDateOnCalendar === val) ? null : val;
            renderCalendar(); 
            renderTasks();
            });
        });
      }
    }
    function changeMonth(step) {
        currentViewDate.setMonth(currentViewDate.getMonth() + step);
        renderCalendar();
    }
    // Calendar settings modal events
    const dateModal = document.getElementById('dateSelectModal');
    const btnOpenCalendarSettings = document.getElementById('btnOpenCalendarSettings');
    if (btnOpenCalendarSettings) {
        btnOpenCalendarSettings.addEventListener('click', () => {
            document.getElementById('selectMonth').value = currentViewDate.getMonth();
            document.getElementById('inputYear').value = currentViewDate.getFullYear();
            if (dateModal) {
                dateModal.classList.remove('hidden'); 
                dateModal.classList.add('flex');
            }
        });
    }
    const btnCloseDateModal = document.getElementById('btnCloseDateModal');
    if (btnCloseDateModal && dateModal) {
        btnCloseDateModal.addEventListener('click', () => { 
            dateModal.classList.add('hidden'); 
            dateModal.classList.remove('flex'); 
        });
    }
    const btnApplyDate = document.getElementById('btnApplyDate');
    if (btnApplyDate && dateModal) {
        btnApplyDate.addEventListener('click', () => {
            const m = parseInt(document.getElementById('selectMonth').value);
            const y = parseInt(document.getElementById('inputYear').value);
            if (!isNaN(m) && !isNaN(y)) {
                currentViewDate.setFullYear(y); currentViewDate.setMonth(m); renderCalendar();
            }
            dateModal.classList.add('hidden'); dateModal.classList.remove('flex');
        });
    }

    // --- NOTIFICATIONS ---
    function checkNotifications() {
        const now = new Date();
        const next24h = new Date(now.getTime() + 24 * 60 * 60 * 1000);
        const urgentTasks = tasks.filter(t => {
            if(!t.date || t.completed || !t.notify) return false;
            const d = new Date(t.date);
            return d >= now && d <= next24h;
        });
        const badge = document.getElementById('notifBadge');
        const bell = document.getElementById('bellIcon');
        const list = document.getElementById('notifList');
        if (urgentTasks.length > 0) {
            badge.innerText = urgentTasks.length;
            badge.classList.remove('hidden');
            bell.classList.add('text-red-500', 'bell-animate');
            list.innerHTML = urgentTasks.map(t => `<li class="font-medium text-red-600 border-b dark:border-slate-700 pb-1 mb-1 last:border-0">• ${escapeHtml(t.title)}</li>`).join('');
        } else {
            badge.classList.add('hidden');
            bell.classList.remove('text-red-500', 'bell-animate');
            list.innerHTML = '<li class="text-gray-400 italic">No upcoming tasks</li>';
        }
    }
    function requestNotificationPermission() { if ("Notification" in window) Notification.requestPermission(); }

    // --- RENDER TASKS (GRID VIEW) ---
    function getFilteredSortedTasks(){
        let arr = tasks.slice();
        if (selectedDateOnCalendar) arr = arr.filter(t => (t.date ? t.date.slice(0,10) : '') === selectedDateOnCalendar);
        if (currentFilter === 'completed') arr = arr.filter(t => t.completed);
        if (currentFilter === 'pending') arr = arr.filter(t => !t.completed);
        if (currentCategory !== 'all') arr = arr.filter(t => (t.category || '').toLowerCase() === currentCategory.toLowerCase());
        if (currentSearch.trim()){
            const q = currentSearch.trim().toLowerCase();
            arr = arr.filter(t => (t.title||'').toLowerCase().includes(q) || (t.desc||'').toLowerCase().includes(q));
        }
        // Sort logic
        if (currentSort === 'newest') {
            arr.sort((a,b)=> (b.created||0) - (a.created||0));
        } else if (currentSort === 'high') {
            // Sort by priority (high first), then by newest
            arr.sort((a,b)=> {
                const priorityA = byPriorityValue(a.priority);
                const priorityB = byPriorityValue(b.priority);
                if (priorityB !== priorityA) return priorityB - priorityA;
                return (b.created||0) - (a.created||0);
            });
            // Filter to show only high priority tasks
            arr = arr.filter(t => byPriorityValue(t.priority) === 3);
        } else if (currentSort === 'medium') {
            // Sort by priority, then by newest
            arr.sort((a,b)=> {
                const priorityA = byPriorityValue(a.priority);
                const priorityB = byPriorityValue(b.priority);
                if (priorityB !== priorityA) return priorityB - priorityA;
                return (b.created||0) - (a.created||0);
            });
            // Filter to show only medium priority tasks
            arr = arr.filter(t => byPriorityValue(t.priority) === 2);
        } else if (currentSort === 'low') {
            // Sort by priority, then by newest
            arr.sort((a,b)=> {
                const priorityA = byPriorityValue(a.priority);
                const priorityB = byPriorityValue(b.priority);
                if (priorityB !== priorityA) return priorityB - priorityA;
                return (b.created||0) - (a.created||0);
            });
            // Filter to show only low priority tasks
            arr = arr.filter(t => byPriorityValue(t.priority) === 1);
        }
        return arr;
    }

    function renderTasks(){
        const container = document.getElementById('taskList');
        const arr = getFilteredSortedTasks();
        const total = arr.length;
        const end = Math.min(currentPage * itemsPerPage, total);
        const visible = arr.slice(0, end);

        // Render HTML cards
        container.innerHTML = visible.map(t => {
            const isDone = t.completed;
            const category = t.category || 'Other';
            const categoryInfo = categoryMeta[category] || defaultCategoryMeta;
            const timeRangeText = getTimeRange(t);
            const overdueInfo = getFreshOverdueInfo(t);
            const cardClass = overdueInfo?.highlight ? 'bg-red-100 text-red-900 border border-red-200' : `${categoryInfo.cardClass} border border-white/70`;
            const titleClass = isDone ? 'font-bold text-lg truncate w-full line-through opacity-70' : 'font-bold text-lg truncate w-full';
            const descClass = isDone ? 'text-sm opacity-70 truncate' : 'text-sm truncate';
            const timePillClass = overdueInfo?.highlight ? 'bg-red-200/80 text-red-900' : 'bg-white/60 text-gray-700';
            const focusButton = isDone ? '' : `<button type="button" onclick="openPomodoro('${t.id}')" class="text-gray-600 hover:text-gray-900 transition" title="Start Focus"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></button>`;
            const completeButton = `<button onclick="toggleComplete('${t.id}')" class="completeTaskBtn text-gray-600 hover:text-green-600 transition" title="Mark complete"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg></button>`;
            const overdueBadge = overdueInfo?.highlight ? `<span class="text-xs font-semibold text-red-600">Overdue • resets in ${overdueInfo.countdown}</span>` : '';

            return `
            <div class="relative p-5 rounded-3xl smooth-shadow hover:-translate-y-0.5 transition-transform group flex flex-col gap-3 min-h-[160px] ${cardClass}">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex flex-col gap-1 w-full overflow-hidden">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full ${categoryInfo.badgeClass}">${categoryInfo.label}</span>
                        </div>
                        <h4 class="${titleClass}" title="${escapeHtml(t.title)}">${escapeHtml(t.title)}</h4>
                    </div>
                    <div class="flex items-center gap-2">
                        ${completeButton}
                        ${focusButton}
                    </div>
                </div>
                <p class="${descClass}">${escapeHtml(t.desc)}</p>
                <div class="flex justify-between items-center mt-auto">
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1.5 px-3 py-1 rounded-lg ${timePillClass}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="text-xs font-medium tracking-wide">${timeRangeText}</span>
                        </div>
                        ${overdueBadge ? `<span class="text-xs font-semibold text-red-600">${overdueBadge}</span>` : ''}
                    </div>
                    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button data-id="${t.id}" class="editTaskBtn bg-white/50 text-gray-700 p-1.5 rounded-lg hover:bg-white"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></button>
                        <button data-id="${t.id}" onclick="confirmDelete('${t.id}')" class="deleteTaskBtn bg-white/50 text-gray-700 p-1.5 rounded-lg hover:bg-white" title="Delete task"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3H4" /></svg></button>
                    </div>
                </div>
            </div>`;
        }).join('');

        if(visible.length === 0) {
            container.innerHTML = `<div class="col-span-1 md:col-span-2 text-center py-10"><div class="text-6xl mb-4">📝</div><h3 class="text-xl font-serif text-gray-600 dark:text-gray-400">No tasks found</h3><p class="text-sm text-gray-400">Create a new task to get started!</p></div>`;
        }

        // Update Stats
        document.getElementById('statCompleted').innerText = String(tasks.filter(t=>t.completed).length).padStart(2,'0');
        document.getElementById('statPending').innerText = String(tasks.filter(t=>!t.completed).length).padStart(2,'0');
        document.getElementById('statTotal').innerText = String(tasks.length).padStart(3,'0');

        // Re-attach Events
        container.querySelectorAll('.completeTaskBtn').forEach(el => el.addEventListener('click', () => toggleComplete(el.dataset.id)));
        container.querySelectorAll('.editTaskBtn').forEach(b => b.addEventListener('click', () => openEditModal(b.dataset.id)));
        container.querySelectorAll('.deleteTaskBtn').forEach(b => b.addEventListener('click', () => confirmDelete(b.dataset.id)));

        // Load More - chỉ hiển thị khi có > 4 tasks và chưa hiển thị hết
        const loadMoreBtn = document.getElementById('btnLoadMore');
        if (loadMoreBtn) {
            if (total <= 4) {
                loadMoreBtn.style.display = 'none';
            } else {
                loadMoreBtn.style.display = (end >= total) ? 'none' : 'block';
            }
        }
        
        // Update Chart if visible
        if (!document.getElementById('analyticsView').classList.contains('hidden')) renderChart();
        checkNotifications();
        renderTodaySchedule();
    }


    function renderTodaySchedule() {
        const todayStr = new Date().toISOString().slice(0, 10);
        const todayTasks = tasks.filter(t => {
            if (!t.date) return false;
            const taskDate = t.date.slice(0, 10);
            return taskDate === todayStr && !t.completed;
        }).sort((a, b) => (a.startTime || a.dueTime || '').localeCompare(b.startTime || b.dueTime || ''));

        const container = document.getElementById('todayList');
        const dateDisplay = document.getElementById('todayDateDisplay');
        if (!container || !dateDisplay) return;

        const today = new Date();
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        dateDisplay.innerText = `${monthNames[today.getMonth()]} ${today.getDate()}, ${today.getFullYear()}`;

        if (todayTasks.length === 0) {
            container.innerHTML = '<div class="text-center text-gray-400 text-sm py-4 italic">No tasks for today. Relax! ☕</div>';
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

    // --- CHART LOGIC ---
    // --- 7. TOGGLE VIEWS & CHARTS ---
    // Helper function để filter tasks theo time range
    function filterTasksByRange(tasks, range) {
        const now = new Date();
        const start = new Date(now);
        
        if (range === 'day') {
            start.setDate(start.getDate() - 1);
        } else if (range === 'week') {
            start.setDate(start.getDate() - 7);
        } else if (range === 'month') {
            start.setMonth(start.getMonth() - 1);
        } else if (range === 'year') {
            start.setFullYear(start.getFullYear() - 1);
        } else {
            start.setFullYear(start.getFullYear() - 5);
        }
        
        const threshold = start.getTime();
        
        return tasks.filter(t => {
            const ts = t.created || (t.date ? new Date(t.date).getTime() : 0);
            return ts >= threshold;
        });
    }


    function renderChart() {
        const chartCanvas = document.getElementById('taskChart');
        if (!chartCanvas) return;
        
        const ctx = chartCanvas.getContext('2d');

        // Filter tasks by selected time range
        const filtered = filterTasksByRange(tasks, analyticsRange);
        
        // Count completed and pending
        const completedCount = filtered.filter(t => t.completed).length;
        const pendingCount = filtered.filter(t => !t.completed).length;

        // Update stats cards
        const completedEl = document.getElementById('overviewCompleted');
        const pendingEl = document.getElementById('overviewPending');
        const totalOpenEl = document.getElementById('openTasksTotal');

        if (completedEl) completedEl.innerText = completedCount;
        if (pendingEl) pendingEl.innerText = pendingCount;

        // Get open tasks only
        const openTasks = filtered.filter(t => !t.completed);
        const totalOpen = openTasks.length;
        if (totalOpenEl) totalOpenEl.innerText = totalOpen;

        // Count by category
        const categoryCounts = {};
        openTasks.forEach(task => {
            const label = task.category || 'Other';
            categoryCounts[label] = (categoryCounts[label] || 0) + 1;
        });

        let labels = Object.keys(categoryCounts);
        let data = labels.map(label => categoryCounts[label]);
        let colors = labels.map((_, idx) => categoryColors[idx % categoryColors.length]);
        
        const breakdownEl = document.getElementById('categoryBreakdown');

        // Handle empty state
        if (!labels.length) {
            labels = ['No open tasks'];
            data = [1];
            colors = ['#CBD5F5'];
            
            if (breakdownEl) {
                breakdownEl.innerHTML = `
                    <li class="flex items-center justify-between bg-gray-50 dark:bg-slate-700/40 rounded-xl px-4 py-3 border border-gray-100 dark:border-slate-600">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full bg-gray-300"></span>
                            <span class="font-medium">No open tasks</span>
                        </div>
                        <span class="font-semibold">0%</span>
                    </li>
                `;
            }
        } else {
            // Render breakdown list
            if (breakdownEl) {
                breakdownEl.innerHTML = labels.map((label, idx) => {
                    const count = data[idx];
                    const percent = totalOpen ? Math.round((count / totalOpen) * 100) : 0;
                    const categoryInfo = categoryMeta[label] || defaultCategoryMeta;
                    
                    return `
                        <li class="flex items-center justify-between bg-gray-50 dark:bg-slate-700/40 rounded-xl px-4 py-3 border border-gray-100 dark:border-slate-600">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full shadow-sm" style="background:${colors[idx]}"></span>
                                <div class="flex flex-col">
                                    <span class="font-semibold">${categoryInfo.label}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">${count} task${count > 1 ? 's' : ''}</span>
                                </div>
                            </div>
                            <span class="font-semibold">${percent}%</span>
                        </li>
                    `;
                }).join('');
            }
        }

        // Destroy old chart instance
        if (chartInstance) {
            chartInstance.destroy();
        }

        // Check dark mode
        const isDark = htmlEl.classList.contains('dark');

        // Create new chart
        chartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: colors,
                    borderColor: isDark ? '#1e293b' : '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { 
                        display: false 
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: isDark ? '#1f2937' : '#fff',
                        titleColor: isDark ? '#f8fafc' : '#0f172a',
                        bodyColor: isDark ? '#e2e8f0' : '#1f2937',
                        borderColor: isDark ? '#475569' : '#e2e8f0',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const pct = totalOpen ? Math.round((value / totalOpen) * 100) : 0;
                                return ` ${context.label}: ${value} (${pct}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true
                }
            }
        });
    }

    // Event listener for time range filter (handled via Livewire component)

    // --- POMODORO LOGIC ---
    let timerInterval = null;
    let currentPomoSeconds = 25 * 60;
    let isTimerRunning = false;
    let activePomoTaskId = null;

    function openPomodoro(taskId) {
        const t = tasks.find(x => x.id === parseInt(taskId));
        if (!t) return;
        activePomoTaskId = taskId;
        document.getElementById('pomoTaskTitle').innerText = t.title;
        document.getElementById('inputMin').value = 25; document.getElementById('inputSec').value = 0;
        updateTimerDisplay(25, 0);
        document.getElementById('pomodoroModal').classList.remove('hidden'); document.getElementById('pomodoroModal').classList.add('flex');
        isTimerRunning = false; togglePomoControls('stop'); document.getElementById('timerInputs').classList.remove('hidden');
    }
    function togglePomoControls(state) {
        const btnStart = document.getElementById('btnPomoStart');
        const btnPause = document.getElementById('btnPomoPause');
        const inputs = document.getElementById('timerInputs');
        if (state === 'running') {
            btnStart.classList.add('hidden'); btnPause.classList.remove('hidden'); inputs.classList.add('hidden');
        } else {
            btnStart.classList.remove('hidden'); btnPause.classList.add('hidden');
        }
    }
    function updateTimerDisplay(min, sec) { document.getElementById('timerDisplay').innerText = `${String(min).padStart(2,'0')}:${String(sec).padStart(2,'0')}`; }
    function startPomodoro() {
        if (!isTimerRunning) {
            if (currentPomoSeconds === null || currentPomoSeconds === 0) {
                const m = parseInt(document.getElementById('inputMin').value) || 25;
                const s = parseInt(document.getElementById('inputSec').value) || 0;
                currentPomoSeconds = m * 60 + s;
            }
            isTimerRunning = true; togglePomoControls('running');
            timerInterval = setInterval(() => {
                if (currentPomoSeconds <= 0) finishPomodoro();
                else { currentPomoSeconds--; updateTimerDisplay(Math.floor(currentPomoSeconds / 60), currentPomoSeconds % 60); }
            }, 1000);
        }
    }
    function pausePomodoro() { isTimerRunning = false; clearInterval(timerInterval); togglePomoControls('stop'); }
    function cancelPomodoro() { pausePomodoro(); currentPomoSeconds = null; document.getElementById('pomodoroModal').classList.add('hidden'); document.getElementById('pomodoroModal').classList.remove('flex'); }
    function finishPomodoro() {
        pausePomodoro();
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator(); osc.connect(audioCtx.destination); osc.start(); setTimeout(() => osc.stop(), 500);
        if (confirm("Time's up! 🎉\nDo you want to mark this task as Completed?")) toggleComplete(activePomoTaskId);
        cancelPomodoro();
    }
    // Bind Pomodoro Events
    const btnPomoStart = document.getElementById('btnPomoStart');
    if (btnPomoStart) btnPomoStart.addEventListener('click', startPomodoro);
    const btnPomoPause = document.getElementById('btnPomoPause');
    if (btnPomoPause) btnPomoPause.addEventListener('click', pausePomodoro);
    const btnPomoCancel = document.getElementById('btnPomoCancel');
    if (btnPomoCancel) btnPomoCancel.addEventListener('click', cancelPomodoro);

    // --- CRUD & EVENTS ---
    async function toggleComplete(id){
        const t = tasks.find(x => x.id === parseInt(id));
        if(!t) return;

        // Optimistic UI
        t.completed = !t.completed;
        await apiCall(`/tasks/${id}/toggle`, 'POST');
        renderTasks();
    }

    function renderColorOptions(){
        const container = document.getElementById('colorPickerContainer');
        if (!container) return;
        container.innerHTML = colorOptions.map((opt, index) => `
            <div class="relative">
                <input type="radio" name="taskColor" id="${opt.id}" value="${opt.value}" class="sr-only color-radio"${index === 0 ? ' checked' : ''}>
                <label for="${opt.id}" class="block w-8 h-8 rounded-full cursor-pointer border-2 border-white dark:border-slate-600 shadow-sm transition-transform ring-gray-400 ${opt.bgClass}"></label>
            </div>
        `).join('');
    }

    function setSelectedColor(value = DEFAULT_COLOR){
        const radios = document.getElementsByName('taskColor');
        let matched = false;
        for (const radio of radios){
            if (radio.value === value){ radio.checked = true; matched = true; }
        }
        if (!matched && radios.length) radios[0].checked = true;
    }

    // Modal Events
    function openAddModal(){
        editingId = null;
        document.getElementById('modalTitle').innerText = 'New Task';
        document.getElementById('taskTitle').value = '';
        document.getElementById('taskDesc').value = '';
        document.getElementById('taskCategory').value = 'Work';
        document.getElementById('taskStartDate').value = new Date().toISOString().slice(0,10);
        document.getElementById('taskStartTime').value = '';
        document.getElementById('taskDueDate').value = selectedDateOnCalendar || new Date().toISOString().slice(0,10);
        document.getElementById('taskDueTime').value = '';
        document.getElementById('taskPriority').value = 'medium';
        document.getElementById('taskNotify').checked = false;
        setSelectedColor();
        renderColorOptions(); // Đảm bảo color picker được render
        document.getElementById('modalBackdrop').classList.remove('hidden');
        document.getElementById('modalBackdrop').classList.add('flex');
    }

    function openEditModal(id){
        const t = tasks.find(x => x.id === parseInt(id));
        if(!t) return;
        editingId = id;
        document.getElementById('modalTitle').innerText = 'Edit Task';
        document.getElementById('taskTitle').value = t.title;
        document.getElementById('taskDesc').value = t.desc;
        document.getElementById('taskCategory').value = t.category || 'Work';
        document.getElementById('taskStartDate').value = t.startDate ? t.startDate.slice(0,10) : '';
        document.getElementById('taskStartTime').value = t.startTime || '';
        document.getElementById('taskDueDate').value = t.date ? t.date.slice(0,10) : '';
        document.getElementById('taskDueTime').value = t.dueTime || '';
        document.getElementById('taskPriority').value = t.priority;
        document.getElementById('taskNotify').checked = t.notify || false;
        renderColorOptions(); // Đảm bảo color picker được render
        setSelectedColor(t.color || DEFAULT_COLOR);
        document.getElementById('modalBackdrop').classList.remove('hidden');
        document.getElementById('modalBackdrop').classList.add('flex');
    }

    const delModal = document.getElementById('deleteModal');
    function confirmDelete(id){ 
        deletingId = id; 
        delModal.classList.remove('hidden'); 
        delModal.classList.add('flex'); 
    }

    const btnCancelDelete = document.getElementById('btnCancelDelete');
    if (btnCancelDelete && delModal) {
        btnCancelDelete.addEventListener('click', () => { 
            deletingId = null; 
            delModal.classList.add('hidden'); 
            delModal.classList.remove('flex'); 
        });
    }

    const btnConfirmDelete = document.getElementById('btnConfirmDelete');
    if (btnConfirmDelete && delModal) {
        btnConfirmDelete.addEventListener('click', async () => {
        if (deletingId) {
            const id = deletingId;
            
            // Xóa card khỏi DOM
            const button = document.querySelector(`button[onclick="confirmDelete('${id}')"]`);
            if (button) {
                const taskCard = button.closest('.relative.p-4');
                if (taskCard) taskCard.remove();
            }
            
            tasks = tasks.filter(t => t.id !== parseInt(id));
            await apiCall(`/tasks/${id}`, 'DELETE');
            
            // Update stats
            document.getElementById('statTotal').innerText = String(tasks.length).padStart(3,'0');
            document.getElementById('statCompleted').innerText = String(tasks.filter(t=>t.completed).length).padStart(2,'0');
            document.getElementById('statPending').innerText = String(tasks.filter(t=>!t.completed).length).padStart(2,'0');
        }
        deletingId = null; 
        delModal.classList.add('hidden'); 
        delModal.classList.remove('flex');
        });
    }

    // Function to close modal
    function closeModal() {
        const modalBackdrop = document.getElementById('modalBackdrop');
        if (modalBackdrop) {
            modalBackdrop.classList.add('hidden'); 
            modalBackdrop.classList.remove('flex');
        }
        editingId = null;
    }

    const btnAdd = document.getElementById('btnAdd');
    if (btnAdd) btnAdd.addEventListener('click', openAddModal);
    
    // THÊM ĐOẠN NÀY ĐỂ KÍCH HOẠT
    document.addEventListener('DOMContentLoaded', function() {
        const cancelBtn = document.getElementById('cancelModal');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeModal();
            });
        }
    });

    const taskForm = document.getElementById('taskForm');
    if (taskForm) {
        taskForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const title = document.getElementById('taskTitle').value.trim(); 
        if(!title) return;
        
        const payload = {
            title: title,
            description: document.getElementById('taskDesc').value.trim(),
            category: document.getElementById('taskCategory').value,
            start_date: document.getElementById('taskStartDate').value,
            start_time: document.getElementById('taskStartTime').value,
            due_date: document.getElementById('taskDueDate').value,
            due_time: document.getElementById('taskDueTime').value,
            priority: document.getElementById('taskPriority').value,
            notify: document.getElementById('taskNotify').checked,
            color: document.querySelector('input[name="taskColor"]:checked')?.value || DEFAULT_COLOR
        };

        let result;
        if(editingId){
            result = await apiCall(`/tasks/${editingId}`, 'PUT', payload);
        } else {
            result = await apiCall('/tasks', 'POST', payload);
        }
        
        if (result) {
            document.getElementById('modalBackdrop').classList.add('hidden');
            document.getElementById('modalBackdrop').classList.remove('flex');
            
            // Reload để lấy HTML mới từ server
            window.location.reload();
        } else {
            alert('Failed to save task. Please try again.');
        }
        });
    }

    // Control Events
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', e => { 
            currentSearch = e.target.value; 
            currentPage = 1; 
            renderTasks(); 
        });
    }
    // Event listeners for Livewire dropdowns are handled via Livewire.on() below
    const btnLoadMore = document.getElementById('btnLoadMore');
    if (btnLoadMore) btnLoadMore.addEventListener('click', () => { currentPage++; renderTasks(); });

    // Function để xử lý click date từ server-rendered calendar
    function selectDate(iso) {
        selectedDateOnCalendar = (selectedDateOnCalendar === iso) ? null : iso;
        renderCalendar(); 
        renderTasks();
    }

    // Initialize helper functions for Livewire components
    if (typeof window.getCategoryName === 'undefined') {
        window.getCategoryName = function(categoryId) {
            return 'Unknown';
        };
    }

    // listen to category, status, sort changes
    document.addEventListener('livewire:init', () => {
        Livewire.on('category-changed', (category) => {
            currentCategory = category === 'all' ? 'all' : category;
            currentPage = 1;
            renderTasks();
        });

        Livewire.on('status-changed', (status) => {
            currentFilter = status;
            currentPage = 1;
            renderTasks();
        });

        Livewire.on('sort-changed', (sort) => {
            currentSort = sort;
            renderTasks();
        });

        Livewire.on('analytics-range-changed', (range) => {
            analyticsRange = range;
            renderChart();
        });
    });
    // BOOTSTRAP
    (function boot(){
        initTheme(); 
        renderColorOptions();
        // Calendar đã được render từ server, nhưng vẫn cần gọi renderCalendar() 
        // để đảm bảo event listeners được attach đúng và cập nhật nếu có thay đổi
        renderCalendar();
        // Không gọi renderTasks() ở đây để tránh flash content, HTML đã render sẵn từ server.
        // Chỉ gọi để bind events và setup state.
        checkNotifications();
        renderTodaySchedule(); // Vẫn cần gọi để cập nhật nếu có thay đổi
        
        // Re-attach listeners for SSR elements
        document.querySelectorAll('.completeTaskBtn').forEach(el => el.addEventListener('click', () => toggleComplete(el.dataset.id)));
        document.querySelectorAll('.editTaskBtn').forEach(b => b.addEventListener('click', () => openEditModal(b.dataset.id)));
        document.querySelectorAll('.deleteTaskBtn').forEach(b => b.addEventListener('click', () => confirmDelete(b.dataset.id)));
        
        // Close modal when clicking on backdrop
        const modalBackdrop = document.getElementById('modalBackdrop');
        if (modalBackdrop) {
            modalBackdrop.addEventListener('click', (e) => {
                // Only close if clicking directly on backdrop, not on modal content
                if (e.target === modalBackdrop) {
                    closeModal();
                }
            });
        }
        
        if ("Notification" in window && Notification.permission !== "granted" && Notification.permission !== "denied") Notification.requestPermission();
    })();


    </script>
    @endpush
@endsection
