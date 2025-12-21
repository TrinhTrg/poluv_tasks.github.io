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
            
            // Notification status: đã gửi thông báo hay chưa
            $table->boolean('is_notified')->default(false);
            
            // Trạng thái hoàn thành
            $table->boolean('is_completed')->default(false);

            $table->timestamps();

            // Indexes for frequently queried columns
            $table->index('is_completed', 'tasks_is_completed_index');
            $table->index('due_at', 'tasks_due_at_index');
            $table->index('is_notified', 'tasks_is_notified_index');
            $table->index('has_notify', 'tasks_has_notify_index');
            
            // Composite index for common query pattern: user_id + is_completed + due_at
            // Used in: filtering by user, status, and sorting by due date
            $table->index(['user_id', 'is_completed', 'due_at'], 'tasks_user_status_due_index');
            
            // Composite index for notification scanning: is_notified + is_completed + has_notify + due_at
            // Used in: ScanDueTasks command
            $table->index(['is_notified', 'is_completed', 'has_notify', 'due_at'], 'tasks_notification_scan_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};