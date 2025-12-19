<?php

namespace App\Livewire;
use App\Models\Task;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class PriorityDropdown extends Component
{
    public $sort = 'newest';

    public function mount()
    {
        // Đảm bảo sort có giá trị mặc định
        if (empty($this->sort)) {
            $this->sort = 'newest';
        }
    }

    public function updatedSort($value)
    {
        // Dispatch event để parent component biết sort đã thay đổi
        $this->dispatch('sort-changed', sort: $value);
    }

    public function render()
    {
        // Component chỉ cần render view, không cần query tasks
        // Filtering được thực hiện client-side trong homepage.blade.php
        return view('livewire.priority-dropdown');
    }
}