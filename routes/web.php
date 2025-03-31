<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Event\RequestEvent;

//first page after booting server
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

//dashboard appears post user auth. We fetch the project data associated with the user
Route::get('dashboard', function (Request $request) {
    $user = $request->user();
    
    $projects = $user->projects()
        ->select('projects.id', 'projects.title', 'projects.description')
        ->with('tasks', 'tasks.assignee') 
        ->get();

    return Inertia::render('Dashboard', ['projects' => $projects, 'username'=> $user->name]);
})->middleware(['auth', 'verified'])->name('dashboard');

//When seeing more details abt project, we fetch and pass the tasks associated
Route::get('/projects/{project}', function (Request $request, Project $project) {
    
    $user = $request->user();
    $userProject = $user->projects()->find($project->id);

    if( !$userProject ) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $role = $userProject->pivot->role;

    $project->load('tasks.assignee'); // Load tasks with the project

    $project->pivot = ['role' => $role];
    
    return response()->json($project);
})->middleware(['auth', 'verified'])->name('project.show');

// //Fetch users associated with a project
// Route::get('/projects/{project}/users', function(Request $request, Project $project) {
    
//     $user = $request->user();
//     $userProject = $user->projects()->find($project->id);

//     if( !$userProject ) {
//         return response()->json(['error' => 'Unauthorized'], 403);
//     }

//     $users = $project->users()->select('id', 'name')->get();

//     return response()->json($users);
// })->middleware(['auth', 'verified']);

//When the tasks inside a project are updated, we update  task's order attribute inside the database
Route::post('/projects/{project}/update-task-order', [ProjectController::class, 'updateTaskOrder']);

//Get and post comments
Route::get('/tasks/{taskId}/comments', [CommentsController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('task.comments.index');

Route::post('/tasks/{taskId}/comments', [CommentsController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('task.comments.store');

Route::get('/users', [UserController::class, 'index']);

//Update task priority
Route::patch('/tasks/{taskId}', [TaskController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('task.update');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
