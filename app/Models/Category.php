<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    // 1. TẮT TIMESTAMPS (Do bảng categories của bạn không có cột created_at, updated_at)
    public $timestamps = false;

    // 2. Các trường được phép nhập dữ liệu
    protected $fillable = [
        'user_id', // Danh mục thuộc về ai
        'name',    // Tên danh mục (Work, Personal...)
        'color'    // Mã màu (#FF0000)
    ];

    // 3. Quan hệ: Một Category thuộc về 1 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 4. Quan hệ: Một Category có nhiều Task
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}