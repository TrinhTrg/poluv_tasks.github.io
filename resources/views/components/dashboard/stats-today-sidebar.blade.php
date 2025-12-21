@php
    // Lấy todayTasks từ view nếu có, nếu không thì tạo empty collection
    $todayTasks = $todayTasks ?? collect([]);
    
    // Helper function để format time
    $formatTime = function($datetime) {
        if (!$datetime) return '';
        return $datetime->format('h:i A');
    };
    
    $getTimeRange = function($task) use ($formatTime) {
        $start = $formatTime($task->start_at);
        $end = $formatTime($task->due_at);
        if ($start && $end) return $start . ' - ' . $end;
        if ($start) return $start;
        if ($end) return $end;
        return '--';
    };
    
    $categoryColors = [
        'Work' => 'bg-green-400',
        'Homework' => 'bg-blue-400', 
        'Meeting' => 'bg-red-400',
        'Personal' => 'bg-yellow-400',
        'Other' => 'bg-purple-400'
    ];
@endphp

<div class="bg-white dark:bg-[#1E293B] rounded-2xl sm:rounded-3xl p-4 sm:p-5 md:p-6 shadow-sm smooth-shadow border border-gray-100 dark:border-gray-700 flex-grow">
    <div class="flex justify-between items-center mb-3 sm:mb-4">
        <h3 class="font-bold text-base sm:text-lg font-serif text-gray-800 dark:text-white">{{__('common.today_schedule')}}</h3>
        <span id="todayDateDisplay" class="text-xs text-gray-400 font-medium font-sans">
            {{ now()->format('M d, Y') }}
        </span>
    </div>

    <div id="todayList" class="space-y-2 sm:space-y-3 overflow-y-auto max-h-[250px] sm:max-h-[300px] pr-1">
        @if($todayTasks->isEmpty())
            <div class="text-center text-gray-400 text-xs sm:text-sm py-3 sm:py-4 italic">{{__('common.no_tasks_for_today')}}</div>
        @else
            @foreach($todayTasks as $task)
                @php
                    $category = $task->category ? $task->category->name : 'Other';
                    $colorClass = $categoryColors[$category] ?? 'bg-gray-400';
                    $timeRange = $getTimeRange($task);
                @endphp
                <div class="flex gap-2 sm:gap-3 items-center group cursor-pointer" onclick="openEditModal('{{ $task->id }}')">
                    <div class="w-0.5 sm:w-1 h-8 sm:h-10 rounded-full {{ $colorClass }}"></div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs sm:text-sm font-semibold text-gray-800 dark:text-gray-200 line-clamp-1 group-hover:text-pink-500 dark:group-hover:text-pink-400 transition">
                            {{ $task->title }}
                        </div>
                        <div class="text-[10px] sm:text-xs text-gray-400">{{ $timeRange }}</div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>