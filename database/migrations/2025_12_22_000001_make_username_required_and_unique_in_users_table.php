<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any NULL usernames to a unique value
        // This handles existing data if any
        $usersWithNullUsername = DB::table('users')->whereNull('username')->get();
        foreach ($usersWithNullUsername as $index => $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['username' => 'user_' . $user->id . '_' . time() . '_' . $index]);
        }

        // Drop existing unique index if exists (to avoid conflicts)
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
        });

        // Modify the username column to be NOT NULL and add unique constraint
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false)->change();
            $table->unique('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->string('username')->nullable()->change();
            $table->unique('username'); // Keep unique even when nullable
        });
    }
};

