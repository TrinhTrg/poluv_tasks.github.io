@props(['task'])

@php
    use Carbon\Carbon;

    // 1. Màu mặc định theo Category
    $categoryColors = [
        'Work'     => 'bg-green-200 text-green-900 border-green-200',
        'Homework' => 'bg-blue-200 text-blue-900 border-blue-200',
        'Meeting'  => 'bg-red-200 text-red-900 border-red-200',
        'Personal' => 'bg-yellow-200 text-yellow-900 border-yellow-200',
        'Other'    => 'bg-purple-200 text-purple-900 border-purple-200',
    ];
    
    // Lấy tên category
    $catName = optional($task->category)->name ?? 'Other';
    
    // 2. Nếu Task có màu riêng (do user chọn), dùng màu đó
    if ($task->color) {
        $customStyle = "background-color: {$task->color}; color: #1e293b; border-color: {$task->color};";
        $cardClass = "border border-white/70";
    } else {
        $customStyle = "";
        $cardClass = ($categoryColors[$catName] ?? $categoryColors['Other']) . " border border-white/70";
    }

    // Xử lý Overdue (Quá hạn)
    $isOverdue = !$task->is_completed && $task->due_at && Carbon::parse($task->due_at)->isPast();
    if ($isOverdue) {
        $cardClass = 'bg-red-50 text-red-900 border-red-200';
        $customStyle = "";
    }

    // Thời gian
    $timeDisplay = '';
    if ($task->start_at || $task->due_at) {
        $start = $task->start_at ? Carbon::parse($task->start_at)->format('h:i A') : '';
        $end   = $task->due_at ? Carbon::parse($task->due_at)->format('h:i A') : '';
        $timeDisplay = ($start && $end) ? "$start - $end" : ($start ?: $end);
    }

    // Category Icons
    $categoryIcons = [
        'Work'     => '💼',
        'Homework' => '📚',
        'Meeting'  => '🗣️',
        'Personal' => '👤',
        'Other'    => '📦',
    ];
    $catIcon = $categoryIcons[$catName] ?? '📦';

    // Badge colors for category
    $badgeColors = [
        'Work'     => 'bg-green-100 text-green-800',
        'Homework' => 'bg-blue-100 text-blue-700',
        'Meeting'  => 'bg-red-100 text-red-700',
        'Personal' => 'bg-yellow-100 text-yellow-800',
        'Other'    => 'bg-purple-100 text-purple-800',
    ];
    $badgeClass = $badgeColors[$catName] ?? $badgeColors['Other'];
@endphp

<div class="relative p-5 rounded-3xl smooth-shadow hover:-translate-y-0.5 transition-transform group flex flex-col gap-3 min-h-[140px] {{ $cardClass }}"
     style="{{ $customStyle }}">
    
    {{-- Header: Category, Title & Focus Button --}}
    <div class="flex items-center justify-between gap-3">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2 flex-wrap">
                {{-- Badge Category với icon --}}
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badgeClass }}">
                    {{ $catIcon }} {{ $catName }}
                </span>
                
                @if($isOverdue)
                    <span class="text-xs font-bold text-red-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Overdue
                    </span>
                @endif
            </div>
            
            <h4 class="font-bold text-lg truncate w-full {{ $task->is_completed ? 'line-through opacity-70' : '' }}" 
                title="{{ $task->title }}">
                {{ $task->title }}
            </h4>
        </div>

        {{-- Focus Button (chỉ hiện khi task chưa completed) --}}
        @if(!$task->is_completed)
            <button 
                type="button"
                class="absolute top-4 right-4 text-gray-600 hover:text-gray-900 transition" 
                title="Start Focus">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
        @endif
    </div>

    {{-- Body: Description --}}
    <p class="text-sm truncate {{ $task->is_completed ? 'opacity-70' : '' }}">
        {{ $task->description }}
    </p>

    {{-- Footer: Time & Actions --}}
    <div class="flex justify-between items-center mt-auto">
        {{-- Time Display --}}
        <div class="flex items-center gap-1.5 px-3 py-1 rounded-lg {{ $isOverdue ? 'bg-red-200/80 text-red-900' : 'bg-white/60 text-gray-700' }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-xs font-medium tracking-wide">{{ $timeDisplay ?: '--:-- - --:--' }}</span>
        </div>

        {{-- Action Buttons (hiện khi hover) --}}
        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
            {{-- Edit --}}
            <button 
                wire:click="$dispatch('open-modal', { id: '{{ $task->id }}' })" 
                class="bg-white/50 text-gray-700 p-1.5 rounded-lg hover:bg-white transition shadow-sm" 
                title="Edit">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            </button>

            {{-- Complete/Uncomplete --}}
            <button 
                wire:click="toggleComplete({{ $task->id }})" 
                class="bg-white/50 text-gray-700 p-1.5 rounded-lg hover:bg-white transition shadow-sm" 
                title="{{ $task->is_completed ? 'Mark as pending' : 'Mark as done' }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </button>

            {{-- Delete --}}
            <x-task.actions :taskId="$task->id" />
        </div>
    </div>
</div>