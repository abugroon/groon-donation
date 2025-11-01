<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDonationRequest;
use App\Models\Donation;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function index(Project $project): JsonResponse
    {
        $donations = $project->donations()->latest()->get()->map(fn ($donation) => [
            'id' => $donation->id,
            'donor_name' => $donation->donor_name,
            'amount' => $donation->amount,
            'is_anonymous' => $donation->is_anonymous,
            'payment_method' => $donation->payment_method,
            'created_at' => $donation->created_at?->format('Y-m-d H:i'),
        ]);

        return response()->json($donations);
    }

    public function store(StoreDonationRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $donation = Donation::create($validated);

        $project = Project::find($validated['project_id']);
        $project->collected_amount += $validated['amount'];
        $project->progress = round(($project->collected_amount / $project->target_amount) * 100, 2);
        if ($project->progress >= 100) {
            $project->status = 'completed';
        } elseif ($project->progress > 0) {
            $project->status = 'in_progress';
        }
        $project->progress = min($project->progress, 100);
        $project->save();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('donations.messages.stored'),
                'donation' => [
                    'id' => $donation->id,
                    'project_id' => $donation->project_id,
                    'donor_name' => $donation->donor_name,
                    'amount' => $donation->amount,
                    'is_anonymous' => $donation->is_anonymous,
                    'payment_method' => $donation->payment_method,
                    'created_at' => $donation->created_at?->format('Y-m-d H:i'),
                ],
                'project' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'target_amount' => $project->target_amount,
                    'collected_amount' => $project->collected_amount,
                    'progress' => $project->progress,
                    'status' => $project->status,
                ],
            ], 201);
        }

        return back()->with('success', __('donations.messages.stored'));
    }
}
