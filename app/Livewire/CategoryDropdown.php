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
        // Load categories từ database - giống logic trong CategoryController
        $userId = auth()->id();
        
        $this->categories = Category::query()
            ->when($userId !== null, function($q) use ($userId) {
                // Authenticated user: lấy categories của user đó
                return $q->where('user_id', $userId);
            }, function($q) {
                // Guest mode: lấy categories không có user_id hoặc user_id = 1
                return $q->where(function($query) {
                    $query->whereNull('user_id')
                          ->orWhere('user_id', 1);
                });
            })
            ->orderBy('name', 'asc')
            ->get();
        
        // Nếu chưa có categories, tạo default categories
        if ($this->categories->isEmpty()) {
            $defaultCategories = [
                ['name' => 'Work', 'color' => '#3B82F6'],
                ['name' => 'Homework', 'color' => '#EF4444'],
                ['name' => 'Personal', 'color' => '#10B981'],
                ['name' => 'Meeting', 'color' => '#F59E0B'],
                ['name' => 'Other', 'color' => '#6B7280'],
            ];
            
            $targetUserId = $userId ?? 1; // Guest dùng user_id = 1
            
            foreach ($defaultCategories as $default) {
                // Kiểm tra xem category đã tồn tại chưa (tránh duplicate)
                $existing = Category::where('user_id', $targetUserId)
                    ->where('name', $default['name'])
                    ->first();
                
                if (!$existing) {
                    $category = Category::create([
                        'user_id' => $targetUserId,
                        'name' => $default['name'],
                        'color' => $default['color'],
                    ]);
                    $this->categories->push($category);
                } else {
                    $this->categories->push($existing);
                }
            }
            
            // Reload lại sau khi tạo
            $this->categories = Category::query()
                ->when($userId !== null, function($q) use ($userId) {
                    return $q->where('user_id', $userId);
                }, function($q) {
                    return $q->where(function($query) {
                        $query->whereNull('user_id')
                              ->orWhere('user_id', 1);
                    });
                })
                ->orderBy('name', 'asc')
                ->get();
        }
        
        // Đảm bảo category có giá trị mặc định
        if (empty($this->category)) {
            $this->category = 'all';
        }
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
