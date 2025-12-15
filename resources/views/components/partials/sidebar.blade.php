@props(['tasks', 'todayTasks'])

<div class="space-y-4 sm:space-y-5 md:space-y-6">
    {{-- Date Display --}}
    <div>
        <div id="currentDayName" class="text-pink-400 dark:text-pink-300 font-script text-3xl sm:text-4xl mb-1">
            {{ now()->format('l') }}
        </div>
        <div id="currentFullDate" class="text-2xl sm:text-3xl font-serif text-gray-800 dark:text-gray-200">
            {{ now()->format('d, F Y') }}
        </div>
    </div>

    {{-- Calendar Sidebar --}}
    <x-dashboard.calendar-sidebar :tasks="$tasks" />

    {{-- Stats Today Sidebar --}}
    <x-dashboard.stats-today-sidebar :todayTasks="$todayTasks" />
</div>

