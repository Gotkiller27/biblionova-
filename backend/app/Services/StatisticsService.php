<?php

namespace App\Services;

use App\Models\User;
use App\Models\Reference;
use App\Models\DepositRequest;
use App\Models\View;
use App\Models\Download;

class StatisticsService
{
    public function getAdminDashboardStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_references' => Reference::count(),
            'total_deposit_requests' => DepositRequest::count(),
            'published_references' => Reference::published()->count(),
            'total_views' => Reference::sum('view_count'),
            'total_downloads' => Reference::sum('download_count'),
        ];
    }

    public function getBibliothecaireDashboardStats(User $bibliothecaire): array
    {
        return [
            'created_references' => Reference::where('bibliothecaire_id', $bibliothecaire->id)->count(),
            'published_references' => Reference::where('bibliothecaire_id', $bibliothecaire->id)->published()->count(),
            'archived_references' => Reference::where('bibliothecaire_id', $bibliothecaire->id)->archived()->count(),
        ];
    }

    public function getResponsableDashboardStats(User $responsable): array
    {
        return [
            'assigned_requests' => DepositRequest::where('assigned_manager_id', $responsable->id)->count(),
            'approved_requests' => DepositRequest::where('assigned_manager_id', $responsable->id)
                ->whereIn('status', ['approved_by_manager', 'approved', 'published'])->count(),
            'rejected_requests' => DepositRequest::where('assigned_manager_id', $responsable->id)
                ->where('status', 'rejected_by_manager')->count(),
        ];
    }

    public function getGeneralStats(): array
    {
        return [
            'total_references' => Reference::count(),
            'total_views' => Reference::sum('view_count'),
            'total_downloads' => Reference::sum('download_count'),
            'published_references' => Reference::published()->count(),
            'draft_references' => Reference::draft()->count(),
            'archived_references' => Reference::archived()->count(),
        ];
    }

    public function getRecentViews(int $limit = 20)
    {
        return View::with(['user', 'reference'])->latest()->paginate($limit);
    }

    public function getRecentDownloads(int $limit = 20)
    {
        return Download::with(['user', 'reference'])->latest()->paginate($limit);
    }
}
