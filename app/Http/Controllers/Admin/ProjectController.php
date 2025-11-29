<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function show(Project $project): View
    {
        $project->load(['bankAccounts', 'approvedDonations']);

        $accountSummaries = DB::table('donations')
            ->select('bank_account_id', DB::raw('COUNT(*) as donations_count'), DB::raw('SUM(amount) as total_amount'))
            ->where('project_id', $project->id)
            ->where('status', 'approved')
            ->groupBy('bank_account_id')
            ->get();

        return view('admin.projects.show', [
            'project' => $project,
            'accountSummaries' => $accountSummaries,
        ]);
    }
}
