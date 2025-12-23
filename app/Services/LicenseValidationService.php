<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LicenseValidationService
{
    private const LICENSE_CACHE_KEY = 'license_verification_status';
    private const LICENSE_KEY_CACHE_KEY = 'license_key';
    private const CACHE_DURATION_HOURS = 6;
    private const VERIFICATION_CACHE_DURATION_DAYS = 1;

    /**
     * Check if the application has a valid license
     *
     * @return bool
     */
    public function hasValidLicense(): bool
    {
        // Check if license is already verified (cached)
        if (Cache::get(self::LICENSE_CACHE_KEY)) {
            return true;
        }

        // Get license key
        $licenseKey = $this->getLicenseKey();
        if (empty($licenseKey)) {
            return false;
        }

        // Verify with server
        if ($this->verifyWithServer($licenseKey)) {
            Cache::put(self::LICENSE_CACHE_KEY, true, now()->addDays(self::VERIFICATION_CACHE_DURATION_DAYS));
            return true;
        }

        return false;
    }

    /**
     * Get the license key from cache or database
     *
     * @return string|null
     */
    public function getLicenseKey(): ?string
    {
        return Cache::remember(self::LICENSE_KEY_CACHE_KEY, now()->addHours(self::CACHE_DURATION_HOURS), function () {
            return Setting::where('slug', 'license_key')->value('value');
        });
    }

    /**
     * Get the activated domain
     *
     * @return string|null
     */
    public function getActivatedDomain(): ?string
    {
        return Setting::where('slug', 'license_domain')->value('value');
    }

    /**
     * Verify license with the license server
     *
     * @param string $licenseKey
     * @param string|null $domain
     * @return bool
     */
    public function verifyWithServer(string $licenseKey, ?string $domain = null): bool
    {
        try {
            $licenseServerUrl = config('services.license.server_url');
            $itemId = config('services.license.item_id');
            $currentDomain = $domain ?? request()->getHttpHost();

            if (empty($licenseServerUrl) || empty($itemId)) {
                Log::warning('License server configuration is missing', [
                    'server_url' => $licenseServerUrl,
                    'item_id' => $itemId
                ]);
                return false;
            }

            $response = Http::timeout(10)->post($licenseServerUrl . '/api/verify', [
                'purchase_code' => $licenseKey,
                'domain'        => $currentDomain,
                'item_id'       => $itemId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $isValid = isset($data['valid']) && $data['valid'] === true;
                
                if (!$isValid) {
                    Log::warning('License verification failed', [
                        'response' => $data,
                        'domain' => $currentDomain
                    ]);
                }
                
                return $isValid;
            }

            Log::warning('License verification request failed', [
                'status' => $response->status(),
                'response' => $response->body(),
                'domain' => $currentDomain
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('License verification error: ' . $e->getMessage(), [
                'domain' => $domain ?? request()->getHttpHost(),
                'exception' => $e
            ]);
            return false;
        }
    }

    /**
     * Activate license with the server
     *
     * @param string $purchaseCode
     * @param string|null $domain
     * @return array
     */
    public function activateLicense(string $purchaseCode, ?string $domain = null): array
    {
        try {
            $licenseServerUrl = config('services.license.server_url');
            $itemId = config('services.license.item_id');
            $currentDomain = $domain ?? request()->getHttpHost();

            if (empty($licenseServerUrl) || empty($itemId)) {
                return [
                    'success' => false,
                    'message' => 'License server configuration is missing'
                ];
            }

            $response = Http::timeout(10)->post($licenseServerUrl . '/api/activate', [
                'purchase_code' => $purchaseCode,
                'domain'        => $currentDomain,
                'item_id'       => $itemId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['valid']) && $data['valid'] === true) {
                    // Save license information
                    Setting::updateOrCreate(
                        ['slug' => 'license_key'],
                        ['value' => $purchaseCode]
                    );
                    Setting::updateOrCreate(
                        ['slug' => 'license_domain'],
                        ['value' => $currentDomain]
                    );

                    // Clear cache to force re-verification
                    $this->clearCache();

                    return [
                        'success' => true,
                        'message' => $data['message'] ?? 'License activated successfully'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $data['message'] ?? 'License activation failed'
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Failed to connect to license server'
            ];
        } catch (\Exception $e) {
            Log::error('License activation error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during license activation'
            ];
        }
    }

    /**
     * Deactivate license with the server
     *
     * @param string|null $domain
     * @return array
     */
    public function deactivateLicense(?string $domain = null): array
    {
        try {
            $licenseKey = $this->getLicenseKey();
            $currentDomain = $domain ?? $this->getActivatedDomain() ?? request()->getHttpHost();

            if (empty($licenseKey)) {
                return [
                    'success' => false,
                    'message' => 'No license key found'
                ];
            }

            $licenseServerUrl = config('services.license.server_url');
            $itemId = config('services.license.item_id');

            $response = Http::timeout(10)->post($licenseServerUrl . '/api/deactivate', [
                'purchase_code' => $licenseKey,
                'domain'        => $currentDomain,
                'item_id'       => $itemId,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['valid']) && $data['valid'] === true) {
                    // Remove license information
                    Setting::whereIn('slug', ['license_key', 'license_domain'])->delete();
                    $this->clearCache();

                    return [
                        'success' => true,
                        'message' => $data['message'] ?? 'License deactivated successfully'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => $data['message'] ?? 'License deactivation failed'
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Failed to connect to license server'
            ];
        } catch (\Exception $e) {
            Log::error('License deactivation error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during license deactivation'
            ];
        }
    }

    /**
     * Clear license-related cache
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget(self::LICENSE_CACHE_KEY);
        Cache::forget(self::LICENSE_KEY_CACHE_KEY);
    }

    /**
     * Get license information
     *
     * @return array
     */
    public function getLicenseInfo(): array
    {
        return [
            'has_license' => !empty($this->getLicenseKey()),
            'license_key' => $this->getLicenseKey(),
            'activated_domain' => $this->getActivatedDomain(),
            'is_verified' => $this->hasValidLicense(),
        ];
    }
}
