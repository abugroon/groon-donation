<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     */
    public function index(Request $request): JsonResponse|Response
    {
        $projects = Project::query()->latest('start_date')->get();
        $resources = ProjectResource::collection($projects);

        if ($request->wantsJson()) {
            return $resources->response();
        }

        return Inertia::render('Projects/Index', [
            'projects' => $resources->resolve(),
            'translations' => [
                'title' => __('projects.title'),
                'create' => __('projects.create'),
                'view' => __('projects.view'),
                'status_labels' => __('projects.statuses'),
                'progress_label' => __('projects.progress'),
                'target_label' => __('projects.target'),
                'collected_label' => __('projects.collected'),
                'empty' => __('projects.empty'),
                'dashboardTitle' => __('dashboard.title'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(): Response
    {
        return Inertia::render('Projects/Create', [
            'translations' => [
                'title' => __('projects.create'),
                'save' => __('projects.save'),
                'fields' => __('projects.fields'),
                'listTitle' => __('projects.title'),
                'cancel' => __('common.cancel'),
                'dashboardTitle' => __('dashboard.title'),
            ],
        ]);
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(StoreProjectRequest $request): JsonResponse|RedirectResponse
    {
        $data = $request->validated();
        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('projects', 'public');
        }

        $project = Project::create([
            ...$data,
            'image' => $imagePath,
            'collected_amount' => 0,
            'progress' => 0,
            'status' => 'open',
        ]);

        $resource = new ProjectResource($project);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('projects.created'),
                'data' => $resource->toArray($request),
            ], 201);
        }

        return redirect()
            ->route('projects.index')
            ->with('success', __('projects.created'));
    }

    /**
     * Display the specified project.
     */
    public function show(Request $request, Project $project): JsonResponse|Response
    {
        $project->load(['donations' => fn ($query) => $query->latest('created_at')]);
        $resource = new ProjectResource($project);

        if ($request->wantsJson()) {
            return $resource->response();
        }

        return Inertia::render('Projects/Show', [
            'project' => $resource->resolve(),
            'translations' => [
                'donations' => __('donations.list'),
                'donate' => __('donations.donate_now'),
                'anonymous_label' => __('donations.anonymous'),
                'amount_label' => __('donations.amount'),
                'donor_label' => __('donations.donor_name'),
                'payment_method_label' => __('donations.payment_method'),
                'status_labels' => __('projects.statuses'),
                'progress_label' => __('projects.progress'),
                'target_label' => __('projects.target'),
                'collected_label' => __('projects.collected'),
                'dashboardTitle' => __('dashboard.title'),
                'projectsTitle' => __('projects.title'),
                'donations_empty' => __('donations.empty'),
                'cancel' => __('common.cancel'),
                'fields' => __('projects.fields'),
            ],
        ]);
    }

    /**
     * Update the specified project in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse|RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($project->image) {
                Storage::disk('public')->delete($project->image);
            }

            $data['image'] = $request->file('image')->store('projects', 'public');
        }

        $project->fill($data);

        if ($project->isDirty('target_amount')) {
            $project->progress = $project->target_amount > 0
                ? round(min(100, ($project->collected_amount / $project->target_amount) * 100), 2)
                : 0;
        }

        if (! isset($data['status'])) {
            $project->status = $this->resolveStatus($project);
        }

        $project->save();

        $resource = new ProjectResource($project);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('projects.updated'),
                'data' => $resource->toArray($request),
            ]);
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', __('projects.updated'));
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Request $request, Project $project): JsonResponse|RedirectResponse
    {
        if ($project->image) {
            Storage::disk('public')->delete($project->image);
        }

        $project->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => __('projects.deleted'),
            ]);
        }

        return redirect()
            ->route('projects.index')
            ->with('success', __('projects.deleted'));
    }

    /**
     * Resolve the project status based on its progress.
     */
    private function resolveStatus(Project $project): string
    {
        return match (true) {
            $project->progress >= 100 => 'completed',
            $project->progress > 0 => 'in_progress',
            default => 'open',
        };
    }
}
