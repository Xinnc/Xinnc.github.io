<?php

namespace App\Domains\TimeEntry\Model;

use App\Domains\Project\Model\Project;
use App\Domains\Shared\Model\Program;
use App\Domains\Task\Model\Task;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TimeEntry extends Model
{
    protected $fillable = [
        'user_id',
        'project_id',
        'task_id',
        'program_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'is_manual',
    ];

    protected $casts = [
        'is_manual' => 'boolean',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function project(): BelongsTo {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function task(): BelongsTo {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function program(): BelongsTo {
        return $this->belongsTo(Program::class, 'program_id');
    }


}
