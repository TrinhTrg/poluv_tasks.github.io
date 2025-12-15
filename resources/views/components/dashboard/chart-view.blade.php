<div id="analyticsView" class="hidden fade-in bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 shadow-sm flex flex-col gap-4 sm:gap-5 md:gap-6">
    {{-- Task Overview Header --}}
    <div>
        <h3 class="text-lg sm:text-xl font-serif font-bold text-gray-800 dark:text-white mb-1 text-left">Task Overview</h3>
        <p class="text-xs text-gray-400 font-sans uppercase tracking-wider mb-3 sm:mb-4">Summary of recent activity</p>
        
        {{-- Completed & Pending Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
            {{-- Completed Tasks --}}
            <div class="rounded-2xl p-3 sm:p-4 bg-gradient-to-br from-purple-50 via-purple-100 to-purple-200 shadow-sm border border-white/60 flex flex-col gap-1">
                <span class="text-xs uppercase font-semibold text-purple-900 tracking-wide">Completed Tasks</span>
                <div class="text-4xl sm:text-5xl font-serif text-purple-900 leading-none" id="overviewCompleted">0</div>
            </div>

            {{-- Pending Tasks --}}
            <div class="rounded-2xl p-3 sm:p-4 bg-gradient-to-br from-orange-50 via-rose-100 to-orange-100 shadow-sm border border-white/60 flex flex-col gap-1">
                <span class="text-xs uppercase font-semibold text-rose-900 tracking-wide">Pending Tasks</span>
                <div class="text-4xl sm:text-5xl font-serif text-rose-900 leading-none" id="overviewPending">0</div>
            </div>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="flex flex-col gap-3 sm:gap-4">
        {{-- Chart Header with Filter --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 sm:gap-3">
            <div>
                <h4 class="text-base sm:text-lg font-serif font-bold text-gray-800 dark:text-white">Open Tasks in Categories</h4>
                <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Visualized distribution</p>
            </div>
            <livewire:analytics-range-dropdown />
        </div>

        {{-- Chart & Breakdown --}}
        <div class="flex flex-col md:flex-row items-center md:items-start gap-4 sm:gap-6">
            {{-- Doughnut Chart --}}
            <div class="relative w-40 h-40 sm:w-48 sm:h-48 flex-shrink-0">
                <canvas id="taskChart"></canvas>
                {{-- Center Text (Total Open) --}}
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span id="openTasksTotal" class="text-3xl sm:text-4xl font-sans font-extrabold text-gray-800 dark:text-white leading-none drop-shadow-sm">0</span>
                    <span class="text-[10px] sm:text-[11px] text-gray-500 dark:text-gray-400 font-bold uppercase tracking-widest mt-1">Open</span>
                </div>
            </div>

            {{-- Category Breakdown List --}}
            <ul id="categoryBreakdown" class="flex-1 w-full text-xs sm:text-sm text-gray-600 dark:text-gray-300 space-y-2 sm:space-y-3">
                <li class="flex items-center justify-between bg-gray-50 dark:bg-slate-700/40 rounded-xl px-3 sm:px-4 py-2 sm:py-3 border border-gray-100 dark:border-slate-600">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <span class="w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full bg-gray-300"></span>
                        <span class="font-medium">No open tasks</span>
                    </div>
                    <span class="font-semibold">0%</span>
                </li>
            </ul>
        </div>
    </div>
</div>