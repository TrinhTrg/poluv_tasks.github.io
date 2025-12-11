<div id="analyticsView" class="hidden fade-in bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-sm flex flex-col gap-6">
    {{-- Task Overview Header --}}
    <div>
        <h3 class="text-xl font-serif font-bold text-gray-800 dark:text-white mb-1 text-left">Task Overview</h3>
        <p class="text-xs text-gray-400 font-sans uppercase tracking-wider mb-4">Summary of recent activity</p>
        
        {{-- Completed & Pending Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Completed Tasks --}}
            <div class="rounded-2xl p-4 bg-gradient-to-br from-purple-100 to-purple-200 dark:from-purple-900/40 dark:to-purple-700/30 shadow-sm border border-white/60 dark:border-purple-600/40 flex flex-col gap-1">
                <span class="text-xs uppercase font-semibold text-purple-900 dark:text-purple-200 tracking-wide">Completed Tasks</span>
                <div class="text-5xl font-serif text-purple-900 dark:text-white leading-none" id="overviewCompleted">0</div>
            </div>

            {{-- Pending Tasks --}}
            <div class="rounded-2xl p-4 bg-gradient-to-br from-rose-100 to-orange-100 dark:from-rose-900/40 dark:to-orange-800/30 shadow-sm border border-white/60 dark:border-rose-700/40 flex flex-col gap-1">
                <span class="text-xs uppercase font-semibold text-rose-900 dark:text-rose-100 tracking-wide">Pending Tasks</span>
                <div class="text-5xl font-serif text-rose-900 dark:text-white leading-none" id="overviewPending">0</div>
            </div>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="flex flex-col gap-4">
        {{-- Chart Header with Filter --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h4 class="text-lg font-serif font-bold text-gray-800 dark:text-white">Open Tasks in Categories</h4>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Visualized distribution</p>
            </div>
            <select id="analyticsRangeSelect" class="self-start sm:self-auto px-4 py-2 rounded-xl border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-sm font-medium text-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-300 transition">
                <option value="day">Day</option>
                <option value="week">Week</option>
                <option value="month" selected>Month</option>
                <option value="year">Year</option>
            </select>
        </div>

        {{-- Chart & Breakdown --}}
        <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
            {{-- Doughnut Chart --}}
            <div class="relative w-48 h-48 flex-shrink-0">
                <canvas id="taskChart"></canvas>
                {{-- Center Text (Total Open) --}}
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span id="openTasksTotal" class="text-4xl font-sans font-extrabold text-gray-800 dark:text-white leading-none drop-shadow-sm">0</span>
                    <span class="text-[11px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest mt-1">Open</span>
                </div>
            </div>

            {{-- Category Breakdown List --}}
            <ul id="categoryBreakdown" class="flex-1 w-full text-sm text-gray-600 dark:text-gray-300 space-y-3">
                <li class="flex items-center justify-between bg-gray-50 dark:bg-slate-700/40 rounded-xl px-4 py-3 border border-gray-100 dark:border-slate-600">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full bg-gray-300"></span>
                        <span class="font-medium">No open tasks</span>
                    </div>
                    <span class="font-semibold">0%</span>
                </li>
            </ul>
        </div>
    </div>
</div>