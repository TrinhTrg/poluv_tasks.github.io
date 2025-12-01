<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // 1. Khai báo bảng (nếu tên model số ít, Laravel tự hiểu bảng số nhiều 'tasks', nhưng khai báo cho chắc)
    protected $table = 'tasks';

    // 2. Khai báo Fillable: Cho phép điền dữ liệu vào các cột này
    protected $fillable = [
        'user_id',       // Quan trọng: Task phải thuộc về ai đó
        'title',
        'description',
        'due_date',
        'priority',      // 1: Low, 2: Medium, 3: High
        'is_completed',
        'has_notify'
    ];

    // 3. Khai báo Casts: Ép kiểu dữ liệu khi lấy từ DB ra
    // Ví dụ: trong DB lưu là 0/1 (TinyInt), nhưng code lấy ra sẽ là true/false (Boolean)
    protected $casts = [
        'is_completed' => 'boolean',
        'has_notify'   => 'boolean',
        'due_date'     => 'date',
        'priority'     => 'integer',
    ];

    // 4. Khai báo mối quan hệ: Task này thuộc về 1 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}   