<div class="relative w-full md:w-64 group">
    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <svg class="h-4 w-4 text-gray-400 group-focus-within:text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </span>
    <input 
        type="text" 
        wire:model.live.debounce.300ms="search"
        wire:keydown.enter="performSearch"
        placeholder="Search..." 
        class="w-full pl-9 sm:pl-10 pr-20 sm:pr-24 py-2 rounded-xl border border-gray-200 dark:border-slate-600 focus:border-pink-300 focus:ring focus:ring-pink-200 text-sm bg-white dark:bg-slate-700 dark:text-white transition outline-none" 
    />
    <div class="absolute inset-y-0 right-0 flex items-center gap-1 pr-2">
        @if($search)
            <button 
                type="button"
                wire:click="clearSearch"
                class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-slate-600 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                title="Clear search"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
        <button 
            type="button"
            wire:click="performSearch"
            class="p-1.5 rounded-lg bg-pink-500 hover:bg-pink-600 text-white transition shadow-sm hover:shadow-md"
            title="Search"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </button>
    </div>
</div>

@push('scripts')
<script>
    // Logic search filtering - moved from homepage.blade.php
    document.addEventListener('livewire:init', () => {
        Livewire.on('search-changed', (event) => {
            // Update global search variable
            window.currentSearch = event.search || '';
            if (typeof currentSearch !== 'undefined') {
                currentSearch = window.currentSearch;
            }
            
            // Reset to first page
            if (typeof window.currentPage !== 'undefined') {
                window.currentPage = 1;
            }
            if (typeof currentPage !== 'undefined') {
                currentPage = 1;
            }
            
            // Filter tasks client-side
            filterTasksBySearch();
            
            // Call renderTasks if it exists (for server-side filtering if needed)
            if (typeof window.renderTasks === 'function') {
                window.renderTasks();
            }
        });
    });
    
    // Function to filter and display tasks by search
    function filterTasksBySearch() {
        const taskList = document.getElementById('taskList');
        if (!taskList || typeof window.tasks === 'undefined') return;
        
        const taskCards = taskList.querySelectorAll('[data-task-id]');
        const searchQuery = (window.currentSearch || '').trim().toLowerCase();
        
        // Remove search empty state first
        const searchEmptyState = taskList.querySelector('.col-span-1.md\\:col-span-2');
        if (searchEmptyState && searchEmptyState.textContent.includes('Try different keywords')) {
            searchEmptyState.remove();
        }
        
        if (!searchQuery) {
            // If no search query, re-apply date filter (if exists) or show all tasks
            if (window.selectedDateOnCalendar && typeof window.filterTasksByDate === 'function') {
                // Re-apply date filter to show tasks correctly
                window.filterTasksByDate();
            } else {
                // If no date filter, show all tasks
                taskCards.forEach(card => {
                    card.style.display = '';
                });
                // Remove any empty states
                const emptyState = taskList.querySelector('.col-span-1.md\\:col-span-2');
                if (emptyState && (emptyState.textContent.includes('No tasks found') || emptyState.textContent.includes('Try different keywords'))) {
                    emptyState.remove();
                }
            }
            return;
        }
        
        // Filter tasks by search query
        // First, we need to check if date filter is active
        const hasDateFilter = window.selectedDateOnCalendar && typeof window.filterTasksByDate === 'function';
        
        let visibleCount = 0;
        taskCards.forEach(card => {
            const taskId = parseInt(card.getAttribute('data-task-id'));
            const task = window.tasks.find(t => t.id === taskId);
            
            if (!task) {
                card.style.display = 'none';
                return;
            }
            
            // Check if task matches search query
            const title = (task.title || '').toLowerCase();
            const description = (task.description || task.desc || '').toLowerCase();
            const matchesSearch = title.includes(searchQuery) || description.includes(searchQuery);
            
            // If date filter is active, also check date match
            let matchesDate = true;
            if (hasDateFilter) {
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
                matchesDate = taskDate === window.selectedDateOnCalendar;
            }
            
            // Show task only if it matches both search and date filter (if active)
            if (matchesSearch && matchesDate) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide empty state for search
        if (visibleCount === 0) {
            // Check if empty state already exists
            const existingEmptyState = taskList.querySelector('.col-span-1.md\\:col-span-2');
            if (!existingEmptyState || !existingEmptyState.textContent.includes('Try different keywords')) {
                if (existingEmptyState) existingEmptyState.remove();
                const emptyDiv = document.createElement('div');
                emptyDiv.className = 'col-span-1 md:col-span-2 text-center py-8 sm:py-10';
                emptyDiv.innerHTML = `
                    <div class="text-5xl sm:text-6xl mb-3 sm:mb-4">🔍</div>
                    <h3 class="text-lg sm:text-xl font-serif text-gray-600 dark:text-gray-400">No tasks found</h3>
                    <p class="text-xs sm:text-sm text-gray-400">Try different keywords or clear your search</p>
                `;
                taskList.appendChild(emptyDiv);
            }
        }
    }
    
    window.filterTasksBySearch = filterTasksBySearch;
</script>
@endpush

