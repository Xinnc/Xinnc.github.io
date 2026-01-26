<?php

namespace App\Domains\Task\Model;

use App\Domains\Project\Model\Project;
use App\Domains\Task\DataTransferObjects\FilterTaskData;
use App\Domains\Task\Enums\TaskStatus;
use App\Domains\TimeEntry\Model\TimeEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => TaskStatus::class,
    ];

    protected $with = [
        'project'
    ];

    public function project(): BelongsTo{
        return $this->belongsTo(Project::class);
    }
    public function timeEntries(): HasMany{
        return $this->hasMany(TimeEntry::class);
    }

    public function scopeFilter($query, FilterTaskData $filter)
    {
        return $query
            ->when($filter->status, fn ($q) => $q->where('status', $filter->status))
            ->when($filter->search, fn ($q) => $q->where('name', 'like', "%{$filter->search}%"));
    }
}
