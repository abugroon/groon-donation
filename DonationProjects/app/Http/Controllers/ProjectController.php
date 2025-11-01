<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): JsonResponse|Response
    {
        $projects = Project::withCount('donations')->orderByDesc('created_at')->paginate(10)->through(fn ($project) => [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'target_amount' => $project->target_amount,
            'collected_amount' => $project->collected_amount,
            'progress' => $project->progress,
            'status' => $project->status,
            'image' => $project->image,
            'start_date' => optional($project->start_date)->format('Y-m-d'),
            'end_date' => optional($project->end_date)->format('Y-m-d'),
        ]);

        if ($request->wantsJson()) {
            return response()->json($projects);
        }

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Projects/Create');
    }

    public function store(StoreProjectRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['image'])) {
            $data['image'] = Storage::url($data['image']);
        }

        $data['collected_amount'] = $data['collected_amount'] ?? 0;
        $data['progress'] = round(($data['collected_amount'] / max($data['target_amount'], 1)) * 100, 2);
        $data['status'] = $data['status'] ?? 'open';
        if ($data['progress'] >= 100) {
            $data['status'] = 'completed';
        } elseif ($data['progress'] > 0 && $data['status'] === 'open') {
            $data['status'] = 'in_progress';
        }
        $data['progress'] = min($data['progress'], 100);

        $project = Project::create($data);

        $projectResource = [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'target_amount' => $project->target_amount,
            'collected_amount' => $project->collected_amount,
            'progress' => $project->progress,
            'status' => $project->status,
            'image' => $project->image,
            'start_date' => optional($project->start_date)->format('Y-m-d'),
            'end_date' => optional($project->end_date)->format('Y-m-d'),
        ];

        if ($request->wantsJson()) {
            return response()->json($projectResource, 201);
        }

        return redirect()->route('projects.index')->with('success', __('projects.messages.created'));
    }

    public function show(Request $request, Project $project): JsonResponse|Response
    {
        $project->load('donations');
        $projectResource = [
            'id' => $project->id,
            'name' => $project->name,
            'description' => $project->description,
            'target_amount' => $project->target_amount,
            'collected_amount' => $project->collected_amount,
            'progress' => $project->progress,
            'status' => $project->status,
            'image' => $project->image,
            'start_date' => optional($project->start_date)->format('Y-m-d'),
            'end_date' => optional($project->end_date)->format('Y-m-d'),
        ];

        $donations = $project->donations()->latest()->get()->map(fn ($donation) => [
            'id' => $donation->id,
            'donor_name' => $donation->donor_name,
            'amount' => $donation->amount,
            'is_anonymous' => $donation->is_anonymous,
            'payment_method' => $donation->payment_method,
            'created_at' => $donation->created_at?->format('Y-m-d H:i'),
        ]);

        if ($request->wantsJson()) {
            return response()->json(array_merge($projectResource, ['donations' => $donations]));
        }

        return Inertia::render('Projects/Show', [
            'project' => $projectResource,
            'donations' => $donations,
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse|RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['image'])) {
            $data['image'] = Storage::url($data['image']);
        }

        $project->update($data);

        if (array_key_exists('collected_amount', $data) || array_key_exists('target_amount', $data)) {
            $project->progress = round(($project->collected_amount / max($project->target_amount, 1)) * 100, 2);
            if ($project->progress >= 100) {
                $project->status = 'completed';
            } elseif ($project->progress > 0 && $project->status === 'open') {
                $project->status = 'in_progress';
            }
            $project->progress = min($project->progress, 100);
            $project->save();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'target_amount' => $project->target_amount,
                'collected_amount' => $project->collected_amount,
                'progress' => $project->progress,
                'status' => $project->status,
                'image' => $project->image,
                'start_date' => optional($project->start_date)->format('Y-m-d'),
                'end_date' => optional($project->end_date)->format('Y-m-d'),
            ]);
        }

        return redirect()->back()->with('success', __('projects.messages.updated'));
    }

    public function destroy(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        $project->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => __('projects.messages.deleted')]);
        }

        return redirect()->route('projects.index')->with('success', __('projects.messages.deleted'));
    }

    public function edit(Project $project): Response
    {
        return Inertia::render('Projects/Edit', [
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'target_amount' => $project->target_amount,
                'collected_amount' => $project->collected_amount,
                'status' => $project->status,
                'start_date' => optional($project->start_date)->format('Y-m-d'),
                'end_date' => optional($project->end_date)->format('Y-m-d'),
                'image' => $project->image,
            ],
        ]);
    }
}
