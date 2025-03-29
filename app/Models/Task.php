<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'order',
        'status',
        'project_id',
        'assignee_user_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'due_date' => 'date',
        'order' => 'integer',
        'status' => 'string'
    ];

    /**
     * Get the project that owns the task.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user assigned to the task.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            
            // set the order as max + 1 (patchwork, should probably set order as nullable)
            $maxOrder = self::where('project_id', $task->project_id)->max('order');
            $task->order = $maxOrder ? $maxOrder + 1 : 1;
        });
    }
}