<?php

namespace App\Http\Controllers;

use App\Http\Requests\Donation\StoreDonationRequest;
use App\Http\Resources\DonationResource;
use App\Models\Donation;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    /**
     * Display a listing of donations for a specific project.
     */
    public function index(Project $project): JsonResponse
    {
        $donations = $project->donations()->latest('created_at')->get();

        return DonationResource::collection($donations)->response();
    }

    /**
     * Store a newly created donation in storage.
     */
    public function store(StoreDonationRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();

        $donation = DB::transaction(function () use ($data) {
            /** @var Project $project */
            $project = Project::lockForUpdate()->findOrFail($data['project_id']);

            $donation = Donation::create($data);

            $project->collected_amount = $project->collected_amount + $data['amount'];
            $project->progress = round(min(100, ($project->collected_amount / $project->target_amount) * 100), 2);
            $project->status = $project->progress >= 100 ? 'completed' : ($project->progress > 0 ? 'in_progress' : 'open');
            $project->save();

            return $donation;
        });

        $project = Project::findOrFail($data['project_id']);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('donations.created'),
                'data' => new DonationResource($donation),
                'project_progress' => $project->progress,
            ], 201);
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', __('donations.created'));
    }
}
