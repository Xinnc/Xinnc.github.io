<?php

namespace App\Domains\Project\Model;

use App\Domains\Project\Enums\ProjectStatus;
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

    protected $casts = [
        'status' => ProjectStatus::class,
    ];

    public function manager(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
