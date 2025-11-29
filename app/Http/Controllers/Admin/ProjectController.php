<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function show(Request $request, Project $project): Response
    {
        $project->load(['bankAccounts', 'approvedDonations']);

        $accountSummaries = DB::table('donations')
            ->select('bank_account_id', DB::raw('COUNT(*) as donations_count'), DB::raw('SUM(amount) as total_amount'))
            ->where('project_id', $project->id)
            ->where('status', 'approved')
            ->groupBy('bank_account_id')
            ->get();

        $projectResource = new ProjectResource($project);

        $accountSummaryData = $accountSummaries->map(fn ($row) => [
            'bank_account_id' => $row->bank_account_id,
            'donations_count' => (int) $row->donations_count,
            'total_amount' => (float) $row->total_amount,
        ])->values();

        if ($request->wantsJson()) {
            return response()->json([
                'project' => $projectResource->resolve(),
                'accountSummaries' => $accountSummaryData,
            ]);
        }

        return Inertia::render('Admin/Projects/Show', [
            'project' => $projectResource->resolve(),
            'accountSummaries' => $accountSummaryData,
            'translations' => [
                'title' => __('projects.title'),
                'summary' => __('projects.bank_summary_title'),
                'sql_examples' => __('projects.sql_examples'),
                'fields' => __('projects.fields'),
                'statuses' => __('projects.statuses'),
                'labels' => [
                    'target' => __('projects.target'),
                    'collected' => __('projects.collected'),
                    'progress' => __('projects.progress'),
                    'status' => __('donations.status_label'),
                    'total_received' => __('projects.total_received'),
                    'donations_count' => __('projects.donations_count'),
                ],
                'dashboardTitle' => __('dashboard.title'),
                'projectsTitle' => __('projects.title'),
            ],
        ]);
    }
}
