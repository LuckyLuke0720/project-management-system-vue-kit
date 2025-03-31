<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use App\Models\Comments;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalProjects = Project::count();
        $totalUsers = User::count();
        $totalTasks = Task::count();
        $totalComments = Comments::count();
        $topCommenter = User::withCount('comments')->orderByDesc('comments_count')->first();


        // projects that have at least one task that is not Completed (does not count projects with NO tasks)
        $ongoingProjects = Project::whereHas('tasks', fn ($query) => 
            $query->where('status', '!=', 'Completed'))->count();

        $completedTasks = Task::where('status', 'Completed')->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) . '%' : '0%';

        if ($topCommenter) {
            $topCommenterName = $topCommenter->name;
            $topCommenterDesc = "{$topCommenter->comments_count} comments";
        } else {
            $topCommenterName = "No comments yet";
            $topCommenterDesc = "";
        }
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
            
            Stat::make('Total Comments', $totalComments)
                ->description('All user comments in the system')
                ->icon('heroicon-o-chat-bubble-left-ellipsis'),

                Stat::make('Top Commenter', $topCommenterName)
                ->description($topCommenterDesc)
                ->icon('heroicon-o-user'),
        ];
    }
}