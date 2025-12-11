@props(['task'])

@php
    use Carbon\Carbon;

    // 1. Định nghĩa màu sắc và label cho Category (giống hệt trong JS cũ)
    $categoryMeta = [
        'Work'     => ['label' => '💼 Work', 'card' => 'bg-green-200 text-green-900', 'badge' => 'bg-green-100 text-green-800'],
        'Homework' => ['label' => '📚 Homework', 'card' => 'bg-blue-200 text-blue-900', 'badge' => 'bg-blue-100 text-blue-700'],
        'Meeting'  => ['label' => '🗣️ Meeting', 'card' => 'bg-red-200 text-red-900', 'badge' => 'bg-red-100 text-red-700'],
        'Personal' => ['label' => '👤 Personal', 'card' => 'bg-yellow-200 text-yellow-900', 'badge' => 'bg-yellow-100 text-yellow-800'],
        'Other'    => ['label' => '📦 Other', 'card' => 'bg-purple-200 text-purple-900', 'badge' => 'bg-purple-100 text-purple-800'],
    ];

    $catRaw = $task->category;
    $cat = 'Other'; // Mặc định

    if (is_object($catRaw)) {
        // Trường hợp 1: Nếu là Relationship (Model), lấy tên cột (thường là 'name' hoặc 'title')
        // Bạn hãy kiểm tra trong bảng categories của bạn cột tên là gì. Ở đây tôi giả định là 'name'
        $cat = $catRaw->name ?? $catRaw->title ?? 'Other';
    } elseif (is_string($catRaw)) {
        // Trường hợp 2: Nếu lưu dạng chuỗi trực tiếp trong bảng tasks
        $cat = $catRaw;
    }

    // Đảm bảo key tồn tại trong mảng màu sắc
    $meta = $categoryMeta[$cat] ?? $categoryMeta['Other'];

    // -----------------------------

    // 2. Xử lý thời gian (Giữ nguyên)
    $startTime = $task->start_time ? Carbon::parse($task->start_time)->format('h:i A') : '';
    $dueTime   = $task->due_time ? Carbon::parse($task->due_time)->format('h:i A') : '';
    
    $timeRange = '--';
    if ($startTime && $dueTime) $timeRange = "$startTime - $dueTime";
    elseif ($startTime) $timeRange = $startTime;
    elseif ($dueTime) $timeRange = $dueTime;

    // 3. Xử lý Overdue (Giữ nguyên)
    $isOverdue = false;
    $countdown = '';
    
    if ($task->due_date && !$task->completed) {
        $due = Carbon::parse($task->due_date);
        $now = Carbon::now();
        if ($now->gt($due) && $now->diffInHours($due) < 24) {
            $isOverdue = true;
            $resetPoint = $now->copy()->endOfDay(); 
            $diff = $resetPoint->diff($now);
            $countdown = $diff->format('%Hh%Im');
        }
    }

    // 4. Class động (Giữ nguyên)
    $cardClass = $isOverdue 
        ? 'bg-red-100 text-red-900 border border-red-200' 
        : "{$meta['card']} border border-white/70";
        
    $timePillClass = $isOverdue 
        ? 'bg-red-200/80 text-red-900' 
        : 'bg-white/60 text-gray-700';

    $titleClass = $task->completed 
        ? 'font-bold text-lg truncate w-full line-through opacity-70' 
        : 'font-bold text-lg truncate w-full';
        
    $descClass = $task->completed 
        ? 'text-sm opacity-70 truncate' 
        : 'text-sm truncate';
@endphp

<div class="relative p-5 rounded-3xl smooth-shadow hover:-translate-y-0.5 transition-transform group flex flex-col gap-3 min-h-[140px] {{ $cardClass }}">
    <div class="flex items-center justify-between gap-3">
        <div class="flex flex-col gap-1 w-full overflow-hidden">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $meta['badge'] }}">
                    {{ $meta['label'] }}
                </span>
                @if($isOverdue)
                    <span class="text-xs font-semibold text-red-600">Overdue • resets in {{ $countdown }}</span>
                @endif
            </div>
            <h4 class="{{ $titleClass }}" title="{{ $task->title }}">
                {{ $task->title }}
            </h4>
        </div>
        
        @if(!$task->completed)
            <button type="button" onclick="openPomodoro('{{ $task->id }}')" class="absolute top-4 right-4 text-gray-600 hover:text-gray-900 transition" title="Start Focus">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
        @endif
    </div>

    <p class="{{ $descClass }}">{{ $task->desc }}</p>

    <div class="flex justify-between items-center mt-auto">
        <div class="flex items-center gap-1.5 px-3 py-1 rounded-lg {{ $timePillClass }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-xs font-medium tracking-wide">{{ $timeRange }}</span>
        </div>

        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
            <button onclick="openEditModal('{{ $task->id }}')" class="editTaskBtn bg-white/50 text-gray-700 p-1.5 rounded-lg hover:bg-white transition" title="Edit">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            </button>
            <button onclick="toggleComplete('{{ $task->id }}')" class="completeTaskBtn bg-white/50 text-gray-700 p-1.5 rounded-lg hover:bg-white transition" title="Toggle Complete">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </button>
            <button onclick="confirmDelete('{{ $task->id }}')" class="deleteTaskBtn bg-white/50 text-gray-700 p-1.5 rounded-lg hover:bg-white transition" title="Delete">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3H4" />
                </svg>
            </button>
        </div>
    </div>
</div>