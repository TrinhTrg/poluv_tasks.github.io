@props(['tasks', 'todayTasks'])

<div class="space-y-4 sm:space-y-5 md:space-y-6">
    {{-- Date Display --}}
    <div>
        <div id="currentDayName" class="text-pink-400 dark:text-pink-300 font-script text-3xl sm:text-4xl mb-1">
            @php
                $dayNames = [
                    0 => __('calendar.sunday'),
                    1 => __('calendar.monday'),
                    2 => __('calendar.tuesday'),
                    3 => __('calendar.wednesday'),
                    4 => __('calendar.thursday'),
                    5 => __('calendar.friday'),
                    6 => __('calendar.saturday'),
                ];
            @endphp
            {{ $dayNames[now()->dayOfWeek] }}
        </div>
        <div id="currentFullDate" class="text-2xl sm:text-3xl font-serif text-gray-800 dark:text-gray-200">
            @php
                $monthNames = [
                    1 => __('calendar.january'),
                    2 => __('calendar.february'),
                    3 => __('calendar.march'),
                    4 => __('calendar.april'),
                    5 => __('calendar.may'),
                    6 => __('calendar.june'),
                    7 => __('calendar.july'),
                    8 => __('calendar.august'),
                    9 => __('calendar.september'),
                    10 => __('calendar.october'),
                    11 => __('calendar.november'),
                    12 => __('calendar.december'),
                ];
            @endphp
            {{ now()->format('d') }}, {{ $monthNames[now()->month] }} {{ now()->format('Y') }}
        </div>
    </div>

    {{-- Calendar Sidebar --}}
    <x-dashboard.calendar-sidebar :tasks="$tasks" />

    {{-- Stats Today Sidebar --}}
    <x-dashboard.stats-today-sidebar :todayTasks="$todayTasks" />
</div>
