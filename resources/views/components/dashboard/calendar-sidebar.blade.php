@php
    // Lấy tasks từ view nếu có, nếu không thì tạo empty collection
    $tasks = $tasks ?? collect([]);
    
    // Tính toán calendar cho tháng hiện tại
    $now = now();
    $year = $now->year;
    $month = $now->month;
    $first = $now->copy()->startOfMonth();
    $last = $now->copy()->endOfMonth();
    $startDay = $first->dayOfWeek; // 0 = Sunday, 6 = Saturday
    $daysInMonth = $last->day;
    $today = $now->format('Y-m-d');
    
    // Tạo Set chứa các ngày có task để đánh dấu chấm đỏ
    $taskStartDates = $tasks->filter(function($task) {
        return $task->start_at !== null;
    })->map(function($task) {
        return $task->start_at->format('Y-m-d');
    })->unique()->toArray();
    
    // XÓA HOÀN TOÀN hàm $getISOWeek - không cần nữa
    
    // Tính toán weeks
    $numRows = ceil(($startDay + $daysInMonth) / 7);
    $weeks = [];
    $currentProcessingDate = $first->copy()->subDays($startDay);
    
    for($r = 0; $r < $numRows; $r++) {
        // Dùng method weekOfYear có sẵn của Carbon
        $weeks[] = $currentProcessingDate->weekOfYear;
        $currentProcessingDate->addDays(7);
    }
@endphp

<div class="space-y-4 sm:space-y-5 md:space-y-6">
    <div class="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-5 smooth-shadow transition-colors">
        <div class="flex items-center justify-between mb-3 sm:mb-4 px-2 sm:px-3 md:px-4 pt-2">
            <button onclick="changeMonth(-1)" class="p-1 sm:p-0 rounded-full hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>

            <button id="btnOpenCalendarSettings" class="px-3 sm:px-4 py-1 sm:py-1.5 rounded-xl hover:bg-white dark:hover:bg-slate-700 hover:shadow-sm transition group border border-transparent hover:border-gray-200 dark:hover:border-slate-600">
                <span id="currentMonth" class="text-center font-serif text-base sm:text-lg font-bold text-gray-800 dark:text-white group-hover:text-pink-600 dark:group-hover:text-pink-400 select-none">
                    {{ now()->format('F Y') }}
                </span>
            </button>

            <button onclick="changeMonth(1)" class="p-1 sm:p-0 rounded-full hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <div class="flex gap-2 sm:gap-3">
            <div class="pt-[24px] sm:pt-[28px]"> 
                <div id="calendarWeeks" class="grid grid-cols-1 gap-y-1.5 sm:gap-y-2">
                    @foreach($weeks as $weekNum)
                        <div class="h-7 w-5 sm:h-8 sm:w-6 flex items-center justify-center bg-pink-50 dark:bg-slate-700 rounded text-[9px] sm:text-[10px] font-bold text-pink-800 dark:text-pink-300 mb-[0px]">{{ $weekNum }}</div>
                    @endforeach
                </div>
            </div>
            
            <div id="calendar" class="w-full">
                <div class="grid grid-cols-7 gap-y-1.5 sm:gap-y-2 gap-x-0.5 sm:gap-x-1 text-[10px] sm:text-xs mb-2 border-b border-gray-100 dark:border-slate-700 pb-1.5 sm:pb-2">
                    @foreach(['Su','Mo','Tu','We','Th','Fr','Sa'] as $day)
                        <div class="text-center font-bold text-gray-400 uppercase tracking-wider">{{ $day }}</div>
                    @endforeach
                </div>
                <div class="grid grid-cols-7 gap-y-1.5 sm:gap-y-2 gap-x-0.5 sm:gap-x-1">
                    @for($i = 0; $i < $startDay; $i++)
                        <div></div>
                    @endfor
                    @for($d = 1; $d <= $daysInMonth; $d++)
                        @php
                            $cellDate = $now->copy()->setDate($year, $month, $d);
                            $iso = $cellDate->format('Y-m-d');
                            $isToday = $iso === $today;
                            $hasTaskStart = in_array($iso, $taskStartDates);
                            $btnClass = "w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center mx-auto text-xs sm:text-sm transition hover:bg-gray-100 dark:hover:bg-slate-700 dark:text-gray-300";
                            if ($isToday) {
                                $btnClass = "w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center mx-auto text-xs sm:text-sm bg-pink-200 text-pink-800 font-bold";
                            }
                        @endphp
                        <div class="flex flex-col items-center gap-0.5 sm:gap-1">
                            <button data-iso="{{ $iso }}" onclick="selectDate('{{ $iso }}')" class="{{ $btnClass }}">{{ $d }}</button>
                            @if($hasTaskStart)
                                <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full bg-red-500"></span>
                            @else
                                <span class="w-1 h-1 sm:w-1.5 sm:h-1.5"></span>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>