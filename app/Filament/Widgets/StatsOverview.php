<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Project;
use App\Models\User;
use App\Models\Task;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProjects = Project::count();
        $totalUsers = User::count();
        $totalTasks = Task::count();

        // projects that have at least one task that is not Completed (does not count projects with NO tasks)
        $ongoingProjects = Project::whereHas('tasks', fn ($query) => 
            $query->where('status', '!=', 'Completed'))->count();

        $completedTasks = Task::where('status', 'Completed')->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) . '%' : '0%';

        return [
            Stat::make('Total Projects', $totalProjects)
                ->description('All active projects')
                ->icon('heroicon-o-folder'),

            Stat::make('Ongoing Projects', $ongoingProjects)
                ->description('Projects with unfinished tasks')
                ->icon('heroicon-o-clipboard-document-list'),

            Stat::make('Total Users', $totalUsers)
                ->description('Registered users in the system')
                ->icon('heroicon-o-users'),

            Stat::make('Total Tasks', $totalTasks)
                ->description("Completed: $completedTasks ($completionRate)")
                ->icon('heroicon-o-check-circle'),

        ];
    }
}