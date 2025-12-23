<?php

namespace App\Http\Controllers;

use App\Services\KernelStatusService; // <-- Import the service

class ReportController extends Controller
{
    protected $licenseService;

    // Inject the service via the constructor
    public function __construct(KernelStatusService $licenseService)
    {
        $this->licenseService = $licenseService;
    }

    public function generateMonthlyReport()
    {
        // ** The Secondary Check **
        if (!$this->licenseService->isVerified()) {
            // You can abort, throw an exception, or return a specific error view.
            // Aborting is simple and effective.
            abort(403, 'License Not Verified. This feature is disabled.');
        }

        // --- Your normal report generation logic continues here ---
        // ...
        // return $pdf->download('monthly-report.pdf');
    }
}
