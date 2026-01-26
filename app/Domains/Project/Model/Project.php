<?php

namespace App\Domains\Project\Model;

use App\Domains\Project\DataTransferObjects\FilterProjectData;
use App\Domains\Project\Enums\ProjectStatus;
use App\Domains\Shared\Enums\SortDirection;
use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'deadline',
        'manager_id',
        'status'
    ];

    protected $with = [
        'manager'
    ];

    protected $casts = [
        'status' => ProjectStatus::class,
    ];

    public function manager(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter($query, FilterProjectData $filter)
    {
        return $query
            ->when($filter->status, fn ($q) => $q->where('status', $filter->status))
            ->when($filter->manager_id, fn ($q) => $q->where('manager_id', $filter->manager_id))
            ->when($filter->deadline_from, fn ($q) => $q->whereDate('deadline', '>=', $filter->deadline_from))
            ->when($filter->deadline_to, fn ($q) => $q->whereDate('deadline', '<=', $filter->deadline_to))
            ->when($filter->search, fn ($q) => $q->where('name', 'ilike', "%{$filter->search}%"));
    }
    public function scopeSortByDeadline($query, SortDirection $direction)
    {
        return $query->orderBy('deadline', $direction->value);
    }
}
