<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Command\Command as CommandAlias;

class LicensePingCommand extends Command
{
    protected $signature = 'license:ping';
    protected $description = 'Sends a periodic check-in to the license server.';

    public function handle(): int
    {
        $licenseKey = Setting::where('slug', 'license_key')->value('value');
        if (!$licenseKey) {
            return CommandAlias::SUCCESS;
        }

        try {
            Http::timeout(10)->post(config('services.license.server_url') . '/api/ping', [
                'purchase_code' => $licenseKey,
                'item_id'       => config('services.license.item_id'),
                'domain'        => request()->getHttpHost(),
            ]);
        } catch (\Exception $e) {
        }

        return CommandAlias::SUCCESS;
    }
}
