<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\LicenseCore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class LicenseController extends Controller
{
    protected $core;

    public function __construct(LicenseCore $core)
    {
        $this->core = $core;
    }

    public function showActivationForm()
    {
        return Inertia::render('License/Activate', [
            'message' => session('license_error')
        ]);
    }

    public function activate(Request $request)
    {
        $request->validate(['purchase_code' => 'required|string']);

        $result = $this->core->activate($request->purchase_code);

        if ($result['success']) {
            Artisan::call('cache:clear');
            return Redirect::to('/dashboard')->with('success', $result['message']);
        }

        return Redirect::route('license.show')->with('license_error', $result['message']);
    }

    public function showSettings()
    {
        $licenseInfo = $this->core->info();

        if (!$licenseInfo['has_license']) {
            return Redirect::route('license.show');
        }

        return Inertia::render('License/Settings', [
            'title'           => 'License Settings',
            'activatedDomain' => $licenseInfo['activated_domain'],
            'licenseKey'      => $licenseInfo['license_key'],
            'isVerified'      => $licenseInfo['is_verified'],
            'error'           => session('error'),
        ]);
    }

    public function deactivate(Request $request)
    {
        if (config('app.demo')) {
            return Redirect::back()->with('error', 'License deactivation is disabled in demo mode.');
        }

        $result = $this->core->deactivate();

        if ($result['success']) {
            Artisan::call('cache:clear');
            return Redirect::route('license.show')->with('license_error', $result['message']);
        }

        return Redirect::route('license.settings')->with('error', $result['message']);
    }
}
