<?php

namespace App\Domains\Task\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'project' =>[
                'project_id' => $this->project->id,
                'project_name' => $this->project->name,
            ],
            'status' => $this->status
        ];
    }
}
