<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Throwable;

class SendPasswordResetCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $email
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $user = User::where('email', $this->email)->first();

            if (!$user) {
                Log::warning('Password reset code job: User not found', ['email' => $this->email]);
                return;
            }

            // Tạo mã xác nhận 6 chữ số
            $token = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Lưu token vào password_reset_tokens table
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->email],
                [
                    'token' => Hash::make($token),
                    'created_at' => Carbon::now(),
                ]
            );

            // Gửi email với mã xác nhận
            $user->notify(new PasswordResetNotification($token));
        } catch (Throwable $e) {
            Log::error('Failed to send password reset code', [
                'email' => $this->email,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);
            
            // Re-throw to trigger job retry
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(?Throwable $exception): void
    {
        Log::error('Send password reset code job failed after all retries', [
            'email' => $this->email,
            'exception' => $exception?->getMessage(),
        ]);
    }
}

