<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id', // Mới
        'title',
        'description',
        'start_at',    // Mới
        'due_at',      // Mới (thay due_date)
        'color',       // Mới
        'priority',
        'has_notify',
        'is_completed'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'has_notify'   => 'boolean',
        'start_at'     => 'datetime', // Tự động chuyển về dạng ngày giờ thông minh
        'due_at'       => 'datetime',
        'priority'     => 'integer',
    ];
    
    // ... Các hàm relation user() và category() giữ nguyên ...
    public function user() { return $this->belongsTo(User::class); }
    public function category() { return $this->belongsTo(Category::class); }
}