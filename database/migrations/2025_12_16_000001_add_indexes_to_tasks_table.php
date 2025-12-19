<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Add indexes for frequently queried columns
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('tasks_is_completed_index');
            $table->dropIndex('tasks_due_at_index');
            $table->dropIndex('tasks_is_notified_index');
            $table->dropIndex('tasks_has_notify_index');
            $table->dropIndex('tasks_user_status_due_index');
            $table->dropIndex('tasks_notification_scan_index');
        });
    }
};

