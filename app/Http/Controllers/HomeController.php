<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Donation;
use App\Models\Project;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the public landing page for the donation platform.
     */
    public function __invoke(): Response
    {
        $featuredProjects = Project::query()
            ->orderByDesc('progress')
            ->orderBy('end_date')
            ->take(3)
            ->get();

        $stats = [
            'projects_total' => Project::count(),
            'projects_completed' => Project::where('status', 'completed')->count(),
            'donations_total' => Donation::count(),
            'donations_amount' => (float) Donation::sum('amount'),
        ];

        return Inertia::render('Home/Index', [
            'stats' => $stats,
            'featuredProjects' => ProjectResource::collection($featuredProjects)->resolve(),
            'translations' => [
                'hero_title' => __('home.hero_title'),
                'hero_subtitle' => __('home.hero_subtitle'),
                'primary_cta' => __('home.primary_cta'),
                'secondary_cta' => __('home.secondary_cta'),
                'stats_title' => __('home.stats_title'),
                'stats_projects' => __('home.stats_projects'),
                'stats_completed' => __('home.stats_completed'),
                'stats_donations' => __('home.stats_donations'),
                'stats_supporters' => __('home.stats_supporters'),
                'features_title' => __('home.features_title'),
                'features_subtitle' => __('home.features_subtitle'),
                'features' => trans('home.features'),
                'projects_title' => __('home.projects_title'),
                'projects_subtitle' => __('home.projects_subtitle'),
                'view_all_projects' => __('home.view_all_projects'),
                'testimonial_quote' => __('home.testimonial_quote'),
                'testimonial_author' => __('home.testimonial_author'),
                'contact_title' => __('home.contact_title'),
                'contact_description' => __('home.contact_description'),
                'contact_email_label' => __('home.contact_email_label'),
                'dashboard_label' => __('navigation.dashboard'),
                'projects_label' => __('navigation.projects'),
                'login_label' => __('navigation.login'),
                'register_label' => __('navigation.register'),
                'logout_label' => __('navigation.logout'),
                'progress_label' => __('projects.progress'),
                'collected_label' => __('projects.collected'),
                'target_label' => __('projects.target'),
                'empty_featured' => __('home.empty_featured'),
                'details_label' => __('home.details_label'),
            ],
        ]);
    }
}
