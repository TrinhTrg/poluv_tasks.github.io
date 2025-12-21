<div 
    wire:ignore
    x-data="{ 
        open: false, 
        selected: @entangle('range')
    }" 
    class="relative"
>
    <button 
        @click="open = !open" 
        type="button" 
        class="flex items-center justify-between w-full min-w-[120px] bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 text-gray-700 dark:text-white py-2 pl-3 pr-2 rounded-xl text-sm font-medium hover:bg-gray-50 dark:hover:bg-slate-600 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
    >
        <span 
            x-text="selected === 'day' ? @js(__('common.day')) : (selected === 'week' ? @js(__('common.week')) : (selected === 'month' ? @js(__('common.month')) : @js(__('common.year'))))" 
            class="truncate"
        ></span>
        <svg 
            class="w-4 h-4 ml-2 transition-transform duration-200" 
            :class="open ? 'rotate-180' : ''" 
            fill="none" 
            viewBox="0 0 24 24" 
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div 
        x-show="open" 
        @click.outside="open = false" 
        x-transition 
        class="absolute mt-2 right-0 w-full min-w-[120px] bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-gray-100 dark:border-slate-700 z-[100] overflow-hidden" 
        style="display: none;"
    >
        <ul class="py-1">
            <li 
                @click="selected = 'day'; open = false" 
                class="px-3 py-2 text-sm cursor-pointer hover:bg-pink-50 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300" 
                :class="selected === 'day' ? 'font-bold text-pink-600 dark:text-pink-400' : ''"
            >
                {{ __('common.day') }}
            </li>
            <li 
                @click="selected = 'week'; open = false" 
                class="px-3 py-2 text-sm cursor-pointer hover:bg-pink-50 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300" 
                :class="selected === 'week' ? 'font-bold text-pink-600 dark:text-pink-400' : ''"
            >
                {{ __('common.week') }}
            </li>
            <li 
                @click="selected = 'month'; open = false" 
                class="px-3 py-2 text-sm cursor-pointer hover:bg-pink-50 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300" 
                :class="selected === 'month' ? 'font-bold text-pink-600 dark:text-pink-400' : ''"
            >
                {{ __('common.month') }}
            </li>
            <li 
                @click="selected = 'year'; open = false" 
                class="px-3 py-2 text-sm cursor-pointer hover:bg-pink-50 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300" 
                :class="selected === 'year' ? 'font-bold text-pink-600 dark:text-pink-400' : ''"
            >
                {{ __('common.year') }}
            </li>
        </ul>
    </div>
</div>

