<?php

namespace App\Domains\TimeEntry\Model;

use App\Domains\Project\Model\Project;
use App\Domains\Shared\Enums\SortDirection;
use App\Domains\Shared\Model\Program;
use App\Domains\Task\Model\Task;
use App\Domains\TimeEntry\DataTransferObjects\FilterTimeEntryData;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_manual' => 'boolean',
    ];

    protected $with = [
        'user',
        'project',
        'task',
        'program',
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

    public function scopeFilter($query, FilterTimeEntryData $data)
    {
        return $query
            ->when($data->user_id, fn($q) => $q->where('user_id', $data->user_id))
            ->when($data->project_id, fn($q) => $q->where('project_id', $data->project_id))
            ->when($data->task_id, fn($q) => $q->where('task_id', $data->task_id))
            ->when($data->is_manual !== null, fn($q) => $q->where('is_manual', $data->is_manual))
            ->when($data->date_from !== null, fn($q) => $q->where('start_time', '>=', $data->date_from))
            ->when($data->date_to !== null, fn($q) => $q->where('start_time', '<=', $data->date_to));
    }

    public function scopeSortByTime($query, SortDirection $direction)
    {
        return $query->orderBy('start_time', $direction->value);
    }
}
