<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\UserController;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function (Request $request) {

    $user = $request->user(); //Get auth user
    $projects = $user->projects()->get() ?? [];// '??[]' always produces an array

    return Inertia::render('Dashboard', ['projects' => $projects]);
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/', [WelcomeController::class, 'welcome'])->name('welcomeView');

// Route::get('/project', [ProjectController::class, 'index'])->name('project.index');
// Route::get('/project/create', [ProjectController::class, 'create'])->name('project.create');
// Route::post('/project', [ProjectController::class, 'store'])->name('project.store');
// Route::get('/project/{id}', [ProjectController::class, 'show'])-> name('project.show');
// Route::get('/project/{id}/edit', [ProjectController::class, 'edit'])-> name('project.edit');
// Route::put('/project/{id}', [ProjectController::class,'update'])->name('project.update');
// Route::delete('/project/{id}', [ProjectController::class, 'delete'])->name('project.delete');

//Route::resource('project', ProjectController::class); #equivalent with the 7 rows above

// Route::resource('user', UserController::class);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
