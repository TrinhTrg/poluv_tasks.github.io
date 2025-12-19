<?php

namespace App\Livewire;

use Livewire\Component;

class Search extends Component
{
    public $search = '';

    public function mount()
    {
        // Initialize search from query parameter if exists
        $this->search = request()->query('search', '');
    }

    public function updatedSearch()
    {
        // Dispatch event when search value changes
        $this->dispatch('search-changed', search: $this->search);
    }

    public function performSearch()
    {
        // Dispatch event when search button is clicked
        $this->dispatch('search-changed', search: $this->search);
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->dispatch('search-changed', search: '');
    }

    public function render()
    {
        return view('livewire.search');
    }
}

