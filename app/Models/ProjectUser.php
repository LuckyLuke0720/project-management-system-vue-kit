<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectUser extends Pivot
{
    public $incrementing = true;

     protected $table = 'project_user';
    
     protected $fillable = [
         'project_id',
         'user_id',
         'role',
     ];

    public function project(): BelongsTo{
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
