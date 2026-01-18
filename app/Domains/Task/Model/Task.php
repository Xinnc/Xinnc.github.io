<?php

namespace App\Domains\Task\Model;

use App\Domains\Project\Model\Project;
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
}
