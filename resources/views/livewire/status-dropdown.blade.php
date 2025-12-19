<div 
    x-data="{ 
        open: false, 
        selected: @entangle('status').live,
        getButtonLabel() {
            const labels = {
                'all': 'All Status',
                'completed': 'Completed',
                'pending': 'Pending'
            };
            return labels[this.selected] || 'All Status';
        }
    }" 
    class="relative"
>
    <button 
        @click="open = !open" 
        type="button" 
        class="flex items-center justify-between w-full min-w-[120px] bg-[#EAD6C0] dark:bg-slate-700 hover:bg-[#E0CCB7] text-gray-800 dark:text-white py-2 pl-3 pr-2 rounded-xl text-sm font-medium transition shadow-sm"
    >
        <span 
            x-text="getButtonLabel()" 
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
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute mt-2 left-0 w-full min-w-[160px] bg-white dark:bg-slate-800 rounded-xl shadow-xl border border-gray-100 dark:border-slate-700 z-[100] overflow-hidden origin-top-left" 
        style="display: none;"
    >
        <ul class="py-1">
            <li 
                @click="selected = 'all'; open = false" 
                class="px-4 py-2 text-sm cursor-pointer transition-colors hover:bg-pink-50 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300" 
                :class="selected === 'all' ? 'font-bold text-pink-600 dark:text-pink-400' : ''"
            >
                All Status
            </li>
            <li 
                @click="selected = 'completed'; open = false" 
                class="px-4 py-2 text-sm cursor-pointer transition-colors hover:bg-pink-50 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300" 
                :class="selected === 'completed' ? 'font-bold text-pink-600 dark:text-pink-400' : ''"
            >
                Completed
            </li>
            <li 
                @click="selected = 'pending'; open = false" 
                class="px-4 py-2 text-sm cursor-pointer transition-colors hover:bg-pink-50 dark:hover:bg-slate-700 text-gray-700 dark:text-gray-300" 
                :class="selected === 'pending' ? 'font-bold text-pink-600 dark:text-pink-400' : ''"
            >
                Pending
            </li>
        </ul>
    </div>
</div>
