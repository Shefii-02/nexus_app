<?php
namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService)
    {
    }

    /**
     * Single combined payload for the admin dashboard.
     * GET /api/dashboard-status
     */
    public function status(): JsonResponse
    {
        $data = $this->dashboardService->getDashboardSummary();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
