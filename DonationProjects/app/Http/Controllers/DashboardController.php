<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Project;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $projectsCount = Project::count();
        $totalCollected = Project::sum('collected_amount');
        $totalTarget = Project::sum('target_amount');
        $completion = $totalTarget > 0 ? round(($totalCollected / $totalTarget) * 100, 2) : 0;
        $recentDonations = Donation::latest()->take(5)->get()->map(fn ($donation) => [
            'id' => $donation->id,
            'donor_name' => $donation->donor_name,
            'amount' => $donation->amount,
            'payment_method' => $donation->payment_method,
            'is_anonymous' => $donation->is_anonymous,
            'created_at' => $donation->created_at?->format('Y-m-d H:i'),
        ]);

        return Inertia::render('Dashboard/Index', [
            'projectsCount' => $projectsCount,
            'totalCollected' => $totalCollected,
            'totalTarget' => $totalTarget,
            'overallProgress' => $completion,
            'recentDonations' => $recentDonations,
        ]);
    }
}
