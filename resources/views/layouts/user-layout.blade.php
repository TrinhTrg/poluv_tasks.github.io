@extends('layouts.base-layout')

@section('content')
    <header class="bg-header dark:bg-slate-800 sticky top-0 z-30 transition-all duration-300 shadow-sm border-b border-transparent dark:border-slate-700">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-18 md:h-20">
                <x-ui.logo />
                <x-partials.navigation />
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-0 sm:px-4 md:px-6 lg:px-8 py-3 sm:py-4 flex-grow flex flex-col overflow-visible">
        @yield('main-content')
    </main>
    
    <x-partials.footer />
@endsection

@section('components')
        <div id="modalBackdrop" class="fixed inset-0 z-50 hidden items-center justify-center bg-[#4A403A]/60 backdrop-blur-sm transition-opacity p-3 sm:p-4">
            <div class="bg-[#FAF7F2] dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-5 sm:p-6 md:p-8 w-full max-w-md smooth-shadow transform transition-all scale-100 border-2 sm:border-4 border-white dark:border-slate-700 max-h-[90vh] overflow-y-auto">
                <h3 id="modalTitle" class="text-xl sm:text-2xl font-serif font-semibold mb-4 sm:mb-6 text-gray-800 dark:text-white">Add New Task</h3>
                <form id="taskForm" class="space-y-3 sm:space-y-4">
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
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
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
                    <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3 mt-4 sm:mt-6 pt-3 sm:pt-4 border-t border-gray-200 dark:border-slate-700">
                        <button type="button" id="cancelModal" class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-700 font-medium transition">Cancel</button>
                        <button type="submit" class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-black dark:bg-indigo-600 text-white hover:bg-gray-800 dark:hover:bg-indigo-700 font-medium shadow-lg transition">Save Task</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="pomodoroModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-indigo-900/80 backdrop-blur-md transition-opacity p-3 sm:p-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl sm:rounded-3xl p-6 sm:p-8 w-full max-w-sm text-center shadow-2xl border-2 sm:border-4 border-indigo-200 dark:border-indigo-900">
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 text-2xl sm:text-3xl">⏰</div>
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 dark:text-white mb-2">Focus Mode</h3>
                <p id="pomoTaskTitle" class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-4 sm:mb-6 px-3 sm:px-4 truncate">Task Name Here</p>
                <div class="text-5xl sm:text-6xl font-mono font-bold text-gray-900 dark:text-white mb-4 sm:mb-6 tracking-widest" id="timerDisplay">25:00</div>
                <div id="timerInputs" class="flex justify-center gap-2 mb-4 sm:mb-6">
                    <input id="inputMin" type="number" min="1" max="60" value="25" class="w-14 sm:w-16 text-center border rounded-lg p-2 text-sm sm:text-base dark:bg-slate-700 dark:text-white" />
                    <span class="text-xl sm:text-2xl self-center">:</span>
                    <input id="inputSec" type="number" min="0" max="59" value="00" class="w-14 sm:w-16 text-center border rounded-lg p-2 text-sm sm:text-base dark:bg-slate-700 dark:text-white" />
                </div>
                <div class="flex flex-col sm:flex-row justify-center gap-2 sm:gap-3">
                    <button id="btnPomoCancel" class="px-4 py-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition text-sm sm:text-base">Cancel</button>
                    <button id="btnPomoStart" class="px-5 sm:px-6 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 shadow-lg font-bold transition text-sm sm:text-base">Start Focus</button>
                    <button id="btnPomoPause" class="hidden px-5 sm:px-6 py-2 rounded-xl bg-yellow-500 text-white hover:bg-yellow-600 shadow-lg font-bold transition text-sm sm:text-base">Pause</button>
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
    @endsection