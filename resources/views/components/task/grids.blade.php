@props(['tasks'])

<div id="listView" class="fade-in">
    <div id="taskList" class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 md:gap-6">
        
        @forelse($tasks as $task)
            <x-task.card :task="$task" />
        @empty
            @guest
                {{-- Landing Page Content for Guest --}}
                <div class="col-span-1 md:col-span-2 text-center py-12 sm:py-16 md:py-20">
                    <div class="text-6xl sm:text-7xl md:text-8xl mb-6 sm:mb-8">📋✨</div>
                    <h2 class="text-3xl sm:text-4xl md:text-5xl font-serif text-gray-900 dark:text-white mb-4 sm:mb-6">
                        Welcome to PoLuv Tasks
                    </h2>
                    <p class="text-lg sm:text-xl text-gray-600 dark:text-gray-400 mb-8 sm:mb-10 max-w-2xl mx-auto px-4">
                        Your personal task management companion. Organize your life, boost your productivity, and achieve your goals.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                        <a href="{{ route('login') }}" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-3 rounded-xl font-semibold transition shadow-lg">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-800 dark:text-white px-6 py-3 rounded-xl font-semibold transition">
                            Sign Up
                        </a>
                    </div>
                </div>
            @else
                <div class="col-span-1 md:col-span-2 text-center py-8 sm:py-10">
                    <div class="text-5xl sm:text-6xl mb-3 sm:mb-4">📝</div>
                    <h3 class="text-lg sm:text-xl font-serif text-gray-600 dark:text-gray-400">No tasks found</h3>
                    <p class="text-xs sm:text-sm text-gray-400">Create a new task to get started!</p>
            </div>
            @endguest
        @endforelse

    </div>

    <div class="flex justify-center mt-6 sm:mt-8 md:mt-10 mb-6 sm:mb-8">
        <button id="btnLoadMore" class="px-5 sm:px-6 py-2 sm:py-2.5 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-gray-300 text-sm sm:text-base font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition shadow-sm hidden">
            Load more
        </button>
    </div>
</div>