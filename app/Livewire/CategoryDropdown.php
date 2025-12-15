<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;

class CategoryDropdown extends Component
{
    public $category = 'all'; // Giá trị mặc định
    public $categories = [];

    public function mount()
    {
        // Load categories từ database
        $this->categories = Category::where('user_id', 1) // Tạm thời hardcode, sau dùng auth()->id()
            ->orderBy('name', 'asc')
            ->get();
    }

    public function updatedCategory($value)
    {
        // Dispatch event để parent component biết category đã thay đổi
        $this->dispatch('category-changed', category: $value);
    }

    public function render()
    {
        return view('livewire.category-dropdown');
    }
}
