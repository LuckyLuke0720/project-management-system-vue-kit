<?php
namespace Database\Factories;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        // Statuses for tasks
        $statuses = ['To Do', 'In Progress', 'Under Review', 'Completed'];

        // Get existing projects and users to ensure data integrity
        $project = Project::inRandomOrder()->first() ?? Project::factory()->create();
        $assignee = User::inRandomOrder()->first() ?? User::factory()->create();

        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'due_date' => $this->faker->dateTimeBetween('now', '+3 months'),
            'priority' => $this->faker->numberBetween(1, 5),
            'status' => $this->faker->randomElement($statuses),
            'project_id' => $project->id,
            'assignee_user_id' => $assignee->id
        ];
    }

    /**
     * Create a task with specific project and assignee.
     */
    public function forProject(Project $project): static
    {
        return $this->state(fn () => [
            'project_id' => $project->id
        ]);
    }

    /**
     * Create a task for a specific user.
     */
    public function assignedTo(User $user): static
    {
        return $this->state(fn () => [
            'assignee_user_id' => $user->id
        ]);
    }
}