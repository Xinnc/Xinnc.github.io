<?php

namespace App\Providers;

use App\Domains\Project\Model\Project;
use App\Domains\Project\Policies\ProjectPolicy;
use App\Domains\Task\Model\Task;
use App\Domains\Task\Policies\TaskPolicy;
use App\Domains\TimeEntry\Model\TimeEntry;
use App\Domains\TimeEntry\Policies\TimeEntryPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(Project::class, ProjectPolicy::class);
        Gate::policy(Task::class, TaskPolicy::class);
        Gate::policy(TimeEntry::class, TimeEntryPolicy::class);
    }
}
