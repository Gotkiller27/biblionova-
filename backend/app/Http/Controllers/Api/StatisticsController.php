<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\StatisticsService;
use App\Helpers\ApiResponse;
use App\Http\Resources\ViewResource;
use App\Http\Resources\DownloadResource;

class StatisticsController extends Controller
{
    public function __construct(private StatisticsService $statisticsService) {}

    public function index()
    {
        $stats = $this->statisticsService->getGeneralStats();
        return ApiResponse::success($stats, 'General statistics retrieved successfully');
    }

    public function downloads()
    {
        $downloads = $this->statisticsService->getRecentDownloads();
        return ApiResponse::paginated($downloads, DownloadResource::class, 'Recent downloads retrieved successfully');
    }

    public function views()
    {
        $views = $this->statisticsService->getRecentViews();
        return ApiResponse::paginated($views, ViewResource::class, 'Recent views retrieved successfully');
    }
}
