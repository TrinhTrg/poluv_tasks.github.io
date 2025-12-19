@extends('layouts.base-layout')

@section('content')
    <header class="bg-header dark:bg-slate-800 sticky top-0 z-30 transition-all duration-300 shadow-sm border-b border-transparent dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <x-ui.logo />
                <x-partials.navigation />
            </div>
        </div>
    </header>

    <main class="bg-main text-gray-800 dark:bg-slate-900 dark:text-gray-100 antialiased font-sans min-h-screen flex flex-col transition-colors duration-300">
        @yield('main-content')
    </main>
    
    <x-partials.footer />
@endsection

@section('components')
        {{-- Task Create/Edit Modal --}}
        <x-task.modal />

        <div 
            id="pomodoroModal" 
            class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 dark:bg-indigo-900/80 backdrop-blur-md transition-opacity p-4"
            x-data="pomodoroLogic()"
            x-show="isOpen"
            x-cloak
            style="display: none;"
            @keydown.escape.window="closeModal()"
        >
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 w-full max-w-sm text-center shadow-2xl border-4 border-white dark:border-slate-700 relative">
                
                <button @click="closeModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-1">Focus Mode! Go 🚀</h3>
                
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 truncate px-4 font-medium" x-text="taskName"></p>

                <div class="relative w-64 h-64 mx-auto mb-6 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="6" class="text-gray-100 dark:text-slate-700" />
                        <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round"
                                class="text-orange-500 transition-all duration-1000 ease-linear"
                                :stroke-dasharray="circumference"
                                :stroke-dashoffset="dashOffset" />
                    </svg>
                    
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <div class="text-5xl font-mono font-bold text-gray-800 dark:text-white tracking-tighter" x-text="formattedTime"></div>
                        <div class="text-xs font-semibold uppercase tracking-widest text-orange-500 mt-2" x-text="isRunning ? 'FOCUSING' : 'READY'"></div>
                    </div>
                </div>

                <div class="mb-6 h-10 transition-all" x-show="!isRunning" x-transition>
                    <div class="flex items-center justify-center gap-4">
                        <button @click="adjustTime(-5)" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-slate-700 hover:bg-gray-200">-5</button>
                        <div class="flex items-baseline gap-1">
                            <input type="number" x-model.number="inputMinutes" @input="updateTimeFromInput()" class="w-12 text-center text-xl font-bold bg-transparent border-b border-gray-300 focus:outline-none focus:border-orange-500 dark:text-white p-0">
                            <span class="text-sm text-gray-400">min</span>
                        </div>
                        <button @click="adjustTime(5)" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-slate-700 hover:bg-gray-200">+5</button>
                    </div>
                </div>

                <div class="flex justify-center gap-3">
                    <button @click="toggleTimer()" 
                            class="px-8 py-3 rounded-2xl text-white font-bold shadow-lg transition transform active:scale-95 flex items-center gap-2"
                            :class="isRunning ? 'bg-pink-400 hover:bg-pink-500' : 'bg-pink-500 hover:bg-pink-600 text-white text-sm sm:text-base font-semibold shadow-sm hover:shadow-md'">
                        <span x-text="isRunning ? '⏸ Pause' : '▶ Start Focus'"></span>
                    </button>
                    
                    <button @click="resetTimer()" x-show="timeLeft !== totalTime" 
                            class="px-4 py-3 rounded-2xl bg-gray-100 dark:bg-slate-700 text-gray-500 hover:bg-gray-200 transition">
                        ↺
                    </button>
                </div>
            </div>
        </div>

        <div id="dateSelectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#4A403A]/60 backdrop-blur-sm transition-opacity p-3 sm:p-4">
            <div class="bg-[#FAF7F2] dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-5 md:p-6 w-full max-w-sm smooth-shadow border-2 sm:border-4 border-white dark:border-slate-700">
                <h3 class="text-lg sm:text-xl font-serif font-semibold mb-3 sm:mb-4 text-gray-800 dark:text-white text-center">Jump to Date</h3>
                <div class="space-y-3 sm:space-y-4 mb-4 sm:mb-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-wider">Month</label>
                        <select id="selectMonth" class="w-full px-3 sm:px-4 py-2 sm:py-3 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl outline-none dark:text-white text-sm sm:text-base">
                            <option value="0">January</option> <option value="1">February</option> <option value="2">March</option>
                            <option value="3">April</option> <option value="4">May</option> <option value="5">June</option>
                            <option value="6">July</option> <option value="7">August</option> <option value="8">September</option>
                            <option value="9">October</option> <option value="10">November</option> <option value="11">December</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-wider">Year</label>
                        <input id="inputYear" type="number" class="w-full px-3 sm:px-4 py-2 sm:py-3 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl outline-none dark:text-white text-sm sm:text-base" placeholder="{{ date('Y') }}">
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3">
                    <button id="btnCloseDateModal" class="w-full sm:w-auto px-4 py-2 rounded-xl border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition text-sm sm:text-base">Cancel</button>
                    <button id="btnApplyDate" class="w-full sm:w-auto px-5 py-2 rounded-xl bg-pink-500 text-white hover:bg-pink-600 shadow-md font-bold transition text-sm sm:text-base">Go</button>
                </div>
            </div>
        </div>

        <div id="deleteModal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-red-900/20 backdrop-blur-sm transition-opacity p-3 sm:p-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-6 sm:p-8 w-full max-w-sm smooth-shadow border-2 sm:border-4 border-red-100 dark:border-red-900/50 text-center">
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 sm:h-8 sm:w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white mb-2">Delete Task?</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4 sm:mb-6 text-xs sm:text-sm px-2">Are you sure you want to delete this task? This action cannot be undone.</p>    
                <div class="flex flex-col sm:flex-row justify-center gap-2 sm:gap-3">
                    <button id="btnCancelDelete" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 font-medium transition text-sm sm:text-base">Cancel</button>
                    <button id="btnConfirmDelete" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-red-500 text-white hover:bg-red-600 font-bold shadow-lg transition text-sm sm:text-base">Delete</button>
                </div>
            </div>
        </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('pomodoroLogic', () => ({
                isOpen: false,
                taskId: null,
                taskName: 'Unknown Task',
                inputMinutes: 25,
                totalTime: 25 * 60,
                timeLeft: 25 * 60,
                isRunning: false,
                interval: null,
                circumference: 2 * Math.PI * 45, // ≈ 282.74

                // Khởi tạo: Gắn hàm openPomodoro vào window để bên ngoài gọi được
                init() {
                    window.openPomodoro = (id) => {
                        this.openWithTask(id);
                    }
                },

                // Hàm được gọi từ bên ngoài
                openWithTask(id) {
                    if (typeof window.tasks === 'undefined') {
                        console.error('Không tìm thấy biến window.tasks');
                        return;
                    }
                    const task = window.tasks.find(x => x.id === parseInt(id));
                    
                    this.taskId = id;
                    this.taskName = task ? task.title : 'Task ID: ' + id;
                    this.inputMinutes = 25; // Reset về mặc định
                    this.updateTimeFromInput();
                    this.isOpen = true;
                    this.isRunning = false;
                },

                // Các Getter tính toán hiển thị
                get formattedTime() {
                    const m = Math.floor(this.timeLeft / 60).toString().padStart(2, '0');
                    const s = (this.timeLeft % 60).toString().padStart(2, '0');
                    return `${m}:${s}`;
                },

                get dashOffset() {
                    // Tính toán độ dài vòng tròn còn lại
                    return this.circumference - ((this.timeLeft / this.totalTime) * this.circumference);
                },

                // Logic Timer
                updateTimeFromInput() {
                    if (this.inputMinutes < 1) this.inputMinutes = 1;
                    this.totalTime = this.inputMinutes * 60;
                    this.timeLeft = this.totalTime;
                },

                adjustTime(val) {
                    this.inputMinutes += val;
                    this.updateTimeFromInput();
                },

                toggleTimer() {
                    this.isRunning ? this.pause() : this.start();
                },

                start() {
                    if (this.timeLeft <= 0) return;
                    this.isRunning = true;
                    this.interval = setInterval(() => {
                        if (this.timeLeft > 0) {
                            this.timeLeft--;
                        } else {
                            this.finish();
                        }
                    }, 1000);
                },

                pause() {
                    this.isRunning = false;
                    clearInterval(this.interval);
                },

                resetTimer() {
                    this.pause();
                    this.timeLeft = this.totalTime;
                },

                finish() {
                    this.pause();
                    this.playSound(); // Gọi hàm âm thanh
                    
                    // Logic xác nhận hoàn thành (giữ nguyên logic cũ của bạn)
                    setTimeout(() => {
                        if (confirm("Time's up! 🎉\nMark task as Completed?")) {
                            if (typeof window.toggleComplete !== 'undefined') {
                                window.toggleComplete(this.taskId);
                            }
                            this.closeModal();
                        }
                    }, 100);
                },

                closeModal() {
                    this.pause();
                    this.isOpen = false;
                },

                playSound() {
                    // Giữ nguyên logic tạo âm thanh của bạn
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = audioCtx.createOscillator();
                    osc.connect(audioCtx.destination);
                    osc.start();
                    setTimeout(() => osc.stop(), 500);
                }
            }));
        });
    </script>

    <script>
        // --- TASK CARD SCRIPTS (Complete, Delete) ---
        // These functions interact with the global tasks array and apiCall function
        // Note: This script is placed here (not in card.blade.php) to avoid duplicate declarations
        // when multiple task cards are rendered in a loop
        
        // Toggle Complete Task
        async function toggleComplete(id){
            if (typeof window.tasks === 'undefined' || typeof window.apiCall === 'undefined') {
                console.error('Tasks array or apiCall function not found');
                return;
            }
            
            const t = window.tasks.find(x => x.id === parseInt(id));
            if(!t) return;

            // Lưu trạng thái ban đầu để rollback nếu lỗi
            const wasCompleted = t.is_completed;
            
            // Optimistic UI - Update ngay lập tức
            t.is_completed = !t.is_completed;
            t.completed = t.is_completed;
            
            // Update UI ngay lập tức
            const card = document.querySelector(`[data-task-id="${id}"]`);
            if (card) {
                const title = card.querySelector('h4');
                const desc = card.querySelector('p');
                const completeBtn = card.querySelector('.completeTaskBtn');
                
                if (title) {
                    if (t.is_completed) {
                        title.classList.add('line-through', 'opacity-70');
                    } else {
                        title.classList.remove('line-through', 'opacity-70');
                    }
                }
                
                if (desc) {
                    if (t.is_completed) {
                        desc.classList.add('opacity-70');
                    } else {
                        desc.classList.remove('opacity-70');
                    }
                }
                
                // Update button title
                if (completeBtn) {
                    completeBtn.setAttribute('title', t.is_completed ? 'Mark as pending' : 'Mark as done');
                }
                
                // Hide/show focus button
                const focusBtn = card.querySelector('button[onclick*="openPomodoro"]');
                if (focusBtn) {
                    if (t.is_completed) {
                        focusBtn.style.display = 'none';
                    } else {
                        focusBtn.style.display = 'block';
                    }
                }
            }
            
            // Update stats nếu có
            const statTotal = document.getElementById('statTotal');
            const statCompleted = document.getElementById('statCompleted');
            const statPending = document.getElementById('statPending');
            
            if (statTotal && statCompleted && statPending && window.tasks) {
                const total = window.tasks.length;
                const completed = window.tasks.filter(t => t.is_completed || t.completed).length;
                const pending = total - completed;
                
                statTotal.innerText = String(total).padStart(3, '0');
                statCompleted.innerText = String(completed).padStart(2, '0');
                statPending.innerText = String(pending).padStart(2, '0');
            }
            
            // Update overview stats (chart view)
            if (window.tasks && typeof window.renderChart === 'function') {
                window.renderChart();
            }
            
            if (typeof window.checkNotifications === 'function') {
                window.checkNotifications();
            }
            if (typeof window.renderTodaySchedule === 'function') {
                window.renderTodaySchedule();
            }
            
            try {
                await window.apiCall(`/tasks/${id}/toggle`, 'POST');
                // Không reload trang - UI đã được update rồi
            } catch (error) {
                // Rollback nếu lỗi
                t.is_completed = wasCompleted;
                t.completed = wasCompleted;
                
                if (card) {
                    const title = card.querySelector('h4');
                    const desc = card.querySelector('p');
                    const completeBtn = card.querySelector('.completeTaskBtn');
                    const focusBtn = card.querySelector('button[onclick*="openPomodoro"]');
                    
                    if (title) {
                        if (wasCompleted) {
                            title.classList.add('line-through', 'opacity-70');
                        } else {
                            title.classList.remove('line-through', 'opacity-70');
                        }
                    }
                    
                    if (desc) {
                        if (wasCompleted) {
                            desc.classList.add('opacity-70');
                        } else {
                            desc.classList.remove('opacity-70');
                        }
                    }
                    
                    if (completeBtn) {
                        completeBtn.setAttribute('title', wasCompleted ? 'Mark as pending' : 'Mark as done');
                    }
                    
                    if (focusBtn) {
                        if (wasCompleted) {
                            focusBtn.style.display = 'none';
                        } else {
                            focusBtn.style.display = 'block';
                        }
                    }
                }
                
                console.error('Failed to toggle task:', error);
                alert('Failed to update task. Please try again.');
            }
        }

        // Confirm Delete Task
        function confirmDelete(id){ 
            if (typeof window.deletingId === 'undefined') window.deletingId = null;
            window.deletingId = id; 
            const delModal = document.getElementById('deleteModal');
            if (delModal) {
                delModal.classList.remove('hidden'); 
                delModal.classList.add('flex');
            }
        }

        // Delete confirmation handlers
        document.addEventListener('DOMContentLoaded', function() {
            const delModal = document.getElementById('deleteModal');
            const btnCancelDelete = document.getElementById('btnCancelDelete');
            if (btnCancelDelete && delModal) {
                btnCancelDelete.addEventListener('click', () => { 
                    if (typeof window.deletingId !== 'undefined') window.deletingId = null; 
                    delModal.classList.add('hidden'); 
                    delModal.classList.remove('flex'); 
                });
            }

            const btnConfirmDelete = document.getElementById('btnConfirmDelete');
            if (btnConfirmDelete && delModal) {
                btnConfirmDelete.addEventListener('click', async () => {
                    if (typeof window.deletingId === 'undefined' || !window.deletingId) return;
                    
                    const id = window.deletingId;
                    
                    if (typeof window.tasks !== 'undefined') {
                        window.tasks = window.tasks.filter(t => t.id !== parseInt(id));
                    }
                    
                    if (typeof window.apiCall !== 'undefined') {
                        await window.apiCall(`/tasks/${id}`, 'DELETE');
                    }
                    
                    // Reload page to reflect changes
                    window.location.reload();
                });
            }
        });

        // Make functions globally available
        window.toggleComplete = toggleComplete;
        window.confirmDelete = confirmDelete;
    </script>
    @endsection