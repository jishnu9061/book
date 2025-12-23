<?php

namespace App\Jobs;

use App\Services\LicenseCore;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VerifyLicenseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $core = app(LicenseCore::class);
            
            // Verify license (this will make HTTP request and cache result)
            $isValid = $core->check();
            
            // Update verification timestamp
            Cache::put('license_last_verified_timestamp', now(), now()->addDays(2));
            
            // Update verification attempt timestamp (for grace period)
            Cache::put('license_verification_attempt', now(), now()->addMinutes(10));
            
            // Remove lock
            Cache::forget('license_verification_lock');
            
            if ($isValid) {
                Log::info('License verified successfully via background job');
            } else {
                Log::warning('License verification failed via background job');
            }
        } catch (\Exception $e) {
            Log::error('License verification job failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            
            // Remove lock on error
            Cache::forget('license_verification_lock');
            
            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(\Throwable $exception)
    {
        Log::error('License verification job failed permanently', [
            'exception' => $exception->getMessage()
        ]);
        
        // Remove lock on permanent failure
        Cache::forget('license_verification_lock');
    }
}



