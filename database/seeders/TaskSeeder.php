<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // Motivation: Clear existing tasks to prevent duplicate seeding
        Task::query()->delete();

        // Motivation: Explicitly verify the exact project and users we want to use
        $project = Project::findOrFail(1);
        $user1 = User::findOrFail(1);
        $user2 = User::findOrFail(2);

        // Motivation: Create a set of tasks that cover all attributes needed in ProjectTaskShow
        $tasks = [
            [
                'title' => 'Design Project Architecture',
                'description' => 'Create a comprehensive architectural design for the project, including system components and interactions.',
                'due_date' => now()->addDays(14),
                'priority' => 3,
                'status' => 'To Do',
                'project_id' => $project->id,
                'assignee_user_id' => $user1->id
            ],
            [
                'title' => 'Implement User Authentication',
                'description' => 'Develop robust user authentication system with role-based access control.',
                'due_date' => now()->addDays(10),
                'priority' => 4,
                'status' => 'In Progress',
                'project_id' => $project->id,
                'assignee_user_id' => $user2->id
            ],
            [
                'title' => 'Create Database Schema',
                'description' => 'Finalize and document the database schema for all project entities.',
                'due_date' => now()->addDays(7),
                'priority' => 2,
                'status' => 'Under Review',
                'project_id' => $project->id,
                'assignee_user_id' => $user1->id
            ],
            [
                'title' => 'Frontend Component Design',
                'description' => 'Design and prototype key frontend components for the application.',
                'due_date' => now()->addDays(21),
                'priority' => 1,
                'status' => 'Completed',
                'project_id' => $project->id,
                'assignee_user_id' => $user2->id
            ]
        ];

        // Motivation: Bulk insert tasks to minimize database operations
        Task::insert($tasks);
    }
}