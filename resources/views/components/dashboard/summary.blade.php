@props(['tasks'])

@php
    // Tính toán số liệu ban đầu từ server để hiển thị ngay lập tức
    $completedCount = $tasks->where('completed', true)->count();
    $pendingCount   = $tasks->where('completed', false)->count();
    $totalCount     = $tasks->count();
@endphp

<div class="mt-12 pt-8 border-t border-gray-200/60 dark:border-slate-700">
    <h3 class="text-lg font-serif italic text-gray-500 dark:text-gray-400 mb-6 text-center">Dashboard Summary</h3>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-completed rounded-3xl p-6 relative overflow-hidden flex flex-col justify-between h-40">
            <div class="absolute -right-4 -top-4 text-white opacity-20 text-7xl rotate-12">✓</div>
            <div>
                <div class="text-xs font-bold text-gray-800 uppercase tracking-widest mb-1">Completed</div>
                <div class="text-sm text-gray-800/80">Tasks</div>
            </div>
            <div id="statCompleted" class="text-5xl font-serif text-gray-900 leading-none">
                {{ str_pad($completedCount, 2, '0', STR_PAD_LEFT) }}
            </div>
        </div>

        <div class="bg-pending rounded-3xl p-6 relative overflow-hidden flex flex-col justify-between h-40">
            <div class="absolute -right-4 -top-4 text-white opacity-20 text-7xl rotate-12">?</div>
            <div>
                <div class="text-xs font-bold text-white uppercase tracking-widest mb-1">Pending</div>
                <div class="text-sm text-white/80">Tasks</div>
            </div>
            <div id="statPending" class="text-5xl font-serif text-white leading-none">
                {{ str_pad($pendingCount, 2, '0', STR_PAD_LEFT) }}
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 smooth-shadow flex flex-col items-center justify-center h-40 relative overflow-hidden text-center transition-colors">
            <div class="absolute top-0 right-0 w-20 h-full bg-gradient-to-l from-gray-50 dark:from-slate-700 to-transparent opacity-50"></div>
            <div class="z-10">
                <div class="text-sm font-bold text-cyan-600 dark:text-cyan-400 mb-2 uppercase tracking-wide">Tasks created</div>
                <div id="statTotal" class="text-6xl font-serif text-gray-900 dark:text-white leading-none tracking-tight">
                    {{ str_pad($totalCount, 3, '0', STR_PAD_LEFT) }}
                </div>
            </div>
        </div>

    </div>
</div>