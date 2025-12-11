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

<div class="space-y-6">
    <div>
        <div id="currentDayName" class="text-pink-400 dark:text-pink-300 font-script text-4xl mb-1">
            {{ now()->format('l') }}
        </div>
        <div id="currentFullDate" class="text-3xl font-serif text-gray-800 dark:text-gray-200">
            {{ now()->format('d, F Y') }}
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-3xl p-5 smooth-shadow transition-colors">
        <div class="flex items-center justify-between mb-4 px-4 pt-2">
            <button onclick="changeMonth(-1)" class="p-0 rounded-full hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>

            <button id="btnOpenCalendarSettings" class="px-4 py-1.5 rounded-xl hover:bg-white dark:hover:bg-slate-700 hover:shadow-sm transition group border border-transparent hover:border-gray-200 dark:hover:border-slate-600">
                <span id="currentMonth" class="text-center font-serif text-lg font-bold text-gray-800 dark:text-white group-hover:text-pink-600 dark:group-hover:text-pink-400 select-none">
                    {{ now()->format('F Y') }}
                </span>
            </button>

            <button onclick="changeMonth(1)" class="p-0 rounded-full hover:bg-gray-100 dark:hover:bg-slate-700 text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <div class="flex gap-3">
            <div class="pt-[28px]"> 
                <div id="calendarWeeks" class="grid grid-cols-1 gap-y-2">
                    @foreach($weeks as $weekNum)
                        <div class="h-8 w-6 flex items-center justify-center bg-pink-50 dark:bg-slate-700 rounded text-[10px] font-bold text-pink-800 dark:text-pink-300 mb-[0px]">{{ $weekNum }}</div>
                    @endforeach
                </div>
            </div>
            
            <div id="calendar" class="w-full">
                <div class="grid grid-cols-7 gap-y-2 gap-x-1 text-xs mb-2 border-b border-gray-100 dark:border-slate-700 pb-2">
                    @foreach(['Su','Mo','Tu','We','Th','Fr','Sa'] as $day)
                        <div class="text-center font-bold text-gray-400 uppercase tracking-wider">{{ $day }}</div>
                    @endforeach
                </div>
                <div class="grid grid-cols-7 gap-y-2 gap-x-1">
                    @for($i = 0; $i < $startDay; $i++)
                        <div></div>
                    @endfor
                    @for($d = 1; $d <= $daysInMonth; $d++)
                        @php
                            $cellDate = $now->copy()->setDate($year, $month, $d);
                            $iso = $cellDate->format('Y-m-d');
                            $isToday = $iso === $today;
                            $hasTaskStart = in_array($iso, $taskStartDates);
                            $btnClass = "w-8 h-8 rounded-full flex items-center justify-center mx-auto text-sm transition hover:bg-gray-100 dark:hover:bg-slate-700 dark:text-gray-300";
                            if ($isToday) {
                                $btnClass = "w-8 h-8 rounded-full flex items-center justify-center mx-auto text-sm bg-pink-200 text-pink-800 font-bold";
                            }
                        @endphp
                        <div class="flex flex-col items-center gap-1">
                            <button data-iso="{{ $iso }}" onclick="selectDate('{{ $iso }}')" class="{{ $btnClass }}">{{ $d }}</button>
                            @if($hasTaskStart)
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                            @else
                                <span class="w-1.5 h-1.5"></span>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>