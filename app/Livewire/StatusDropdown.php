<?php

namespace App\Livewire;

use Livewire\Component;

class StatusDropdown extends Component
{
    public $status = 'all';

    public function mount()
    {
        // Đảm bảo status có giá trị mặc định
        if (empty($this->status)) {
            $this->status = 'all';
        }
    }

    public function updatedStatus($value)
    {
        // Dispatch event để parent component biết status đã thay đổi
        $this->dispatch('status-changed', status: $value);
    }

    public function render()
    {
        return view('livewire.status-dropdown');
    }
}

