@props(['tasks'])

<div id="listView" class="fade-in">
    <div id="taskList" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        @forelse($tasks as $task)
            <x-task-card :task="$task" />
        @empty
            <div class="col-span-1 md:col-span-2 text-center py-10">
                <div class="text-6xl mb-4">📝</div>
                <h3 class="text-xl font-serif text-gray-600 dark:text-gray-400">No tasks found</h3>
                <p class="text-sm text-gray-400">Create a new task to get started!</p>
            </div>
        @endforelse

    </div>

    <div class="flex justify-center mt-10 mb-8">
        <button id="btnLoadMore" class="px-6 py-2.5 bg-white dark:bg-slate-800 border border-gray-300 dark:border-slate-600 text-gray-600 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-50 dark:hover:bg-slate-700 transition shadow-sm">
            Load more
        </button>
    </div>
</div>