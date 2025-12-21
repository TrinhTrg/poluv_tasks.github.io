@props(['task'])

@php
    use Carbon\Carbon;

    // Lấy tên category
    $catName = optional($task->category)->name ?? 'Other';
    
    // Chỉ dùng màu từ task->color (color tag do user chọn), không fallback về category
    if ($task->color) {
        $customStyle = "background-color: {$task->color}; color: #1e293b; border-color: {$task->color};";
        $cardClass = "border border-white/70";
    } else {
        // Nếu không có color tag, dùng màu mặc định (màu trung tính)
        $customStyle = "";
        $cardClass = "bg-gray-50 text-gray-900 border border-gray-200";
    }

    // Xử lý Overdue (Quá hạn) - Override màu nếu task quá hạn
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

    // Badge colors for category (chỉ dùng cho badge nhỏ, không phải background card)
    $badgeColors = [
        'Work'     => 'bg-green-100 text-green-800',
        'Homework' => 'bg-blue-100 text-blue-700',
        'Meeting'  => 'bg-red-100 text-red-700',
        'Personal' => 'bg-yellow-100 text-yellow-800',
        'Other'    => 'bg-purple-100 text-purple-800',
    ];
    $badgeClass = $badgeColors[$catName] ?? $badgeColors['Other'];

    // Priority display
    $priorityValue = $task->priority ?? 2; // Default medium
    $priorityLabels = [
        1 => __('priority.low'),
        2 => __('priority.medium'),
        3 => __('priority.high')
    ];
    $priorityColors = [
        1 => 'bg-gray-100 text-gray-700 border-gray-300', // Low
        2 => 'bg-blue-100 text-blue-700 border-blue-300', // Medium
        3 => 'bg-red-100 text-red-700 border-red-300', // High
    ];
    $priorityLabel = $priorityLabels[$priorityValue] ?? __('priority.medium');
    $priorityBadgeClass = $priorityColors[$priorityValue] ?? $priorityColors[2];

@endphp

<div class="relative p-5 rounded-3xl smooth-shadow hover:-translate-y-0.5 transition-transform group flex flex-col gap-3 min-h-[140px] {{ $cardClass }}"
     style="{{ $customStyle }}"
     data-task-id="{{ $task->id }}">
    
    {{-- Header: Category, Title & Complete Button --}}
    <div class="flex items-center justify-between gap-3">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2 flex-wrap">
                {{-- Badge Category với icon --}}
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badgeClass }}">
                    {{ $catIcon }} {{ $catName }}
                </span>
                
                {{-- Priority Badge --}}
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full border {{ $priorityBadgeClass }}">
                    {{ $priorityLabel }}
                </span>
                
                @if($isOverdue)
                    <span class="text-xs font-bold text-red-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('common.overdue') }}
                    </span>
                @endif
            </div>
            
            <h4 class="font-bold text-lg truncate w-full {{ $task->is_completed ? 'line-through opacity-70' : '' }}" 
                title="{{ $task->title }}">
                {{ $task->title }}
            </h4>
        </div>

        {{-- Complete/Uncomplete Button (góc phải trên) --}}
        @auth
            <button 
                onclick="toggleComplete({{ $task->id }})" 
                class="absolute top-4 right-4 bg-white/50 dark:bg-slate-700/50 text-gray-700 dark:text-gray-300 p-2 rounded-lg hover:bg-white dark:hover:bg-slate-600 transition shadow-sm completeTaskBtn" 
                title="{{ $task->is_completed ? __('task.mark_as_pending') : __('task.mark_as_done') }}">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </button>
        @endauth
    </div>

    {{-- Body: Description --}}
    @php
        $description = $task->description ?? '';
        $maxChars = 20; // Giới hạn 20 ký tự
        $truncatedDescription = '';

        // Dùng mb_strlen để đếm độ dài chuỗi (hỗ trợ tiếng Việt)
        if (mb_strlen($description) > $maxChars) {
            // Dùng mb_substr để cắt chuỗi an toàn
            $truncatedDescription = mb_substr($description, 0, $maxChars) . '...';
        } else {
            $truncatedDescription = $description;
        }
    @endphp
    <p class="text-sm line-clamp-2 {{ $task->is_completed ? 'opacity-70' : '' }}" 
       style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;"
       title="{{ $task->description }}">
        {{ $truncatedDescription }}
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

        {{-- Action Buttons (hiện khi hover) - Chỉ hiển thị cho authenticated users --}}
        @auth
            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none group-hover:pointer-events-auto">
            {{-- Edit --}}
            <button 
                    onclick="openEditModal({{ $task->id }})" 
                    class="bg-white/50 text-gray-700 p-1.5 rounded-lg hover:bg-white transition shadow-sm pointer-events-auto" 
                title="{{ __('task.edit_button') }}">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
            </button>

            {{-- Focus Button --}}
            @if(!$task->is_completed)
                <button 
                    type="button"
                    onclick="openPomodoro({{ $task->id }})"
                    class="bg-white/50 text-gray-700 p-1.5 rounded-lg hover:bg-white transition shadow-sm pointer-events-auto focusTaskBtn" 
                    title="{{ __('task.start_focus') }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </button>
            @endif

            {{-- Delete --}}
            <x-task.actions :taskId="$task->id" />
        </div>
        @endauth
    </div>
</div>