<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Get current user ID or null for guest
     */
    protected function getUserId()
    {
        return Auth::check() ? Auth::id() : null;
    }
    /**
     * LẤY DANH SÁCH CHO DROPDOWN
     * GET /api/v1/categories
     */
    public function index()
    {
        // Với Dropdown, ta thường cần lấy HẾT danh mục để user chọn,
        // chứ không phân trang (paginate) vì dropdown khó bấm "Trang sau".
        
        $userId = $this->getUserId();
        
        $categories = Category::query()
            // 1. Lấy categories của User hiện tại hoặc guest
            ->when($userId !== null, function($q) use ($userId) {
                return $q->where('user_id', $userId);
            }, function($q) {
                // Guest mode: lấy categories không có user_id hoặc user_id = 1
                return $q->where(function($query) {
                    $query->whereNull('user_id')
                          ->orWhere('user_id', 1);
                });
            }) 
            
            // 2. Sắp xếp: Cái nào mới tạo lên đầu
            ->orderBy('id', 'desc')
            
            // 3. Lấy toàn bộ (Dùng get() thay vì paginate())
            ->get(); 

        return response()->json($categories);
    }

    /**
     * TẠO DANH MỤC MỚI
     * POST /api/v1/categories
     * Body: { "name": "Gym", "color": "#FF0000" }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7', // Mã màu Hex (VD: #FF0000)
        ]);

        // Gán user_id: null cho guest, Auth::id() cho authenticated
        $validated['user_id'] = $this->getUserId();

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    /**
     * XEM CHI TIẾT (Nếu cần sửa)
     */
    public function show(Category $category)
    {
        $userId = $this->getUserId();
        // Check ownership
        if ($userId !== null && $category->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($userId === null && $category->user_id !== null && $category->user_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return response()->json($category);
    }

    /**
     * CẬP NHẬT DANH MỤC
     * PUT /api/v1/categories/{id}
     */
    public function update(Request $request, Category $category)
    {
        $userId = $this->getUserId();
        // Check ownership
        if ($userId !== null && $category->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($userId === null && $category->user_id !== null && $category->user_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $category->update($validated);

        return response()->json($category);
    }

    /**
     * XÓA DANH MỤC
     * DELETE /api/v1/categories/{id}
     */
    public function destroy(Category $category)
    {
        $userId = $this->getUserId();
        // Check ownership
        if ($userId !== null && $category->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($userId === null && $category->user_id !== null && $category->user_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Lưu ý: Vì trong Migration ta để 'nullOnDelete' ở bảng Tasks,
        // nên khi xóa Category này, các Task thuộc về nó sẽ có category_id = NULL
        // chứ không bị xóa mất Task.
        $category->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}