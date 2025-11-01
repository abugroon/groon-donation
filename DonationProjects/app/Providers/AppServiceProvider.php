<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            'auth' => fn () => [
                'user' => auth()->user(),
            ],
            'locale' => fn () => app()->getLocale(),
            'translations' => fn () => [
                'app' => [
                    'name' => config('app.name'),
                ],
                'navigation' => [
                    'dashboard' => __('dashboard.title'),
                    'projects' => __('projects.title'),
                ],
                'projects' => trans('projects'),
                'donations' => trans('donations'),
                'dashboard' => trans('dashboard'),
                'auth' => trans('auth'),
                'common' => trans('common'),
            ],
        ]);
    }
}
