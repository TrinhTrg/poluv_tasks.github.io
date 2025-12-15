<?php

namespace App\Livewire;

use Livewire\Component;

class AnalyticsRangeDropdown extends Component
{
    public $range = 'month';

    public function updatedRange($value)
    {
        $this->dispatch('analytics-range-changed', range: $value);
    }

    public function render()
    {
        return view('livewire.analytics-range-dropdown');
    }
}

