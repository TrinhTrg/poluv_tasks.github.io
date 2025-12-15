<div 
    x-data="{ 
        open: false, 
        selected: @entangle('sort')
    }" 
    class="relative"
>
    <button 
        @click="open = !open" 
        type="button" 
        class="flex items-center justify-between w-full min-w-[120px] bg-[#EAD6C0] dark:bg-slate-700 hover:bg-[#E0CCB7] text-gray-800 dark:text-white py-2 pl-3 pr-2 rounded-xl text-sm font-medium transition shadow-sm"
    >
        <span 
            x-text="selected === 'newest' ? 'Newest' : (selected === 'high' ? 'High Priority' : (selected === 'medium' ? 'Medium Priority' : 'Low Priority'))" 
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
                @click="selected = 'newest'; open = false" 
                class="px-3 py-2 text-sm cursor-pointer hover:bg-pink-50 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300" 
                :class="selected === 'newest' ? 'font-bold text-pink-600 dark:text-pink-400' : ''"
            >
                Newest
            </li>
            <li 
                @click="selected = 'high'; open = false" 
                class="px-3 py-2 text-sm cursor-pointer hover:bg-pink-50 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300" 
                :class="selected === 'high' ? 'font-bold text-pink-600 dark:text-pink-400' : ''"
            >
                High Priority
            </li>
            <li 
                @click="selected = 'medium'; open = false" 
                class="px-3 py-2 text-sm cursor-pointer hover:bg-pink-50 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300" 
                :class="selected === 'medium' ? 'font-bold text-pink-600 dark:text-pink-400' : ''"
            >
                Medium Priority
            </li>
            <li 
                @click="selected = 'low'; open = false" 
                class="px-3 py-2 text-sm cursor-pointer hover:bg-pink-50 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300" 
                :class="selected === 'low' ? 'font-bold text-pink-600 dark:text-pink-400' : ''"
            >
                Low Priority
            </li>
        </ul>
    </div>
</div>
