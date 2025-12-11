<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Contracts\Database\Query\Builder;

class TaskController extends Controller
{
    public function index()
    {
        $query = Task::query()
            // 1. Chỉ lấy Task của User hiện tại (tạm thời gán cứng là 1)
            ->where('user_id', 1) 
            
            // 2. TÌM KIẾM TASK (Search bar chính)
            ->when(request('search'), function(Builder $query, $search) {
                // Tìm trong Title HOẶC Description
                return $query->where(function($q) use ($search) {
                    $q->where('title', 'like', '%'.$search.'%')
                      ->orWhere('description', 'like', '%'.$search.'%');
                });
            })

            // 3. LỌC THEO CATEGORY (Cái Dropdown "All Categories")
            // Nếu gửi lên ?category_id=5 thì chỉ hiện task của danh mục số 5
            ->when(request('category_id'), function($query, $catId) {
                return $query->where('category_id', $catId);
            })

            // 4. LỌC THEO STATUS (Cái Dropdown "All Status")
            // ?status=completed hoặc ?status=pending
            ->when(request('status'), function($query, $status) {
                if ($status === 'completed') return $query->where('is_completed', true);
                if ($status === 'pending') return $query->where('is_completed', false);
            })

            // 5. Sắp xếp
            ->latest('id');

        // Kèm theo thông tin Category (để hiển thị màu sắc, tên danh mục trên thẻ Task)
        return $query->with('category')->simplePaginate(10);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
