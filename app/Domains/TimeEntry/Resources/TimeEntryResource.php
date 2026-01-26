<?php

namespace App\Domains\TimeEntry\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
class TimeEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => "{$this->user->first_name} {$this->user->surname}",
            'project' => $this->project->name,
            'task' => $this->task->name ?? null,
            'program' => $this->program->name ?? null,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'duration_seconds' => $this->duration_minutes,
            'is_manual' => $this->is_manual
        ];
    }
}
