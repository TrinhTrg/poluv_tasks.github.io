<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) { // Laravel dùng số nhiều: 'tasks'
            $table->id();
            
            // Khóa ngoại liên kết với bảng users       
            // Trong hình là 'users_id', nhưng chuẩn Laravel là 'user_id'
            // constrained('users') nghĩa là nó sẽ tự hiểu liên kết với id của bảng users
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('title'); // title VARCHAR(255)
            $table->text('description')->nullable(); // description TEXT
            $table->date('due_date')->nullable(); // due_date DATE
            
            // priority TINYINT (1: Low, 2: Medium, 3: High)
            $table->tinyInteger('priority')->default(2); 
            
            // is_completed TINYINT (Laravel dùng boolean sẽ tạo ra tinyint(1) trong MySQL)
            $table->boolean('is_completed')->default(false); 
            
            // has_notify TINYINT
            $table->boolean('has_notify')->default(false);

            $table->timestamps(); // created_at, updated_at DATETIME
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
