<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the user's dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get projects the user is assigned to or owns
        $projects = $user->allProjects()
            ->with(['owner:id,name', 'members:id,name', 'tasks'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
            
        // Get tasks assigned to the user
        $tasks = $user->assignedTasks()
            ->with('project:id,title')
            ->where('status', '!=', 'Completed')
            ->orderBy('due_date')
            ->orderBy('priority', 'desc')
            ->take(10)
            ->get();
            
        // Get project statistics
        $projectStats = [
            'total' => $user->allProjects()->count(),
            'completed_tasks' => Task::whereIn('project_id', $user->allProjects()->pluck('id'))
                                    ->where('status', 'Completed')
                                    ->count(),
            'pending_tasks' => Task::whereIn('project_id', $user->allProjects()->pluck('id'))
                                    ->where('status', '!=', 'Completed')
                                    ->count(),
        ];
        
        return Inertia::render('Dashboard', [
            'projects' => $projects,
            'tasks' => $tasks,
            'projectStats' => $projectStats,
        ]);
    }
}