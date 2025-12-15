<?php

namespace App\Livewire;

use Livewire\Component;

class PriorityDropdown extends Component
{
    public $sort = 'newest';

    public function updatedSort($value)
    {
        $this->dispatch('sort-changed', sort: $value);
    }

    public function render()
    {
        return view('livewire.priority-dropdown');
    }
}