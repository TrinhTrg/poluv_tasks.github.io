<div id="analyticsView" class="hidden fade-in bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 shadow-sm flex flex-col gap-4 sm:gap-5 md:gap-6">
    {{-- Task Overview Header --}}
    <div>
        <h3 class="text-lg sm:text-xl font-serif font-bold text-gray-800 dark:text-white mb-1 text-left">Task Overview</h3>
        <p class="text-xs text-gray-400 font-sans uppercase tracking-wider mb-3 sm:mb-4">Summary of recent activity</p>
        
        {{-- Completed, Pending & Overdue Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
            {{-- Completed Tasks --}}
            <div class="rounded-2xl p-3 sm:p-4 bg-gradient-to-br from-purple-50 via-purple-100 to-purple-200 shadow-sm border border-white/60 flex flex-col gap-1">
                <span class="text-xs uppercase font-semibold text-purple-900 tracking-wide">Completed Tasks</span>
                <div class="text-4xl sm:text-5xl font-serif text-purple-900 leading-none" id="overviewCompleted">0</div>
            </div>

            {{-- Pending Tasks --}}
            <div class="rounded-2xl p-3 sm:p-4 bg-gradient-to-br from-amber-50 via-yellow-100 to-amber-100 shadow-sm border border-white/60 flex flex-col gap-1">
                <span class="text-xs uppercase font-semibold text-amber-900 tracking-wide">Pending Tasks</span>
                <div class="text-4xl sm:text-5xl font-serif text-amber-900 leading-none" id="overviewPending">0</div>
            </div>

            {{-- Overdue Tasks --}}
            <div class="rounded-2xl p-3 sm:p-4 bg-gradient-to-br from-red-50 via-red-100 to-red-200 shadow-sm border border-white/60 flex flex-col gap-1">
                <span class="text-xs uppercase font-semibold text-red-900 tracking-wide">Overdue Tasks</span>
                <div class="text-4xl sm:text-5xl font-serif text-red-900 leading-none" id="overviewOverdue">0</div>
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

@push('scripts')
<script>
    // --- CHART LOGIC ---
    // Global variables should be defined in homepage
    // let chartInstance = null;
    // let analyticsRange = 'month';
    // const categoryColors = [...];
    // const categoryMeta = {...};
    
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
            const ts = t.created || (t.created_at ? new Date(t.created_at).getTime() : (t.date ? new Date(t.date).getTime() : 0));
            return ts >= threshold;
        });
    }

    function renderChart() {
        if (typeof window.tasks === 'undefined' || typeof window.analyticsRange === 'undefined') {
            console.error('Tasks array or analyticsRange not found');
            return;
        }
        
        const chartCanvas = document.getElementById('taskChart');
        if (!chartCanvas) return;
        
        const ctx = chartCanvas.getContext('2d');

        // Filter tasks by selected time range
        const filtered = filterTasksByRange(window.tasks, window.analyticsRange);
        
        // Count completed tasks
        const completedCount = filtered.filter(t => t.is_completed || t.completed).length;
        
        // Count overdue tasks (not completed + due_at is in the past)
        const now = new Date();
        const overdueCount = filtered.filter(t => {
            if (t.is_completed || t.completed) return false;
            if (!t.due_at) return false;
            const dueDate = new Date(t.due_at);
            return dueDate < now;
        }).length;
        
        // Count pending tasks (not completed AND not overdue)
        const pendingCount = filtered.filter(t => {
            if (t.is_completed || t.completed) return false;
            // Exclude overdue tasks from pending
            if (t.due_at) {
                const dueDate = new Date(t.due_at);
                if (dueDate < now) return false; // This is overdue, not pending
            }
            return true;
        }).length;

        // Update stats cards
        const completedEl = document.getElementById('overviewCompleted');
        const pendingEl = document.getElementById('overviewPending');
        const overdueEl = document.getElementById('overviewOverdue');
        const totalOpenEl = document.getElementById('openTasksTotal');

        if (completedEl) completedEl.innerText = completedCount;
        if (pendingEl) pendingEl.innerText = pendingCount;
        if (overdueEl) overdueEl.innerText = overdueCount;

        // Get open tasks only
        const openTasks = filtered.filter(t => !(t.is_completed || t.completed));
        const totalOpen = openTasks.length;
        if (totalOpenEl) totalOpenEl.innerText = totalOpen;

        // Count by category - Use exact logic from reference
        const categoryColors = ['#6b5bff', '#f97316', '#facc15', '#22d3ee', '#a855f7', '#34d399', '#f472b6'];
        
        const counts = {};
        openTasks.forEach(t => {
            // Extract category name - handle both object and string
            let cat = 'Other';
            if (t.category) {
                if (typeof t.category === 'object' && t.category.name) {
                    cat = t.category.name;
                } else if (typeof t.category === 'string') {
                    cat = t.category;
                }
            }
            counts[cat] = (counts[cat] || 0) + 1;
        });

        const labels = Object.keys(counts);
        const data = Object.values(counts);
        let colors = labels.map((_, idx) => categoryColors[idx % categoryColors.length]);
        
        const breakdownEl = document.getElementById('categoryBreakdown');

        // Handle empty state
        if (!labels.length) {
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
        } else if (breakdownEl) {
            // Render breakdown list - Use exact format from reference
            breakdownEl.innerHTML = labels.map((label, idx) => {
                const count = data[idx];
                const percent = totalOpen ? Math.round((count / totalOpen) * 100) : 0;
                const color = colors[idx];
                
                return `
                    <li class="flex items-center justify-between bg-gray-50 dark:bg-slate-700/40 rounded-xl px-4 py-3 border border-gray-100 dark:border-slate-600">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full shadow-sm" style="background:${color}"></span>
                            <div class="flex flex-col">
                                <span class="font-semibold">${label}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">${count} task${count > 1 ? 's' : ''}</span>
                            </div>
                        </div>
                        <span class="font-semibold">${percent}%</span>
                    </li>
                `;
            }).join('');
        }
        
        // Update chart data
        if (!labels.length) {
            labels.push('No open tasks');
            data.push(1);
            colors = ['#CBD5F5'];
        }

        // Destroy old chart instance
        if (typeof window.chartInstance !== 'undefined' && window.chartInstance) {
            window.chartInstance.destroy();
        }

        // Check dark mode
        const htmlEl = document.documentElement;
        const isDark = htmlEl.classList.contains('dark');

        // Create new chart
        if (typeof Chart !== 'undefined') {
            window.chartInstance = new Chart(ctx, {
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
    }

    // Make function globally available
    window.renderChart = renderChart;
    window.filterTasksByRange = filterTasksByRange;
</script>
@endpush