<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\StatisticsService;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private StatisticsService $statisticsService) {}

    public function admin()
    {
        $this->authorize('admin-only');
        $stats = $this->statisticsService->getAdminDashboardStats();
        return ApiResponse::success($stats, 'Admin dashboard stats retrieved successfully');
    }

    public function bibliothecaire()
    {
        $this->authorize('bibliothecaire-only');
        $stats = $this->statisticsService->getBibliothecaireDashboardStats(Auth::user());
        return ApiResponse::success($stats, 'Bibliothecaire dashboard stats retrieved successfully');
    }

    public function responsable()
    {
        $this->authorize('responsable-only');
        $stats = $this->statisticsService->getResponsableDashboardStats(Auth::user());
        return ApiResponse::success($stats, 'Responsable dashboard stats retrieved successfully');
    }
}
