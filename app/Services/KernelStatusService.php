<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class KernelStatusService
{
    /**
     * Checks if the license is verified.
     * Relies on the cache set by the AppIntegrityValidator middleware.
     * This is a very fast check.
     *
     * @return bool
     */
    public function isVerified(): bool
    {
        return (bool) Cache::get('license_verification_status', false);
    }
}
