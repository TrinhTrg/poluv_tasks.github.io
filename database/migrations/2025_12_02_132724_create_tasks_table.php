<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            // 1. LIÊN KẾT:
            // User: Bắt buộc (Task phải của ai đó)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Category: Có thể để trống (Nullable) nếu user không chọn danh mục
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();

            // 2. NỘI DUNG:
            $table->string('title');
            $table->text('description')->nullable();

            // 3. THỜI GIAN (Ngày + Giờ):
            // Thay vì chỉ lưu date, ta lưu dateTime để khớp với giao diện có chọn giờ
            $table->dateTime('start_at')->nullable(); 
            $table->dateTime('due_at')->nullable();   

            // 4. GIAO DIỆN & CẤU HÌNH:
            // Color Tag: Lưu mã màu Hex (VD: #EF4444)
            $table->string('color')->nullable(); 
            
            // Priority: 1=Low, 2=Medium, 3=High
            $table->tinyInteger('priority')->default(2); 
            
            // Toggle thông báo (ON/OFF)
            $table->boolean('has_notify')->default(false);
            
            // Trạng thái hoàn thành
            $table->boolean('is_completed')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};