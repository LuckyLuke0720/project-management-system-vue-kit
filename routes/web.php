<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CommentsController;
use Inertia\Inertia;

//first page after booting server
Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

//dashboard appears post user auth. We fetch the project data associated with the user
Route::get('dashboard', function (Request $request) {
    $user = $request->user();
    
    $projects = $user->projects()
        ->select('projects.id', 'projects.title', 'projects.description')
        ->with('tasks') 
        ->get();

    return Inertia::render('Dashboard', ['projects' => $projects]);
})->middleware(['auth', 'verified'])->name('dashboard');

//When seeing more details abt project, we fetch and pass the tasks associated
Route::get('/projects/{project}', function (Project $project) {
    
    $project->load('tasks'); // Load tasks with the project
    
    return response()->json($project);
})->middleware(['auth', 'verified'])->name('project.show');

//When the tasks inside a project are updated, we update  task's order attribute inside the database
Route::post('/projects/{project}/update-task-order', [ProjectController::class, 'updateTaskOrder']);

//Get and post comments
Route::get('/tasks/{taskId}/comments', [CommentsController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('task.comments.index');

Route::post('/tasks/{taskId}/comments', [CommentsController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('task.comments.store');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
