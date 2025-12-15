<?php

namespace App\Livewire;

use Livewire\Component;

class StatusDropdown extends Component
{
    public $status = 'all';

    public function updatedStatus($value)
    {
        $this->dispatch('status-changed', status: $value);
    }

    public function render()
    {
        return view('livewire.status-dropdown');
    }
}

