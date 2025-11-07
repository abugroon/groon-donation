<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Donation;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display aggregated statistics for the donation platform.
     */
    public function __invoke(Request $request): JsonResponse|Response
    {
        $stats = [
            'projects_total' => Project::count(),
            'projects_completed' => Project::where('status', 'completed')->count(),
            'projects_in_progress' => Project::where('status', 'in_progress')->count(),
            'donations_total' => Donation::count(),
            'donations_amount' => (float) Donation::sum('amount'),
        ];

        $totals = Project::selectRaw('SUM(target_amount) as target, SUM(collected_amount) as collected')->first();
        $stats['overall_progress'] = ($totals?->target ?? 0) > 0
            ? round(min(100, (($totals->collected ?? 0) / $totals->target) * 100), 2)
            : 0;

        $recentProjects = ProjectResource::collection(
            Project::query()->latest()->limit(3)->get(),
        );

        if ($request->wantsJson()) {
            return response()->json([
                'stats' => $stats,
                'recent_projects' => $recentProjects->resolve(),
            ]);
        }

        return Inertia::render('Dashboard/Index', [
            'stats' => $stats,
            'recentProjects' => $recentProjects->resolve(),
            'translations' => [
                'title' => __('dashboard.title'),
                'projects_total' => __('dashboard.projects_total'),
                'projects_completed' => __('dashboard.projects_completed'),
                'projects_in_progress' => __('dashboard.projects_in_progress'),
                'donations_total' => __('dashboard.donations_total'),
                'donations_amount' => __('dashboard.donations_amount'),
                'overall_progress' => __('dashboard.overall_progress'),
                'recent_projects' => __('dashboard.recent_projects'),
                'recent_projects_empty' => __('dashboard.recent_projects_empty'),
                'status_labels' => __('projects.statuses'),
            ],
        ]);
    }
}
