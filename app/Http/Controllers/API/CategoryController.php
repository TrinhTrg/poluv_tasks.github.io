<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * LẤY DANH SÁCH CHO DROPDOWN
     * GET /api/v1/categories
     */
    public function index()
    {
        // Với Dropdown, ta thường cần lấy HẾT danh mục để user chọn,
        // chứ không phân trang (paginate) vì dropdown khó bấm "Trang sau".
        
        $categories = Category::query()
            // 1. Chỉ lấy của User hiện tại (Tạm fix cứng là 1)
            ->where('user_id', 1) 
            
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

        // Gán cứng user_id = 1 (Sau này đổi thành auth()->id())
        $validated['user_id'] = 1;

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    /**
     * XEM CHI TIẾT (Nếu cần sửa)
     */
    public function show(Category $category)
    {
        // Bảo mật: Kiểm tra xem danh mục này có phải của User 1 không
        if ($category->user_id !== 1) {
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
        if ($category->user_id !== 1) {
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
        if ($category->user_id !== 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Lưu ý: Vì trong Migration ta để 'nullOnDelete' ở bảng Tasks,
        // nên khi xóa Category này, các Task thuộc về nó sẽ có category_id = NULL
        // chứ không bị xóa mất Task.
        $category->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}