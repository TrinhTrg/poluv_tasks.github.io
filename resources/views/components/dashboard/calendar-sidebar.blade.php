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
    
    // Tạo Set chứa các ngày có task chưa hoàn thành để đánh dấu chấm đỏ
    $taskStartDates = $tasks->filter(function($task) {
        return $task->start_at !== null && !$task->is_completed;
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
                            <button data-iso="{{ $iso }}" onclick="selectDate('{{ $iso }}', event)" type="button" class="{{ $btnClass }}">{{ $d }}</button>
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

@push('scripts')
<script>
    // --- CALENDAR LOGIC ---
    // Global variables should be defined in homepage
    // let selectedDateOnCalendar = null;
    // let currentViewDate = new Date();
    
    function renderCalendar(){
        if (typeof window.tasks === 'undefined' || typeof window.currentViewDate === 'undefined') {
            console.error('Tasks array or currentViewDate not found');
            return;
        }
        
        const elDate = document.getElementById('calendar');
        const elWeeks = document.getElementById('calendarWeeks');
        const elMonthTitle = document.getElementById('currentMonth');
        
        const year = window.currentViewDate.getFullYear();
        const month = window.currentViewDate.getMonth();
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

        // Tạo Set chứa các ngày có task chưa hoàn thành để đánh dấu chấm đỏ
        // Check cả start_at và start_date để tương thích
        const taskStartDates = new Set(window.tasks
            .filter(t => !(t.is_completed || t.completed)) // Chỉ lấy tasks chưa hoàn thành
            .map(t => {
                // Ưu tiên start_at (field name từ database), sau đó start_date, cuối cùng startDate (alias)
                const startDate = t.start_at || t.start_date || t.startDate;
                if (!startDate) return null;
                // Nếu là string, parse nó; nếu đã là Date object, dùng trực tiếp
                const date = typeof startDate === 'string' ? new Date(startDate) : startDate;
                if (isNaN(date.getTime())) return null;
                return date.toISOString().slice(0,10);
            })
            .filter(Boolean));
        
        for (let d=1; d<=daysInMonth; d++){
            const cellDate = new Date(year, month, d, 12, 0, 0); 
            const iso = cellDate.toISOString().slice(0,10);
            const isToday = cellDate.toDateString() === today.toDateString();
            const hasTaskStart = taskStartDates.has(iso);
            
            let btnClass = "w-8 h-8 rounded-full flex items-center justify-center mx-auto text-sm transition hover:bg-gray-100 dark:hover:bg-slate-700 dark:text-gray-300";
            if (window.selectedDateOnCalendar === iso) {
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
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const val = btn.getAttribute('data-iso');
                    if (typeof window.selectedDateOnCalendar === 'undefined') window.selectedDateOnCalendar = null;
                    window.selectedDateOnCalendar = (window.selectedDateOnCalendar === val) ? null : val;
                    renderCalendar();
                    // Filter tasks by selected date
                    if (typeof filterTasksByDate === 'function') {
                        filterTasksByDate();
                    }
                });
            });
        }
    }

    function changeMonth(step) {
        if (typeof window.currentViewDate === 'undefined') {
            window.currentViewDate = new Date();
        }
        window.currentViewDate.setMonth(window.currentViewDate.getMonth() + step);
        renderCalendar();
    }

    // Function để xử lý click date từ server-rendered calendar
    function selectDate(iso, event) {
        // Prevent default behavior if event is provided
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        if (typeof window.selectedDateOnCalendar === 'undefined') window.selectedDateOnCalendar = null;
        
        // Toggle: nếu click vào cùng ngày thì bỏ filter, nếu khác ngày thì filter
        window.selectedDateOnCalendar = (window.selectedDateOnCalendar === iso) ? null : iso;
        
        // Re-render calendar to show selected state
        renderCalendar();
        
        // Filter tasks by selected date
        filterTasksByDate();
    }
    
    // Function để filter tasks theo ngày được chọn
    function filterTasksByDate() {
        const taskList = document.getElementById('taskList');
        if (!taskList || typeof window.tasks === 'undefined') return;
        
        const taskCards = taskList.querySelectorAll('[data-task-id]');
        
        if (!window.selectedDateOnCalendar) {
            // Nếu không có date được chọn, hiển thị tất cả tasks
            taskCards.forEach(card => {
                card.style.display = '';
            });
            // Xóa empty state của date filter nếu có
            const allEmptyStates = taskList.querySelectorAll('.col-span-1.md\\:col-span-2');
            allEmptyStates.forEach(emptyState => {
                if (emptyState.textContent.includes('No tasks found for this date') || 
                    emptyState.textContent.includes('Try selecting another date')) {
                    emptyState.remove();
                }
            });
            return;
        }
        
        // Filter tasks theo selected date
        // Check cả start_at và due_at để match với ngày được chọn
        taskCards.forEach(card => {
            const taskId = parseInt(card.getAttribute('data-task-id'));
            const task = window.tasks.find(t => t.id === taskId);
            
            if (!task) {
                card.style.display = 'none';
                return;
            }
            
            // Lấy ngày từ start_at hoặc due_at
            const startDate = task.start_at || task.start_date || task.startDate;
            const dueDate = task.due_at || task.due_date || task.date;
            
            let taskDate = null;
            if (startDate) {
                const date = typeof startDate === 'string' ? new Date(startDate) : startDate;
                if (!isNaN(date.getTime())) {
                    taskDate = date.toISOString().slice(0, 10);
                }
            }
            if (!taskDate && dueDate) {
                const date = typeof dueDate === 'string' ? new Date(dueDate) : dueDate;
                if (!isNaN(date.getTime())) {
                    taskDate = date.toISOString().slice(0, 10);
                }
            }
            
            // Hiển thị task nếu ngày match với selected date
            if (taskDate === window.selectedDateOnCalendar) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
        
        // Hiển thị empty state nếu không có task nào
        const visibleTasks = Array.from(taskCards).filter(card => card.style.display !== 'none');
        
        // Xóa tất cả empty states trước (cả date và search empty states)
        const allEmptyStates = taskList.querySelectorAll('.col-span-1.md\\:col-span-2');
        allEmptyStates.forEach(emptyState => {
            // Chỉ xóa empty states được tạo bởi JavaScript (không phải server-rendered)
            if (emptyState.textContent.includes('No tasks found for this date') || 
                emptyState.textContent.includes('Try different keywords') ||
                emptyState.textContent.includes('Try selecting another date')) {
                emptyState.remove();
            }
        });
        
        if (visibleTasks.length === 0) {
            // Tạo empty state mới cho date filter
            const emptyDiv = document.createElement('div');
            emptyDiv.className = 'col-span-1 md:col-span-2 text-center py-8 sm:py-10';
            emptyDiv.innerHTML = `
                <div class="text-5xl sm:text-6xl mb-3 sm:mb-4">📝</div>
                <h3 class="text-lg sm:text-xl font-serif text-gray-600 dark:text-gray-400">No tasks found for this date</h3>
                <p class="text-xs sm:text-sm text-gray-400">Try selecting another date or create a new task!</p>
            `;
            taskList.appendChild(emptyDiv);
        }
    }

    // Calendar settings modal events
    document.addEventListener('DOMContentLoaded', function() {
        const dateModal = document.getElementById('dateSelectModal');
        const btnOpenCalendarSettings = document.getElementById('btnOpenCalendarSettings');
        if (btnOpenCalendarSettings) {
            btnOpenCalendarSettings.addEventListener('click', () => {
                if (typeof window.currentViewDate === 'undefined') {
                    window.currentViewDate = new Date();
                }
                const selectMonth = document.getElementById('selectMonth');
                const inputYear = document.getElementById('inputYear');
                if (selectMonth) selectMonth.value = window.currentViewDate.getMonth();
                if (inputYear) inputYear.value = window.currentViewDate.getFullYear();
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
                const selectMonth = document.getElementById('selectMonth');
                const inputYear = document.getElementById('inputYear');
                const m = selectMonth ? parseInt(selectMonth.value) : 0;
                const y = inputYear ? parseInt(inputYear.value) : new Date().getFullYear();
                if (!isNaN(m) && !isNaN(y)) {
                    if (typeof window.currentViewDate === 'undefined') {
                        window.currentViewDate = new Date();
                    }
                    window.currentViewDate.setFullYear(y);
                    window.currentViewDate.setMonth(m);
                    renderCalendar();
                }
                if (dateModal) {
                    dateModal.classList.add('hidden');
                    dateModal.classList.remove('flex');
                }
            });
        }
    });

    // Make functions globally available
    window.renderCalendar = renderCalendar;
    window.changeMonth = changeMonth;
    window.selectDate = selectDate;
    window.filterTasksByDate = filterTasksByDate;
</script>
@endpush