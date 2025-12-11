<x-base-layout>
    <header class="bg-header dark:bg-slate-800 sticky top-0 z-30 transition-all duration-300 shadow-sm border-b border-transparent dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <x-logo />
                <x-navigation />
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex-grow flex flex-col">
        {{ $slot }}
    </main>

    <footer class="border-t border-gray-200 dark:border-slate-700 bg-[#F2EAEA] dark:bg-slate-900 py-6 mt-0 transition-colors">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex items-center justify-center gap-2 mb-2">
                <span class="font-sans font-bold text-gray-700 dark:text-gray-300">PoLuv Tasks</span>
            </div>
            <p class="text-gray-500 text-sm">© {{ date('Y') }} PoLuv Tasks. All Rights Reserved.</p>
        </div>
    </footer>

    @section('components')
        <div id="modalBackdrop" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#4A403A]/60 backdrop-blur-sm transition-opacity">
            <div class="bg-[#FAF7F2] dark:bg-slate-800 rounded-3xl p-8 w-full max-w-md smooth-shadow transform transition-all scale-100 border-4 border-white dark:border-slate-700 max-h-[90vh] overflow-y-auto">
                <h3 id="modalTitle" class="text-2xl font-serif font-semibold mb-6 text-gray-800 dark:text-white">Add New Task</h3>
                <form id="taskForm" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-wider">Title</label>
                        <input id="taskTitle" class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl focus:border-pink-300 dark:text-white outline-none transition" placeholder="e.g. Learn React" required />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-wider">Description</label>
                        <textarea id="taskDesc" rows="2" class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl focus:border-pink-300 dark:text-white outline-none transition resize-none" placeholder="Details..."></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-wider">Category</label>
                        <div class="relative">
                            <select id="taskCategory" class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl appearance-none outline-none dark:text-white cursor-pointer">
                                <option value="Work">💼 Work</option>
                                <option value="Homework">📚 Homework</option>
                                <option value="Meeting">🗣️ Meeting</option>
                                <option value="Personal">👤 Personal</option>
                                <option value="Other">📦 Other</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-wider">Start</label>
                            <div class="flex flex-col gap-2">
                                <input id="taskStartDate" type="date" class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:text-white dark:scheme-dark" />
                                <input id="taskStartTime" type="time" class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:text-white dark:scheme-dark" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-wider">Due</label>
                            <div class="flex flex-col gap-2">
                                <input id="taskDueDate" type="date" class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:text-white dark:scheme-dark" />
                                <input id="taskDueTime" type="time" class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:text-white dark:scheme-dark" />
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 tracking-wider">Color Tag</label>
                        <div id="colorPickerContainer" class="flex flex-wrap gap-3 justify-start"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-wider">Priority</label>
                        <select id="taskPriority" class="w-full px-3 py-2 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl text-sm dark:text-white outline-none">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between pt-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Remind me (24h alert)</span>
                        <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="taskNotify" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 dark:bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-slate-700">
                        <button type="button" id="cancelModal" class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 font-medium transition">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-black dark:bg-indigo-600 text-white hover:bg-gray-800 dark:hover:bg-indigo-700 font-medium shadow-lg transition">Save Task</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="pomodoroModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-indigo-900/80 backdrop-blur-md transition-opacity">
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 w-full max-w-sm text-center shadow-2xl border-4 border-indigo-200 dark:border-indigo-900">
                <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">⏰</div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">Focus Mode</h3>
                <p id="pomoTaskTitle" class="text-sm text-gray-500 dark:text-gray-400 mb-6 px-4 truncate">Task Name Here</p>
                <div class="text-6xl font-mono font-bold text-gray-900 dark:text-white mb-6 tracking-widest" id="timerDisplay">25:00</div>
                <div id="timerInputs" class="flex justify-center gap-2 mb-6">
                    <input id="inputMin" type="number" min="1" max="60" value="25" class="w-16 text-center border rounded-lg p-2 dark:bg-slate-700 dark:text-white" />
                    <span class="text-2xl self-center">:</span>
                    <input id="inputSec" type="number" min="0" max="59" value="00" class="w-16 text-center border rounded-lg p-2 dark:bg-slate-700 dark:text-white" />
                </div>
                <div class="flex justify-center gap-3">
                    <button id="btnPomoCancel" class="px-4 py-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition">Cancel</button>
                    <button id="btnPomoStart" class="px-6 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg font-bold transition">Start Focus</button>
                    <button id="btnPomoPause" class="hidden px-6 py-2 rounded-xl bg-yellow-500 text-white hover:bg-yellow-600 shadow-lg font-bold transition">Pause</button>
                </div>
            </div>
        </div>

        <div id="dateSelectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#4A403A]/60 backdrop-blur-sm transition-opacity">
            <div class="bg-[#FAF7F2] dark:bg-slate-800 rounded-3xl p-6 w-full max-w-sm smooth-shadow border-4 border-white dark:border-slate-700">
                <h3 class="text-xl font-serif font-semibold mb-4 text-gray-800 dark:text-white text-center">Jump to Date</h3>
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-wider">Month</label>
                        <select id="selectMonth" class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl outline-none dark:text-white">
                            <option value="0">January</option> <option value="1">February</option> <option value="2">March</option>
                            <option value="3">April</option> <option value="4">May</option> <option value="5">June</option>
                            <option value="6">July</option> <option value="7">August</option> <option value="8">September</option>
                            <option value="9">October</option> <option value="10">November</option> <option value="11">December</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1 tracking-wider">Year</label>
                        <input id="inputYear" type="number" class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-gray-200 dark:border-slate-600 rounded-xl outline-none dark:text-white" placeholder="{{ date('Y') }}">
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button id="btnCloseDateModal" class="px-4 py-2 rounded-xl border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition">Cancel</button>
                    <button id="btnApplyDate" class="px-5 py-2 rounded-xl bg-pink-500 text-white hover:bg-pink-600 shadow-md font-bold transition">Go</button>
                </div>
            </div>
        </div>

        <div id="deleteModal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-red-900/20 backdrop-blur-sm transition-opacity">
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 w-full max-w-sm smooth-shadow border-4 border-red-100 dark:border-red-900/50 text-center">
                <div class="w-14 h-14 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Delete Task?</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm">Are you sure you want to delete this task? This action cannot be undone.</p>    
                <div class="flex justify-center gap-3">
                    <button id="btnCancelDelete" class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 font-medium transition">Cancel</button>
                    <button id="btnConfirmDelete" class="px-5 py-2.5 rounded-xl bg-red-500 text-white hover:bg-red-600 font-bold shadow-lg transition">Delete</button>
                </div>
            </div>
        </div>
    @endsection

</x-base-layout>