{{-- Task Create/Edit Modal --}}
<div id="modalBackdrop" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#4A403A]/60 backdrop-blur-sm transition-opacity p-3 sm:p-4">
    <div class="bg-[#FAF7F2] dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-6 md:p-8 w-full max-w-3xl smooth-shadow transform transition-all scale-100 border-2 sm:border-4 border-white dark:border-slate-700 max-h-[95vh] overflow-y-auto">
        <h3 id="modalTitle" class="text-xl sm:text-2xl font-serif font-semibold mb-4 sm:mb-5 text-gray-800 dark:text-white">Add New Task</h3>
        <form id="taskForm" class="space-y-3.5 sm:space-y-4">
            {{-- Title - Full Width --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-wider">Title</label>
                <input id="taskTitle" class="w-full px-3 py-2.5 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl focus:border-pink-300 dark:text-white outline-none transition text-sm" placeholder="e.g. Learn React" required />
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-wider">Description</label>
                <textarea id="taskDesc" rows="2" class="w-full px-3 py-2.5 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl focus:border-pink-300 dark:text-white outline-none transition resize-none text-sm" placeholder="Details..."></textarea>
            </div>

            {{-- Category + Remind me toggle --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-wider">Category</label>
                    <div class="relative">
                        <select id="taskCategory" class="w-full px-3 py-2.5 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl appearance-none outline-none dark:text-white cursor-pointer text-sm">
                            <option value="Work">💼 Work</option>
                            <option value="Homework">📚 Homework</option>
                            <option value="Meeting">🗣️ Meeting</option>
                            <option value="Personal">👤 Personal</option>
                            <option value="Other">📦 Other</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-gray-500">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between gap-4 w-full">
    
    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 flex-1 break-words">
        Remind me (24h alert)
    </span>
    
    <label class="inline-flex items-center cursor-pointer ml-auto">
        <input type="checkbox" id="taskNotify" class="sr-only peer" wire:model.live="notify">
        
        <div class="relative w-13 h-5 bg-gray-200 dark:bg-slate-600 rounded-full peer 
                    shrink-0 min-w-[2.75rem]
                    peer-focus:outline-none peer-focus:ring-3 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800
                    peer-checked:bg-blue-600 
                    after:content-[''] after:absolute after:top-[2px] after:left-[3px]
                    after:bg-white after:rounded-full after:h-4 after:w-4 
                    after:transition-all after:duration-200 after:ease-in-out
                    after:shadow-md
                    peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full">
        </div>
    </label>
</div>
            </div>

            {{-- Start + Due --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-wider">Start</label>
                    <div class="flex flex-col gap-1.5">
                        <div class="relative">
                            <input id="taskStartDate" type="date" class="w-full px-3 py-2 pr-9 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:text-white dark:scheme-dark" />
                            <button type="button" onclick="document.getElementById('taskStartDate').showPicker()" class="absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 cursor-pointer transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>
                        <div class="relative">
                            <input id="taskStartTime" type="time" class="w-full px-3 py-2 pr-9 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:text-white dark:scheme-dark" />
                            <button type="button" onclick="document.getElementById('taskStartTime').showPicker()" class="absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 cursor-pointer transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-wider">Due</label>
                    <div class="flex flex-col gap-1.5">
                        <div class="relative">
                            <input id="taskDueDate" type="date" class="w-full px-3 py-2 pr-9 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:text-white dark:scheme-dark" />
                            <button type="button" onclick="document.getElementById('taskDueDate').showPicker()" class="absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 cursor-pointer transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </button>
                        </div>
                        <div class="relative">
                            <input id="taskDueTime" type="time" class="w-full px-3 py-2 pr-9 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:text-white dark:scheme-dark" />
                            <button type="button" onclick="document.getElementById('taskDueTime').showPicker()" class="absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 cursor-pointer transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Color Tag + Priority --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1.5 tracking-wider">Color Tag</label>
                    <div id="colorPickerContainer" class="flex flex-wrap gap-2.5 justify-start"></div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1.5 tracking-wider">Priority</label>
                    <div x-data="{ 
                            priority: 2,
                            options: [
                                { value: 1, label: 'Low', color: 'bg-gray-100 text-gray-700 border-gray-300 ring-gray-300' },
                                { value: 2, label: 'Medium', color: 'bg-blue-100 text-blue-700 border-blue-300 ring-blue-300' },
                                { value: 3, label: 'High', color: 'bg-red-100 text-red-700 border-red-300 ring-red-300' }
                            ]
                        }" 
                        class="flex gap-2"
                    >
                        <input type="hidden" id="taskPriority" name="priority" x-model="priority">
                        <template x-for="option in options" :key="option.value">
                            <button 
                                type="button" 
                                @click="priority = option.value"
                                class="flex-1 px-3 py-2 rounded-xl text-xs font-semibold border transition-all duration-200 focus:outline-none"
                                :class="priority === option.value 
                                    ? (option.color + ' ring-2 ring-offset-1 dark:ring-offset-slate-800') 
                                    : 'bg-white dark:bg-slate-700 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-slate-600 hover:bg-gray-50 dark:hover:bg-slate-600'"
                            >
                                <span x-text="option.label"></span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3 pt-3 sm:pt-4 border-t border-gray-200 dark:border-slate-700">
                <button type="button" id="cancelModal" class="w-full sm:w-auto px-5 py-2 rounded-xl border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 font-medium transition text-sm">Cancel</button>
                <button type="submit" class="w-full sm:w-auto px-5 py-2 rounded-xl bg-black dark:bg-indigo-600 text-white hover:bg-gray-800 dark:hover:bg-indigo-700 font-medium shadow-lg transition text-sm">Save Task</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // --- MODAL SCRIPTS (Add/Edit Task Form) ---
    // Color picker options - Pastel colors
    const colorOptions = [
        { id: 'colorOption1', value: '#FFB6C1', bgClass: 'bg-[#FFB6C1]' }, // Pastel Pink
        { id: 'colorOption2', value: '#B0E0E6', bgClass: 'bg-[#B0E0E6]' }, // Pastel Blue
        { id: 'colorOption3', value: '#FFFACD', bgClass: 'bg-[#FFFACD]' }, // Pastel Yellow
        { id: 'colorOption4', value: '#DDA0DD', bgClass: 'bg-[#DDA0DD]' }, // Pastel Purple
        { id: 'colorOption5', value: '#FFDAB9', bgClass: 'bg-[#FFDAB9]' }, // Pastel Peach
        { id: 'colorOption6', value: '#98FB98', bgClass: 'bg-[#98FB98]' }  // Pastel Green
    ];
    const DEFAULT_COLOR = colorOptions[0].value;

    function renderColorOptions(){
        const container = document.getElementById('colorPickerContainer');
        if (!container) return;
        container.innerHTML = colorOptions.map((opt, index) => `
            <div class="relative">
                <input type="radio" name="taskColor" id="${opt.id}" value="${opt.value}" class="sr-only color-radio peer"${index === 0 ? ' checked' : ''}>
                <label for="${opt.id}" class="block w-8 h-8 rounded-full cursor-pointer border-2 border-white dark:border-slate-600 shadow-sm transition-all peer-checked:ring-4 peer-checked:ring-yellow-500 peer-checked:ring-offset-2 peer-checked:border-yellow-600 peer-checked:scale-90 ${opt.bgClass}"></label>
            </div>
        `).join('');
    }

    function setSelectedColor(value = DEFAULT_COLOR){
        const radios = document.getElementsByName('taskColor');
        let matched = false;
        for (const radio of radios){
            if (radio.value === value){ 
                radio.checked = true; 
                matched = true;
                // Trigger change event to update visual feedback
                radio.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
        if (!matched && radios.length) {
            radios[0].checked = true;
            radios[0].dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    // Modal Events
    function openAddModal(){
        if (typeof window.editingId === 'undefined') window.editingId = null;
        window.editingId = null;
        document.getElementById('modalTitle').innerText = 'New Task';
        document.getElementById('taskTitle').value = '';
        document.getElementById('taskDesc').value = '';
        document.getElementById('taskCategory').value = 'Work';
        document.getElementById('taskStartDate').value = new Date().toISOString().slice(0,10);
        document.getElementById('taskStartTime').value = '';
        document.getElementById('taskDueDate').value = (window.selectedDateOnCalendar || new Date().toISOString().slice(0,10));
        document.getElementById('taskDueTime').value = '';
        document.getElementById('taskPriority').value = 'medium';
        document.getElementById('taskNotify').checked = false;
        setSelectedColor();
        renderColorOptions();
        document.getElementById('modalBackdrop').classList.remove('hidden');
        document.getElementById('modalBackdrop').classList.add('flex');
    }

    function openEditModal(id){
        if (typeof window.tasks === 'undefined') {
            console.error('Tasks array not found');
            return;
        }
        const t = window.tasks.find(x => x.id === parseInt(id));
        if(!t) return;
        if (typeof window.editingId === 'undefined') window.editingId = null;
        window.editingId = id;
        document.getElementById('modalTitle').innerText = 'Edit Task';
        document.getElementById('taskTitle').value = t.title;
        document.getElementById('taskDesc').value = t.description || t.desc || '';
        document.getElementById('taskCategory').value = t.category || 'Work';
        
        // Parse start_at datetime để lấy date và time (fix timezone issue)
        let startDate = '';
        let startTime = '';
        if (t.start_at) {
            // Parse datetime string trực tiếp để tránh timezone conversion
            // Format: "2025-12-17 23:00:00" hoặc "2025-12-17T23:00:00"
            const startAtStr = t.start_at.replace('T', ' ').replace('Z', '').trim();
            const [datePart, timePart] = startAtStr.split(' ');
            if (datePart && timePart) {
                startDate = datePart.slice(0, 10);
                const [hours, minutes] = timePart.split(':');
                startTime = `${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}`;
            } else {
                // Fallback: parse như datetime object
                const startDateTime = new Date(t.start_at);
                if (!isNaN(startDateTime.getTime())) {
                    startDate = startDateTime.toISOString().slice(0, 10);
                    const hours = String(startDateTime.getHours()).padStart(2, '0');
                    const minutes = String(startDateTime.getMinutes()).padStart(2, '0');
                    startTime = `${hours}:${minutes}`;
                }
            }
        } else if (t.start_date || t.startDate) {
            startDate = (t.start_date || t.startDate).slice(0, 10);
            startTime = t.start_time ? t.start_time.slice(0, 5) : (t.startTime || '');
        }
        document.getElementById('taskStartDate').value = startDate;
        document.getElementById('taskStartTime').value = startTime;
        
        // Parse due_at datetime để lấy date và time (fix timezone issue)
        let dueDate = '';
        let dueTime = '';
        if (t.due_at) {
            // Parse datetime string trực tiếp để tránh timezone conversion
            const dueAtStr = t.due_at.replace('T', ' ').replace('Z', '').trim();
            const [datePart, timePart] = dueAtStr.split(' ');
            if (datePart && timePart) {
                dueDate = datePart.slice(0, 10);
                const [hours, minutes] = timePart.split(':');
                dueTime = `${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}`;
            } else {
                // Fallback: parse như datetime object
                const dueDateTime = new Date(t.due_at);
                if (!isNaN(dueDateTime.getTime())) {
                    dueDate = dueDateTime.toISOString().slice(0, 10);
                    const hours = String(dueDateTime.getHours()).padStart(2, '0');
                    const minutes = String(dueDateTime.getMinutes()).padStart(2, '0');
                    dueTime = `${hours}:${minutes}`;
                }
            }
        } else if (t.due_date || t.date) {
            dueDate = (t.due_date || t.date).slice(0, 10);
            dueTime = t.due_time ? t.due_time.slice(0, 5) : (t.dueTime || '');
        }
        document.getElementById('taskDueDate').value = dueDate;
        document.getElementById('taskDueTime').value = dueTime;
        
        // Convert priority từ số (1,2,3) sang string (low,medium,high) để match với select option
        let priorityValue = 'medium';
        if (t.priority) {
            const priorityNum = typeof t.priority === 'string' ? parseInt(t.priority) : t.priority;
            if (priorityNum === 1) priorityValue = 'low';
            else if (priorityNum === 2) priorityValue = 'medium';
            else if (priorityNum === 3) priorityValue = 'high';
        }
        document.getElementById('taskPriority').value = priorityValue;
        document.getElementById('taskNotify').checked = t.has_notify || t.notify || false;
        renderColorOptions();
        setSelectedColor(t.color || DEFAULT_COLOR);
        document.getElementById('modalBackdrop').classList.remove('hidden');
        document.getElementById('modalBackdrop').classList.add('flex');
    }

    // Function to close modal
    function closeModal() {
        const modalBackdrop = document.getElementById('modalBackdrop');
        if (modalBackdrop) {
            modalBackdrop.classList.add('hidden'); 
            modalBackdrop.classList.remove('flex');
        }
        if (typeof window.editingId !== 'undefined') window.editingId = null;
    }

    // Make functions globally available
    window.openAddModal = openAddModal;
    window.openEditModal = openEditModal;
    window.closeModal = closeModal;
    window.renderColorOptions = renderColorOptions;
    window.setSelectedColor = setSelectedColor;

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        // Cancel button
        const cancelBtn = document.getElementById('cancelModal');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeModal();
            });
        }

        // Add button
        const btnAdd = document.getElementById('btnAdd');
        if (btnAdd) btnAdd.addEventListener('click', openAddModal);

        // Form submit
        const taskForm = document.getElementById('taskForm');
        if (taskForm) {
            taskForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const title = document.getElementById('taskTitle').value.trim(); 
                if(!title) return;
                
                // Get API call function from global scope
                if (typeof window.apiCall === 'undefined') {
                    console.error('apiCall function not found');
                    return;
                }

                const payload = {
                    title: title,
                    description: document.getElementById('taskDesc').value.trim(),
                    category: document.getElementById('taskCategory').value,
                    start_date: document.getElementById('taskStartDate').value,
                    start_time: document.getElementById('taskStartTime').value,
                    due_date: document.getElementById('taskDueDate').value,
                    due_time: document.getElementById('taskDueTime').value,
                    priority: document.getElementById('taskPriority').value,
                    notify: document.getElementById('taskNotify').checked,
                    color: document.querySelector('input[name="taskColor"]:checked')?.value || DEFAULT_COLOR
                };

                try {
                    let result;
                    if(window.editingId){
                        result = await window.apiCall(`/tasks/${window.editingId}`, 'PUT', payload);
                    } else {
                        result = await window.apiCall('/tasks', 'POST', payload);
                    }
                    
                    if (result && result.id) {
                        closeModal();
                        // Reload để lấy HTML mới từ server
                        window.location.reload();
                    } else {
                        console.error('Save task failed:', result);
                        alert('Failed to save task. Please check the console for details.');
                    }
                } catch (error) {
                    console.error('Error saving task:', error);
                    alert('Failed to save task: ' + (error.message || 'Unknown error'));
                }
            });
        }

        // Close modal when clicking on backdrop
        const modalBackdrop = document.getElementById('modalBackdrop');
        if (modalBackdrop) {
            modalBackdrop.addEventListener('click', (e) => {
                if (e.target === modalBackdrop) {
                    closeModal();
                }
            });
        }

        // Initial render color options
        renderColorOptions();
        
        // Add event listeners for color radio buttons to update visual feedback
        document.addEventListener('change', function(e) {
            if (e.target && e.target.name === 'taskColor') {
                // Visual feedback is handled by peer-checked classes in Tailwind
                // No additional JS needed, but we can add a small animation
                const label = document.querySelector(`label[for="${e.target.id}"]`);
                if (label) {
                    label.classList.add('scale-90');
                    setTimeout(() => {
                        label.classList.remove('scale-90');
                    }, 200);
                }
            }
        });
    });
</script>
@endpush

